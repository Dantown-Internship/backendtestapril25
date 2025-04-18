<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateCompanyTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_company_creation_via_api(): void
    {
        $response = $this->postJson('/api/v1/companies',[ "name" => "BellView Limited", "email" => "bellview.companies@gmail.com" ]);
        $response->assertStatus(201);
        $response->assertJson(['status' => 'success']);
    }
}
