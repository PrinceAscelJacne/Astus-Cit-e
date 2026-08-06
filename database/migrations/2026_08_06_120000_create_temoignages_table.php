<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temoignages', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('entreprise')->nullable();
            $table->string('service');
            $table->unsignedTinyInteger('note');
            $table->text('message');
            // Rien n'apparaît sur le site avant validation : le formulaire est
            // public, donc tout dépôt est considéré comme non fiable.
            $table->enum('statut', ['en_attente', 'publie', 'refuse'])->default('en_attente');
            $table->timestamps();

            $table->index(['statut', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temoignages');
    }
};
