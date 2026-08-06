<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Droits d'accès configurables par l'administrateur.
 *
 * Deux niveaux, comme demandé :
 *   1. Le rôle porte un jeu de droits (role_permission). L'administrateur crée
 *      ses propres rôles — « Graphiste », « Chargé de clientèle »…
 *   2. Une dérogation peut être posée sur une personne précise
 *      (permission_user), pour accorder ou retirer un droit sans toucher au
 *      rôle. La dérogation l'emporte toujours sur le rôle.
 *
 * Le catalogue des droits vit dans App\Support\Droits : ce sont les
 * fonctionnalités de l'application, elles ne se créent pas depuis l'interface.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            // Les trois rôles d'origine ne doivent pas pouvoir être supprimés :
            // le code s'appuie dessus pour les réglages par défaut.
            $table->boolean('systeme')->default(false)->after('description');
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->string('droit');
            $table->timestamps();

            $table->unique(['role_id', 'droit']);
        });

        Schema::create('permission_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('droit');
            // true = accordé malgré le rôle, false = retiré malgré le rôle.
            $table->boolean('accorde');
            $table->timestamps();

            $table->unique(['user_id', 'droit']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_user');
        Schema::dropIfExists('role_permission');

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['description', 'systeme']);
        });
    }
};
