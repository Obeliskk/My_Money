<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('period_balances', function (Blueprint $table): void {
            $table->decimal('baseline_net', 12, 2)->nullable()->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('period_balances', function (Blueprint $table): void {
            $table->dropColumn('baseline_net');
        });
    }
};
