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
        Schema::create('person_projeto', function (Blueprint $table) {
            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('projeto_id');
            $table->primary(['person_id', 'projeto_id']);
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
            $table->foreign('projeto_id')->references('id')->on('projetos')->onDelete('cascade');
            $table->index(['person_id', 'projeto_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_projeto');
    }
};
