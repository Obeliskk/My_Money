<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('period_balances', function (Blueprint $table): void {
            $table->id();
            $table->string('period_type', 10);
            $table->string('period_key', 10);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['period_type', 'period_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('period_balances');
    }
};
