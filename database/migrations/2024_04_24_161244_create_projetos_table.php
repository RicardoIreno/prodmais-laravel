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
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('instituicao')->nullable();
            $table->jsonb('integrantes')->nullable();
            $table->longText('description')->nullable();
            $table->string('projectYearEnd')->nullable();
            $table->string('projectYearStart')->nullable();
            $table->string('situacao')->nullable();
            $table->timestamps();

            $table->index([
                'name', 'instituicao', 'integrantes', 'description', 'projectYearStart', 'projectYearEnd', 'situacao'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};