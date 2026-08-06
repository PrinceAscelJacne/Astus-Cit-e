<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Journal d'activité.
 *
 * Répond au besoin de tracer : plusieurs personnes travaillent sur un même
 * projet, et en cas de problème il faut pouvoir remonter à qui a fait quoi.
 *
 * Le journal est alimenté par les événements Eloquent (voir le trait
 * App\Support\ConsigneLesActivites) et non par des appels dispersés dans les
 * contrôleurs : une action ajoutée plus tard est donc tracée sans qu'on ait à
 * y penser.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activites', function (Blueprint $table) {
            $table->id();

            // Qui. Nul si l'action vient d'une commande ou d'un traitement
            // automatique ; le nom est recopié pour rester lisible même après
            // suppression du compte.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('auteur_nom')->nullable();

            // Quoi : cree, modifie, supprime, archive, restaure, publie, refuse…
            $table->string('action');

            // Sur quoi.
            $table->string('sujet_type');
            $table->unsignedBigInteger('sujet_id');
            $table->string('sujet_libelle')->nullable();

            // Dans quel projet, quand la chose s'y rattache.
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();

            // Détail des champs touchés, pour les modifications.
            $table->json('details')->nullable();

            $table->timestamps();

            $table->index(['sujet_type', 'sujet_id']);
            $table->index(['project_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activites');
    }
};
