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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->jsonb('atuacao')->nullable();
            $table->string('curso_nome')->nullable();
            $table->string('departamento')->nullable();
            $table->string('email')->nullable();
            $table->jsonb('formacao')->nullable();
            $table->string('genero')->nullable();
            $table->jsonb('idiomas')->nullable();
            $table->jsonb('instituicao')->nullable();
            $table->string('lattesDataAtualizacao');
            $table->string('lattesID10')->nullable();
            $table->string('nacionalidade')->nullable();
            $table->string('name');
            $table->longText('nomeCitacoesBibliograficas');
            $table->string('orcid');
            $table->jsonb('orientacoesConcluidas')->nullable();
            $table->jsonb('orientacoesEmAndamento')->nullable();
            $table->jsonb('ppg_nome')->nullable();
            $table->longText('resumoCVpt');
            $table->longText('resumoCVen');
            $table->jsonb('tipvin')->nullable();
            $table->jsonb('unidade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
