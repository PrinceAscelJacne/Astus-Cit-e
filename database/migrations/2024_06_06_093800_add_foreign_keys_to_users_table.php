<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pose les clés étrangères de « users » vers « departments » et « roles ».
 *
 * Elles étaient déclarées dans la migration de création de « users »
 * (2014_10_12_000000), c'est-à-dire avant l'existence des tables référencées :
 * une installation neuve échouait sur « Foreign key constraint is incorrectly
 * formed ». Cette migration s'exécute après la création des deux tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! $this->contrainteExiste('users_department_id_foreign')) {
                $table->foreign('department_id')->references('id')->on('departments');
            }

            if (! $this->contrainteExiste('users_role_id_foreign')) {
                $table->foreign('role_id')->references('id')->on('roles');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if ($this->contrainteExiste('users_department_id_foreign')) {
                $table->dropForeign('users_department_id_foreign');
            }

            if ($this->contrainteExiste('users_role_id_foreign')) {
                $table->dropForeign('users_role_id_foreign');
            }
        });
    }

    /**
     * Les bases déjà en service portent ces contraintes depuis l'ancienne
     * migration : on ne les repose pas.
     */
    private function contrainteExiste(string $nom): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return false;
        }

        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', 'users')
            ->where('CONSTRAINT_NAME', $nom)
            ->exists();
    }
};
