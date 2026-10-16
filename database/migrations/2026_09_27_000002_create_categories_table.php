<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 80)->unique();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        $defaults = ['Alimentación', 'Vivienda', 'Transporte', 'Salud', 'Ocio', 'Servicios', 'Ahorro', 'Regalos', 'Otro'];
        foreach ($defaults as $name) {
            DB::table('categories')->insertOrIgnore([
                'name' => $name,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('movements')
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->each(function (string $name): void {
                DB::table('categories')->insertOrIgnore([
                    'name' => $name,
                    'is_default' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
