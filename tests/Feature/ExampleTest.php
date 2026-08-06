<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * La page d'accueil interroge désormais la table des témoignages pour
     * afficher ceux qui sont publiés. Sans RefreshDatabase, aucune table
     * n'existe dans la base SQLite en mémoire et la requête échoue.
     */
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
