<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_redirects_to_a_locale(): void
    {
        $response = $this->get('/');
        $response->assertRedirect();
        $this->assertMatchesRegularExpression('#/(fr|nl|en)$#', (string) $response->headers->get('Location'));
    }

    public function test_french_home_is_ok(): void
    {
        $this->seed();
        $this->get('/fr')->assertOk()->assertSee('ACA');
    }
}
