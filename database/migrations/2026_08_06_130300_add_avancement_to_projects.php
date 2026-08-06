<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Avancement d'un projet, par statuts simples.
 *
 * « status » existe déjà mais sert au classement actif / archivé : il ne dit
 * rien de l'état d'avancement du travail. D'où une colonne distincte.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('avancement', ['a_faire', 'en_cours', 'en_relecture', 'termine'])
                ->default('a_faire')
                ->after('status');
            $table->date('echeance')->nullable()->after('avancement');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['avancement', 'echeance']);
        });
    }
};
