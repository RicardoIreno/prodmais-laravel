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
            $table->string('lattesID16')->nullable();
            $table->string('nacionalidade')->nullable();
            $table->longText('name');
            $table->longText('nomeCitacoesBibliograficas')->nullable();
            $table->string('orcid')->nullable();
            $table->jsonb('orientacoesConcluidas')->nullable();
            $table->jsonb('orientacoesEmAndamento')->nullable();
            $table->jsonb('ppg_nome')->nullable();
            $table->longText('resumoCVpt')->nullable();
            $table->longText('resumoCVen')->nullable();
            $table->string('tipvin')->nullable();
            $table->string('unidade')->nullable();
            $table->timestamps();

            $table->index([
                'curso_nome', 'departamento', 'genero', 'instituicao', 'lattesID10', 'lattesID16', 'name', 'nomeCitacoesBibliograficas', 'orcid', 'ppg_nome', 'tipvin', 'unidade'
            ]);
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