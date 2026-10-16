<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debts', function (Blueprint $table): void {
            $table->unsignedBigInteger('settlement_movement_id')->nullable()->index();
        });

        $category = DB::table('categories')->where('name', 'Otro')->value('name')
            ?? DB::table('categories')->orderBy('id')->value('name')
            ?? 'Otro';

        foreach (DB::table('debts')->where('status', 'paid')->get() as $debt) {
            $isCollection = $debt->direction === 'to_collect';
            $label = $isCollection ? 'Cobro' : 'Pago';
            $description = $label . ': ' . $debt->person;
            if (!empty($debt->description)) {
                $description .= ' — ' . $debt->description;
            }

            $movementId = DB::table('movements')->insertGetId([
                'type' => $isCollection ? 'income' : 'expense',
                'amount' => $debt->amount,
                'category' => $category,
                'description' => $description,
                'movement_date' => $debt->due_date ?: date('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('debts')->where('id', $debt->id)->update([
                'settlement_movement_id' => $movementId,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table): void {
            $table->dropColumn('settlement_movement_id');
        });
    }
};
