<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\MonthlyGoal;
use App\Models\Category;
use App\Models\Debt;
use App\Models\PeriodBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $allowedSections = ['weekly', 'categories', 'debts', 'goal', 'history'];
        $section = in_array($request->query('section'), $allowedSections, true)
            ? $request->query('section')
            : 'weekly';
        $summaryPeriod = in_array($request->query('period'), ['week', 'month'], true)
            ? $request->query('period')
            : 'week';
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('month'))
            ? $request->query('month')
            : now()->format('Y-m');
        $weekInput = (string) $request->query('week');
        $weekStart = preg_match('/^\d{4}-\d{2}-\d{2}$/', $weekInput)
            ? Carbon::createFromFormat('Y-m-d', $weekInput)->startOfDay()
            : now()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

        $movements = Movement::query()
            ->whereBetween('movement_date', ["{$month}-01", date('Y-m-t', strtotime("{$month}-01"))])
            ->latest('movement_date')
            ->latest('id')
            ->get();

        $income = (float) $movements->where('type', 'income')->sum('amount');
        $expenses = (float) $movements->where('type', 'expense')->sum('amount');
        $savings = max(0, $income - $expenses);
        $savingRate = $income > 0 ? round(($savings / $income) * 100) : 0;
        $goal = MonthlyGoal::firstOrCreate(['month' => $month], ['target_amount' => 0]);
        $goalAmount = (float) $goal->target_amount;
        $goalSavedAmount = (float) $goal->saved_amount;
        $goalProgress = $goalAmount > 0 ? min(100, round(($goalSavedAmount / $goalAmount) * 100)) : 0;
        $categories = Category::query()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();
        $debts = Debt::query()
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('due_date')
            ->latest('id')
            ->get();
        $pendingDebtTotal = (float) $debts->where('status', 'pending')->where('direction', 'to_pay')->sum('amount');
        $pendingReceivableTotal = (float) $debts->where('status', 'pending')->where('direction', 'to_collect')->sum('amount');

        $byCategory = $movements->where('type', 'expense')
            ->groupBy('category')
            ->map(fn ($items) => (float) $items->sum('amount'))
            ->sortDesc();
        $weeklyMovements = Movement::query()
            ->whereBetween('movement_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->latest('movement_date')
            ->latest('id')
            ->get();
        $weeklyIncome = (float) $weeklyMovements->where('type', 'income')->sum('amount');
        $weeklyExpenses = (float) $weeklyMovements->where('type', 'expense')->sum('amount');
        $weeklySavings = max(0, $weeklyIncome - $weeklyExpenses);
        $weeklySavingRate = $weeklyIncome > 0 ? round(($weeklySavings / $weeklyIncome) * 100) : 0;
        $weeklyByCategory = $weeklyMovements->where('type', 'expense')
            ->groupBy('category')
            ->map(fn ($items) => (float) $items->sum('amount'))
            ->sortDesc();
        $summaryMovements = $summaryPeriod === 'month' ? $movements : $weeklyMovements;
        $summaryIncome = $summaryPeriod === 'month' ? $income : $weeklyIncome;
        $summaryExpenses = $summaryPeriod === 'month' ? $expenses : $weeklyExpenses;
        // Disponible representa el saldo del mes seleccionado y se mantiene igual
        // al cambiar entre las vistas de semana y mes.
        $summarySavings = $savings;
        $summarySavingRate = $savingRate;
        $summaryBalancePeriodType = 'month';
        $summaryBalancePeriodKey = $month;
        $summaryBalance = PeriodBalance::query()
            ->where('period_type', $summaryBalancePeriodType)
            ->where('period_key', $summaryBalancePeriodKey)
            ->first();
        if (!$summaryBalance) {
            $summaryBalance = PeriodBalance::query()
                ->where('period_type', 'week')
                ->where('period_key', $weekStart->toDateString())
                ->first();
        }
        $summaryBalanceOverridden = $summaryBalance !== null;
        if ($summaryBalanceOverridden) {
            // Un registro antiguo no tenía línea base. La fijamos con el neto
            // actual para que el primer render conserve el monto elegido.
            if ($summaryBalance->baseline_net === null) {
                $summaryBalance->update(['baseline_net' => $income - $expenses]);
            }
            $summarySavings = max(0, (float) $summaryBalance->amount + ($income - $expenses - (float) $summaryBalance->baseline_net));
            $summarySavingRate = $income > 0 ? round(($summarySavings / $income) * 100) : 0;
        }
        $summaryByCategory = $summaryPeriod === 'month' ? $byCategory : $weeklyByCategory;
        $summaryStart = $summaryPeriod === 'month'
            ? Carbon::createFromFormat('Y-m-d', $month . '-01')
            : $weekStart;
        $summaryEnd = $summaryPeriod === 'month'
            ? $summaryStart->copy()->endOfMonth()
            : $weekEnd;
        $editingMovement = $request->filled('edit')
            ? Movement::find($request->integer('edit'))
            : null;
        $editingDebt = $request->filled('edit_debt')
            ? Debt::find($request->integer('edit_debt'))
            : null;
        $editingCategory = $request->filled('edit_category')
            ? Category::find($request->integer('edit_category'))
            : null;

        return view('dashboard', compact(
            'month', 'movements', 'income', 'expenses', 'savings', 'savingRate', 'byCategory',
            'goalAmount', 'goalSavedAmount', 'goalProgress', 'categories', 'debts', 'pendingDebtTotal', 'pendingReceivableTotal',
            'section', 'weekStart', 'weekEnd', 'weeklyMovements', 'weeklyIncome', 'weeklyExpenses',
            'weeklySavings', 'weeklySavingRate', 'weeklyByCategory', 'editingMovement', 'editingDebt',
            'editingCategory', 'summaryPeriod', 'summaryMovements', 'summaryIncome', 'summaryExpenses',
            'summarySavings', 'summarySavingRate', 'summaryByCategory', 'summaryStart', 'summaryEnd',
            'summaryBalancePeriodType', 'summaryBalancePeriodKey', 'summaryBalanceOverridden'
        ));
    }

    public function storeBalance(Request $request): RedirectResponse
    {
        $periodType = $request->input('period_type');
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'period_type' => ['required', 'in:week,month'],
            'period_key' => ['required', 'date_format:' . ($periodType === 'month' ? 'Y-m' : 'Y-m-d')],
            'month' => ['required', 'date_format:Y-m'],
            'week' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $baselineNet = $this->monthlyMovementNet($validated['month']);
        $periods = [
            ['period_type' => 'month', 'period_key' => $validated['month']],
            ['period_type' => 'week', 'period_key' => $validated['week'] ?? now()->startOfWeek(Carbon::MONDAY)->toDateString()],
        ];
        foreach ($periods as $period) {
            PeriodBalance::updateOrCreate($period, [
                'amount' => $validated['amount'],
                'baseline_net' => $baselineNet,
            ]);
        }

        return redirect()->route('dashboard', [
            'section' => 'weekly',
            'period' => $validated['period_type'],
            'month' => $validated['month'],
            'week' => $validated['week'] ?? now()->startOfWeek(Carbon::MONDAY)->toDateString(),
        ])->with('status', 'Disponible actualizado correctamente.');
    }

    public function destroyBalance(Request $request): RedirectResponse
    {
        $periodType = $request->input('period_type');
        $validated = $request->validate([
            'period_type' => ['required', 'in:week,month'],
            'period_key' => ['required', 'date_format:' . ($periodType === 'month' ? 'Y-m' : 'Y-m-d')],
            'month' => ['required', 'date_format:Y-m'],
            'week' => ['nullable', 'date_format:Y-m-d'],
        ]);

        PeriodBalance::query()
            ->where(function ($query) use ($validated): void {
                $query->where(function ($query) use ($validated): void {
                    $query->where('period_type', 'month')->where('period_key', $validated['month']);
                })->orWhere(function ($query) use ($validated): void {
                    $query->where('period_type', 'week')->where('period_key', $validated['week'] ?? now()->startOfWeek(Carbon::MONDAY)->toDateString());
                });
            })
            ->delete();

        return redirect()->route('dashboard', [
            'section' => 'weekly',
            'period' => $validated['period_type'],
            'month' => $validated['month'],
            'week' => $validated['week'] ?? now()->startOfWeek(Carbon::MONDAY)->toDateString(),
        ])->with('status', 'Disponible vuelve a calcularse con tus movimientos.');
    }

    private function monthlyMovementNet(string $month): float
    {
        $movements = Movement::query()
            ->whereBetween('movement_date', ["{$month}-01", date('Y-m-t', strtotime("{$month}-01"))])
            ->get();

        return (float) $movements->where('type', 'income')->sum('amount')
            - (float) $movements->where('type', 'expense')->sum('amount');
    }

    public function storeDebt(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'person' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'direction' => ['required', 'in:to_pay,to_collect'],
            'description' => ['nullable', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
        ]);

        Debt::create($validated + ['status' => 'pending']);

        return redirect()->route('dashboard', ['section' => 'debts'])
            ->with('status', 'Deuda registrada correctamente.');
    }

    public function updateDebt(Request $request, Debt $debt): RedirectResponse
    {
        $validated = $request->validate([
            'person' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'direction' => ['required', 'in:to_pay,to_collect'],
            'description' => ['nullable', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($debt, $validated): void {
            $debt->update($validated);

            if ($debt->status === 'paid') {
                $movementData = $this->settlementMovementData($debt);
                if ($debt->settlement_movement_id && Movement::whereKey($debt->settlement_movement_id)->exists()) {
                    unset($movementData['movement_date']);
                    Movement::whereKey($debt->settlement_movement_id)->update($movementData);
                } else {
                    $movement = Movement::create($movementData);
                    $debt->update(['settlement_movement_id' => $movement->id]);
                }
            }
        });

        return redirect()->route('dashboard', ['section' => 'debts'])
            ->with('status', 'Deuda actualizada correctamente.');
    }

    public function toggleDebt(Debt $debt): RedirectResponse
    {
        $markingPaid = $debt->status === 'pending';

        DB::transaction(function () use ($debt, $markingPaid): void {
            if ($markingPaid) {
                $movement = Movement::create($this->settlementMovementData($debt));
                $debt->update([
                    'status' => 'paid',
                    'settlement_movement_id' => $movement->id,
                ]);
            } else {
                if ($debt->settlement_movement_id) {
                    Movement::whereKey($debt->settlement_movement_id)->delete();
                }

                $debt->update([
                    'status' => 'pending',
                    'settlement_movement_id' => null,
                ]);
            }
        });

        return redirect()->route('dashboard', ['section' => 'debts'])->with('status', $markingPaid
            ? 'Deuda marcada como pagada.'
            : 'Deuda reabierta.');
    }

    private function settlementMovementData(Debt $debt): array
    {
        $isCollection = $debt->direction === 'to_collect';
        $description = ($isCollection ? 'Cobro: ' : 'Pago: ') . $debt->person;
        $note = trim((string) $debt->description);
        if ($note !== '') {
            $description .= ' — ' . $note;
        }

        return [
            'type' => $isCollection ? 'income' : 'expense',
            'amount' => $debt->amount,
            'category' => Category::where('name', 'Otro')->value('name')
                ?? Category::query()->orderBy('id')->value('name')
                ?? 'Otro',
            'description' => $description,
            'movement_date' => now()->toDateString(),
        ];
    }

    public function destroyDebt(Debt $debt): RedirectResponse
    {
        $debt->delete();

        return redirect()->route('dashboard', ['section' => 'debts'])
            ->with('status', 'Deuda eliminada.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $name = trim($validated['name']);
        $duplicate = Category::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['name' => 'Esa etiqueta ya existe.'])->withInput();
        }

        Category::create(['name' => $name, 'is_default' => false]);

        return redirect()->route('dashboard', ['section' => 'categories', 'month' => $validated['month'] ?? now()->format('Y-m')])
            ->with('status', 'Etiqueta creada correctamente.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        if ($category->is_default) {
            return back()->with('error', 'Las etiquetas predeterminadas no se pueden editar.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
        ]);
        $name = trim($validated['name']);
        $duplicate = Category::query()
            ->where('id', '!=', $category->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['name' => 'Esa etiqueta ya existe.'])->withInput();
        }

        $oldName = $category->name;
        DB::transaction(function () use ($category, $name, $oldName): void {
            $category->update(['name' => $name]);
            Movement::where('category', $oldName)->update(['category' => $name]);
        });

        return redirect()->route('dashboard', ['section' => 'categories'])
            ->with('status', 'Etiqueta actualizada correctamente.');
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        if ($category->is_default) {
            return back()->with('error', 'Las etiquetas predeterminadas no se pueden eliminar.');
        }

        if (Movement::where('category', $category->name)->exists()) {
            return back()->with('error', 'No puedes eliminar una etiqueta que ya tiene movimientos asociados.');
        }

        $category->delete();

        return redirect()->route('dashboard', ['section' => 'categories'])
            ->with('status', 'Etiqueta eliminada.');
    }

    public function storeGoal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'saved_amount' => ['required', 'numeric', 'min:0'],
        ]);

        MonthlyGoal::updateOrCreate(
            ['month' => $validated['month']],
            [
                'target_amount' => $validated['target_amount'],
                'saved_amount' => $validated['saved_amount'],
            ]
        );

        return redirect()->route('dashboard', ['section' => 'goal', 'month' => $validated['month']])
            ->with('status', 'Meta de ahorro actualizada.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category' => ['required', 'string', 'max:80', Rule::exists('categories', 'name')],
            'description' => ['nullable', 'string', 'max:255'],
            'movement_date' => ['required', 'date'],
        ]);

        Movement::create($validated);

        return redirect()->route('dashboard', ['section' => 'categories', 'month' => substr($validated['movement_date'], 0, 7)])
            ->with('status', 'Movimiento guardado correctamente.');
    }

    public function update(Request $request, Movement $movement): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category' => ['required', 'string', 'max:80', Rule::exists('categories', 'name')],
            'description' => ['nullable', 'string', 'max:255'],
            'movement_date' => ['required', 'date'],
        ]);

        $movement->update($validated);

        return redirect()->route('dashboard', [
            'section' => 'categories',
            'month' => substr($validated['movement_date'], 0, 7),
        ])->with('status', 'Movimiento actualizado correctamente.');
    }

    public function destroy(Movement $movement): RedirectResponse
    {
        $month = $movement->movement_date->format('Y-m');
        $movement->delete();

        return redirect()->route('dashboard', ['section' => 'history', 'month' => $month])
            ->with('status', 'Movimiento eliminado.');
    }
}
