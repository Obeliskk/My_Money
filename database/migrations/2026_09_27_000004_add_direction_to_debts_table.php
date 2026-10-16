<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debts', function (Blueprint $table): void {
            $table->string('direction', 20)->default('to_pay')->after('amount');
            $table->index('direction');
        });
    }

    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table): void {
            $table->dropIndex(['direction']);
            $table->dropColumn('direction');
        });
    }
};
