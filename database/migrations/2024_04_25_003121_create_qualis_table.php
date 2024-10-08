<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qualis', function (Blueprint $table) {
            $table->id();
            $table->string('issn');
            $table->string('titulo');
            $table->string('estrato');
            $table->string('area');
            $table->string('ano');
            $table->timestamps();

            $table->index([
                'issn', 'titulo'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualis');
    }
};