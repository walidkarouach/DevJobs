<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidatures', function (Blueprint $table) {

            $table->id();

            $table->foreignId('candidat_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('offre_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('statut', [
                'en_attente',
                'acceptée',
                'refusée'
            ])->default('en_attente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};
