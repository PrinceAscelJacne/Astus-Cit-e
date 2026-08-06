<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ce qui a été dit, et ce qui reste à faire.
 *
 * L'équipe travaille à distance : sans trace écrite rattachée au projet, les
 * consignes se perdent dans les conversations privées et personne ne sait où
 * en est le travail.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Fil de discussion du projet.
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('auteur_nom')->nullable();
            $table->text('corps');
            // Un message peut porter sur un fichier précis (« ce logo est trop
            // sombre ») plutôt que sur le projet en général.
            $table->foreignId('file_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'created_at']);
        });

        // Ce qui reste à faire.
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->enum('statut', ['a_faire', 'en_cours', 'fait'])->default('a_faire');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cree_par')->nullable()->constrained('users')->nullOnDelete();
            $table->date('echeance')->nullable();
            $table->timestamp('faite_le')->nullable();
            $table->foreignId('faite_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'statut']);
            $table->index(['assignee_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taches');
        Schema::dropIfExists('discussions');
    }
};
