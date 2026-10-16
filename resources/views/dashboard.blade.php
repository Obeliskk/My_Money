@php
    $sectionTitles = [
        'weekly' => 'Resumen semanal',
        'categories' => 'Nuevo movimiento',
        'debts' => 'Deudas y cobros',
        'goal' => 'Fijar meta',
        'history' => 'Historial de gastos del mes',
    ];
    $sectionTitle = $sectionTitles[$section] ?? $sectionTitles['weekly'];
    $movementDateIso = (string) old('movement_date', $editingMovement?->movement_date?->format('Y-m-d') ?? now()->format('Y-m-d'));
    $movementDateDisplay = preg_match('/^\d{4}-\d{2}-\d{2}$/', $movementDateIso)
        ? \Carbon\Carbon::createFromFormat('Y-m-d', $movementDateIso)->format('d/m/Y')
        : $movementDateIso;
    $debtDueDateIso = (string) old('due_date', $editingDebt?->due_date?->format('Y-m-d') ?? '');
    $debtDueDateDisplay = preg_match('/^\d{4}-\d{2}-\d{2}$/', $debtDueDateIso)
        ? \Carbon\Carbon::createFromFormat('Y-m-d', $debtDueDateIso)->format('d/m/Y')
        : $debtDueDateIso;
@endphp

<x-layout :title="$sectionTitle . ' | GastosLocal'">
    <section class="intro">
        <div>
            <p class="eyebrow">CONTROL PERSONAL</p>
            <h2>{{ $sectionTitle }}</h2>
            <p class="muted">Organiza tus finanzas con información clara y accionable.</p>
        </div>
        @if ($section === 'history' || $section === 'goal')
            <form method="GET" class="month-picker">
                <input type="hidden" name="section" value="{{ $section }}">
                <label for="month">Mes</label>
                @include('components.month-picker', ['id' => 'month', 'month' => $month])
            </form>
        @elseif ($section === 'weekly' && $summaryPeriod === 'week')
            <div class="intro-actions">
                <section class="summary-switch" aria-label="Periodo de KPI">
                    <a class="{{ $summaryPeriod === 'week' ? 'active' : '' }}" href="{{ route('dashboard', ['section' => 'weekly', 'period' => 'week', 'week' => $weekStart->format('Y-m-d'), 'month' => $month]) }}">Semana</a>
                    <a class="{{ $summaryPeriod === 'month' ? 'active' : '' }}" href="{{ route('dashboard', ['section' => 'weekly', 'period' => 'month', 'month' => $month, 'week' => $weekStart->format('Y-m-d')]) }}">Mes</a>
                </section>
                <form method="GET" class="month-picker">
                    <input type="hidden" name="section" value="weekly">
                    <input type="hidden" name="period" value="week">
                    <label for="week">Semana desde</label>
                    <span class="date-control"><input id="week" class="date-display" type="text" inputmode="numeric" data-date-display data-submit-form placeholder="dd/mm/aaaa" value="{{ $weekStart->format('d/m/Y') }}" aria-label="Semana desde, día, mes y año"><button type="button" class="date-picker-trigger" data-date-trigger aria-label="Abrir calendario"><span class="calendar-icon" aria-hidden="true"></span></button><input class="date-native-picker" type="date" data-date-native value="{{ $weekStart->format('Y-m-d') }}" tabindex="-1" aria-hidden="true"><input type="hidden" name="week" data-date-value value="{{ $weekStart->format('Y-m-d') }}"></span>
                </form>
            </div>
        @elseif ($section === 'weekly')
            <div class="intro-actions">
                <section class="summary-switch" aria-label="Periodo de KPI">
                    <a class="{{ $summaryPeriod === 'week' ? 'active' : '' }}" href="{{ route('dashboard', ['section' => 'weekly', 'period' => 'week', 'week' => $weekStart->format('Y-m-d'), 'month' => $month]) }}">Semana</a>
                    <a class="{{ $summaryPeriod === 'month' ? 'active' : '' }}" href="{{ route('dashboard', ['section' => 'weekly', 'period' => 'month', 'month' => $month, 'week' => $weekStart->format('Y-m-d')]) }}">Mes</a>
                </section>
                <form method="GET" class="month-picker">
                    <input type="hidden" name="section" value="weekly">
                    <input type="hidden" name="period" value="month">
                    <label for="summary-month">Mes</label>
                    @include('components.month-picker', ['id' => 'summary-month', 'month' => $month])
                </form>
            </div>
        @endif
    </section>

    <nav class="module-nav" aria-label="Módulos de GastosLocal">
        @foreach ($sectionTitles as $key => $title)
            <a class="{{ $section === $key ? 'active' : '' }}" href="{{ route('dashboard', ['section' => $key, 'month' => $month, 'week' => $weekStart->format('Y-m-d')]) }}">{{ $title }}</a>
        @endforeach
    </nav>

    @if ($section === 'weekly')
        <section class="kpi-grid" aria-label="Indicadores del periodo seleccionado">
            <article class="kpi-card"><span>Ingresos del {{ $summaryPeriod === 'week' ? 'periodo' : 'mes' }}</span><strong>&dollar;{{ number_format($summaryIncome, 0, ',', '.') }}</strong><small>{{ $summaryStart->format('d/m') }} al {{ $summaryEnd->format('d/m/Y') }}</small></article>
            <article class="kpi-card"><span>Gastos del {{ $summaryPeriod === 'week' ? 'periodo' : 'mes' }}</span><strong>&dollar;{{ number_format($summaryExpenses, 0, ',', '.') }}</strong><small>Consumo registrado</small></article>
            <article class="kpi-card accent"><div class="kpi-card-heading"><span>Disponible</span><details class="balance-editor"><summary class="kpi-edit">Editar</summary><div class="balance-popover"><form method="POST" action="{{ route('balance.store') }}">@csrf<input type="hidden" name="period_type" value="{{ $summaryBalancePeriodType }}"><input type="hidden" name="period_key" value="{{ $summaryBalancePeriodKey }}"><input type="hidden" name="month" value="{{ $month }}"><input type="hidden" name="week" value="{{ $weekStart->format('Y-m-d') }}"><label>Saldo disponible<input name="amount" type="number" min="0" step="0.01" value="{{ number_format((float) ($summaryBalance?->amount ?? $summarySavings), 2, '.', '') }}" required></label><p class="balance-help">Este valor se actualizará con tus nuevos movimientos.</p><div class="form-actions"><button class="button primary" type="submit">Guardar</button></div></form>@if ($summaryBalanceOverridden)<form method="POST" action="{{ route('balance.destroy') }}" class="balance-reset-form">@csrf @method('DELETE')<input type="hidden" name="period_type" value="{{ $summaryBalancePeriodType }}"><input type="hidden" name="period_key" value="{{ $summaryBalancePeriodKey }}"><input type="hidden" name="month" value="{{ $month }}"><input type="hidden" name="week" value="{{ $weekStart->format('Y-m-d') }}"><button class="balance-reset" type="submit">Restablecer cálculo</button></form>@endif</div></details></div><strong>&dollar;{{ number_format($summarySavings, 0, ',', '.') }}</strong><small>{{ $summarySavingRate }}% de tus ingresos del mes{{ $summaryBalanceOverridden ? ' · Base ajustada' : '' }}</small></article>
            <article class="kpi-card"><span>Movimientos</span><strong>{{ $summaryMovements->count() }}</strong><small>En el periodo seleccionado</small></article>
        </section>

        <section class="content-grid">
            <article class="panel">
                <div class="panel-heading"><div><p class="eyebrow">DISTRIBUCIÓN</p><h3>Gastos por etiqueta</h3></div></div>
                @forelse ($summaryByCategory as $category => $amount)
                    @php($percentage = $summaryExpenses > 0 ? ($amount / $summaryExpenses) * 100 : 0)
                    <div class="category-row"><div class="category-label"><span>{{ $category }}</span><strong>&dollar;{{ number_format($amount, 0, ',', '.') }}</strong></div><div class="bar"><i style="width: {{ $percentage }}%"></i></div></div>
                @empty
                    <p class="empty">Todavía no hay gastos en el periodo seleccionado.</p>
                @endforelse
            </article>
            <article class="panel">
                <div class="panel-heading"><div><p class="eyebrow">MOVIMIENTOS</p><h3>Actividad reciente</h3></div></div>
                @if ($summaryMovements->isEmpty())
                    <p class="empty">Agrega movimientos desde el módulo Nuevo movimiento.</p>
                @else
                    <div class="table-wrap"><table><thead><tr><th>Fecha</th><th>Detalle</th><th class="right">Monto</th></tr></thead><tbody>
                    @foreach ($summaryMovements->take(8) as $movement)
                        <tr><td>{{ $movement->movement_date->format('d/m') }}</td><td>{{ $movement->description ?: $movement->category }}</td><td class="right amount {{ $movement->type }}">{{ $movement->type === 'income' ? '+' : '-' }}&dollar;{{ number_format((float) $movement->amount, 0, ',', '.') }}</td></tr>
                    @endforeach
                    </tbody></table></div>
                @endif
            </article>
        </section>
    @elseif ($section === 'categories')
        <section class="panel module-panel">
            <div class="panel-heading"><div><p class="eyebrow">NUEVO MOVIMIENTO</p><h3>{{ $editingMovement ? 'Editar movimiento' : 'Nuevo movimiento' }}</h3></div></div>
            <form id="movement-form" method="POST" action="{{ $editingMovement ? route('movements.update', $editingMovement) : route('movements.store') }}" class="movement-form">
                @csrf
                @if ($editingMovement) @method('PUT') @endif
                <div class="field-row"><label>Tipo<select name="type" required><option value="expense" @selected(old('type', $editingMovement?->type ?? 'expense') === 'expense')>Gasto</option><option value="income" @selected(old('type', $editingMovement?->type) === 'income')>Ingreso</option></select></label><label>Monto<input name="amount" type="number" min="0.01" step="0.01" placeholder="0" value="{{ old('amount', $editingMovement?->amount) }}" required></label></div>
                <div class="field-row"><label>Etiqueta<select name="category" required>@foreach ($categories as $category)<option value="{{ $category->name }}" @selected(old('category', $editingMovement?->category) === $category->name)>{{ $category->name }}</option>@endforeach</select></label><label>Fecha <span class="optional">dd/mm/aaaa</span><span class="date-control"><input class="date-display" type="text" inputmode="numeric" data-date-display placeholder="dd/mm/aaaa" value="{{ $movementDateDisplay }}" required aria-label="Fecha, día, mes y año"><button type="button" class="date-picker-trigger" data-date-trigger aria-label="Abrir calendario"><span class="calendar-icon" aria-hidden="true"></span></button><input class="date-native-picker" type="date" data-date-native value="{{ $movementDateIso }}" tabindex="-1" aria-hidden="true"><input type="hidden" name="movement_date" data-date-value value="{{ $movementDateIso }}"></span></label></div>
                <label>Descripción <span class="optional">opcional</span><input name="description" type="text" maxlength="255" placeholder="Ej. supermercado, sueldo..." value="{{ old('description', $editingMovement?->description) }}"></label>
                <div class="form-actions"><button class="button primary" type="submit">{{ $editingMovement ? 'Guardar cambios' : 'Guardar movimiento' }}</button>@if ($editingMovement)<a class="button secondary" href="{{ route('dashboard', ['section' => 'categories', 'month' => $month]) }}">Cancelar</a>@endif</div>
            </form>
        </section>

        <section class="panel module-panel">
            <div class="panel-heading"><div><p class="eyebrow">ETIQUETAS</p><h3>{{ $editingCategory ? 'Editar etiqueta' : 'Administrar etiquetas' }}</h3></div></div>
            <p class="muted tags-help">Regalos ya está disponible. Crea etiquetas propias para clasificar tus movimientos.</p>
            <form method="POST" action="{{ $editingCategory ? route('categories.update', $editingCategory) : route('categories.store') }}" class="tag-form">
                @csrf
                @if ($editingCategory) @method('PUT') @endif
                <input type="hidden" name="month" value="{{ $month }}">
                <input name="name" type="text" maxlength="80" placeholder="Ej. Mascotas, Viajes..." value="{{ old('name', $editingCategory?->name) }}" required>
                <button class="button primary" type="submit">{{ $editingCategory ? 'Guardar cambios' : 'Crear etiqueta' }}</button>
                @if ($editingCategory)<a class="button secondary" href="{{ route('dashboard', ['section' => 'categories']) }}">Cancelar</a>@endif
            </form>
            <div class="tag-list">
                @foreach ($categories as $category)
                    <div class="category-chip">{{ $category->name }} @if (!$category->is_default)<a href="{{ route('dashboard', ['section' => 'categories', 'edit_category' => $category->id]) }}" title="Editar etiqueta">✎</a><form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar esta etiqueta?')">@csrf @method('DELETE')<button type="submit" title="Eliminar etiqueta">×</button></form>@endif</div>
                @endforeach
            </div>
        </section>
    @elseif ($section === 'debts')
        <section class="panel module-panel">
            <div class="panel-heading"><div><p class="eyebrow">DEUDAS PERSONALES</p><h3>{{ $editingDebt ? 'Editar deuda o cobro' : 'Deudas y cobros' }}</h3></div><div class="debt-totals"><strong class="debt-total">Debo: &dollar;{{ number_format($pendingDebtTotal, 0, ',', '.') }}</strong><strong class="receivable-total">Me deben: &dollar;{{ number_format($pendingReceivableTotal, 0, ',', '.') }}</strong></div></div>
            <form method="POST" action="{{ $editingDebt ? route('debts.update', $editingDebt) : route('debts.store') }}" class="debt-form">
                @csrf
                @if ($editingDebt) @method('PUT') @endif
                <div class="field-row"><label>Tipo<select name="direction" required><option value="to_pay" @selected(old('direction', $editingDebt?->direction ?? 'to_pay') === 'to_pay')>Yo debo</option><option value="to_collect" @selected(old('direction', $editingDebt?->direction) === 'to_collect')>Me deben</option></select></label><label>Persona<input name="person" type="text" maxlength="120" placeholder="Ej. Camila" value="{{ old('person', $editingDebt?->person) }}" required></label></div>
                <div class="field-row"><label>Monto<input name="amount" type="number" min="0.01" step="0.01" placeholder="0" value="{{ old('amount', $editingDebt?->amount) }}" required></label><label>Fecha de pago <span class="optional">dd/mm/aaaa · opcional</span><span class="date-control"><input class="date-display" type="text" inputmode="numeric" data-date-display placeholder="dd/mm/aaaa" value="{{ $debtDueDateDisplay }}" aria-label="Fecha de pago, día, mes y año"><button type="button" class="date-picker-trigger" data-date-trigger aria-label="Abrir calendario"><span class="calendar-icon" aria-hidden="true"></span></button><input class="date-native-picker" type="date" data-date-native value="{{ $debtDueDateIso }}" tabindex="-1" aria-hidden="true"><input type="hidden" name="due_date" data-date-value value="{{ $debtDueDateIso }}"></span></label></div>
                <label>Nota <span class="optional">opcional</span><input name="description" type="text" maxlength="255" placeholder="Ej. cena, préstamo..." value="{{ old('description', $editingDebt?->description) }}"></label>
                <div class="form-actions"><button class="button primary" type="submit">{{ $editingDebt ? 'Guardar cambios' : 'Agregar registro' }}</button>@if ($editingDebt)<a class="button secondary" href="{{ route('dashboard', ['section' => 'debts']) }}">Cancelar</a>@endif</div>
            </form>
            @if ($debts->isEmpty())
                <p class="empty">No tienes deudas ni cobros registrados.</p>
            @else
                <div class="table-wrap"><table><thead><tr><th>Tipo</th><th>Persona</th><th>Nota</th><th>Vence</th><th>Estado</th><th class="right">Monto</th><th></th></tr></thead><tbody>
                @foreach ($debts as $debt)
                    <tr class="{{ $debt->status === 'paid' ? 'debt-paid' : '' }}"><td><span class="debt-direction {{ $debt->direction }}">{{ $debt->direction === 'to_pay' ? 'Yo debo' : 'Me deben' }}</span></td><td><strong>{{ $debt->person }}</strong></td><td>{{ $debt->description ?: 'Sin nota' }}</td><td>{{ $debt->due_date?->format('d/m/Y') ?: 'Sin fecha' }}</td><td><span class="debt-status {{ $debt->status }}">{{ $debt->status === 'paid' ? 'Pagada' : 'Pendiente' }}</span></td><td class="right amount {{ $debt->direction === 'to_collect' ? 'income' : 'expense' }}">&dollar;{{ number_format((float) $debt->amount, 0, ',', '.') }}</td><td class="right actions"><a class="small-action" href="{{ route('dashboard', ['section' => 'debts', 'edit_debt' => $debt->id]) }}">Editar</a><form method="POST" action="{{ route('debts.toggle', $debt) }}">@csrf @method('PATCH')<button class="small-action" type="submit">{{ $debt->status === 'paid' ? 'Reabrir' : ($debt->direction === 'to_collect' ? 'Cobrado' : 'Pagar') }}</button></form><form method="POST" action="{{ route('debts.destroy', $debt) }}" onsubmit="return confirm('¿Eliminar este registro?')">@csrf @method('DELETE')<button class="delete" title="Eliminar" type="submit">×</button></form></td></tr>
                @endforeach
                </tbody></table></div>
            @endif
        </section>
    @elseif ($section === 'goal')
        <section class="panel module-panel">
            <div class="panel-heading"><div><p class="eyebrow">PLANIFICACIÓN</p><h3>Meta de ahorro para {{ $month }}</h3></div><strong class="goal-percent">{{ $goalProgress }}%</strong></div>
            <div class="goal-track"><i style="width: {{ $goalProgress }}%"></i></div>
            <p class="muted goal-copy">Has logrado &dollar;{{ number_format($goalSavedAmount, 0, ',', '.') }} de una meta de &dollar;{{ number_format($goalAmount, 0, ',', '.') }}.</p>
            <form method="POST" action="{{ route('goal.store') }}" class="goal-form">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <div class="field-row"><label>Monto objetivo<input name="target_amount" type="number" min="0" step="0.01" value="{{ $goalAmount }}" required></label><label>Ahorro logrado<input name="saved_amount" type="number" min="0" step="0.01" value="{{ $goalSavedAmount }}" required></label></div>
                <p class="muted goal-help">El ahorro logrado se registra por separado y no incluye automáticamente tu saldo disponible.</p>
                <div class="form-actions"><button class="button primary" type="submit">Guardar meta</button></div>
            </form>
        </section>
    @else
        <section class="kpi-grid history-kpis" aria-label="Indicadores del mes">
            <article class="kpi-card"><span>Ingresos</span><strong>&dollar;{{ number_format($income, 0, ',', '.') }}</strong><small>Total del mes</small></article>
            <article class="kpi-card"><span>Gastos</span><strong>&dollar;{{ number_format($expenses, 0, ',', '.') }}</strong><small>Consumo del mes</small></article>
            <article class="kpi-card accent"><span>Disponible</span><strong>&dollar;{{ number_format($savings, 0, ',', '.') }}</strong><small>{{ $savingRate }}% de tus ingresos</small></article>
            <article class="kpi-card"><span>Movimientos</span><strong>{{ $movements->count() }}</strong><small>{{ $month }}</small></article>
        </section>

        <section class="panel module-panel">
            <div class="panel-heading"><div><p class="eyebrow">HISTORIAL</p><h3>Gastos e ingresos de {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->locale('es')->translatedFormat('F Y') }}</h3></div></div>
            @if ($movements->isEmpty())
                <p class="empty">Agrega tu primer movimiento para comenzar a ver tu evolución.</p>
            @else
                <div class="table-wrap"><table><thead><tr><th>Fecha</th><th>Descripción</th><th>Etiqueta</th><th>Tipo</th><th class="right">Monto</th><th></th></tr></thead><tbody>
                @foreach ($movements as $movement)
                    <tr><td>{{ $movement->movement_date->format('d/m/Y') }}</td><td>{{ $movement->description ?: 'Sin descripción' }}</td><td>{{ $movement->category }}</td><td><span class="tag {{ $movement->type }}">{{ $movement->type === 'income' ? 'Ingreso' : 'Gasto' }}</span></td><td class="right amount {{ $movement->type }}">{{ $movement->type === 'income' ? '+' : '-' }}&dollar;{{ number_format((float) $movement->amount, 0, ',', '.') }}</td><td class="right"><a class="small-action" href="{{ route('dashboard', ['section' => 'categories', 'month' => $month, 'edit' => $movement->id]) }}#movement-form">Editar</a><form class="inline-form" method="POST" action="{{ route('movements.destroy', $movement) }}" onsubmit="return confirm('¿Eliminar este movimiento?')">@csrf @method('DELETE')<button class="delete" title="Eliminar" type="submit">×</button></form></td></tr>
                @endforeach
                </tbody></table></div>
            @endif
        </section>
    @endif
</x-layout>
