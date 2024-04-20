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
    { {
            Schema::create('person_work', function (Blueprint $table) {
                $table->unsignedBigInteger('person_id');
                $table->unsignedBigInteger('work_id');
                $table->primary(['person_id', 'work_id']);
                $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
                $table->foreign('work_id')->references('id')->on('works')->onDelete('cascade');
                $table->index(['person_id', 'work_id']);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_work');
    }
};
