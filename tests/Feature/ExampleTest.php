<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Root URL redirects to locale-prefixed URL
        $response = $this->get('/');
        $response->assertRedirect();

        // Locale-prefixed URL returns 200
        $response = $this->get('/ru');
        $response->assertStatus(200);
    }
}
