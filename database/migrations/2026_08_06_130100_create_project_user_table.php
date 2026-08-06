<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Équipe d'un projet.
 *
 * projects.user_id ne désignait qu'un créateur : plusieurs personnes ne
 * pouvaient pas travailler sur le même projet. Cette table de liaison porte
 * l'équipe, avec un responsable et des contributeurs.
 *
 * La colonne projects.user_id est conservée : elle reste la trace du créateur.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role_projet', ['responsable', 'contributeur'])->default('contributeur');
            // Qui a fait entrer cette personne dans l'équipe, et quand.
            $table->foreignId('ajoute_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
