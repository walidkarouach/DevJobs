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
        Schema::create('competence_offre', function (Blueprint $table) {

            $table->foreignId('offre_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('competence_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->primary(['offre_id','competence_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competence_offre');
    }
};
