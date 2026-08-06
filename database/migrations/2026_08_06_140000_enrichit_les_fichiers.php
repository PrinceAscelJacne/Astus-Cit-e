<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Les fichiers deviennent les livrables d'un projet.
 *
 * Astuscité est une agence de communication et d'événementiel : ce qui circule
 * dans un projet, ce sont des photos, des vidéos, des visuels, des logos, des
 * textes. Il fallait donc décrire ces fichiers, et surtout pouvoir restreindre
 * qui les voit — certains éléments n'ont pas à être visibles de toute
 * l'entreprise.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table) {
            // Qui peut voir ce fichier. Le déposant choisit ; la direction
            // peut imposer une valeur, auquel cas on retient qui l'a fait.
            $table->enum('visibilite', ['equipe', 'prive', 'direction', 'entreprise'])
                ->default('equipe')
                ->after('status');
            $table->foreignId('visibilite_imposee_par')->nullable()
                ->after('visibilite')
                ->constrained('users')->nullOnDelete();

            // Métadonnées utiles pour lister et prévisualiser sans ouvrir.
            $table->string('mime')->nullable()->after('path');
            $table->unsignedBigInteger('taille')->nullable()->after('mime');
            $table->string('description')->nullable()->after('taille');

            // Version : un visuel est repris plusieurs fois avant validation.
            $table->unsignedInteger('version')->default(1)->after('description');
            $table->foreignId('remplace_id')->nullable()->after('version')
                ->constrained('files')->nullOnDelete();

            $table->index(['project_id', 'visibilite']);
        });
    }

    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropConstrainedForeignId('visibilite_imposee_par');
            $table->dropConstrainedForeignId('remplace_id');
            $table->dropColumn(['visibilite', 'mime', 'taille', 'description', 'version']);
        });
    }
};
