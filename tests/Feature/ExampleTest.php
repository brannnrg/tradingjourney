<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Root route: unauthenticated user sees the welcome page (200).
     */
    public function test_root_shows_welcome_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Login page is accessible publicly.
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }
}
