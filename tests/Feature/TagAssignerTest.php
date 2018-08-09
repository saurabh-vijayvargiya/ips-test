<?php

namespace Tests\Feature;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\InfusionsoftController;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagAssignerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testTagAssigner()
    {
        $apiController = new ApiController();
        $userEmail = $apiController->exampleCustomer()->email;
        (new InfusionsoftController)->testInfusionsoftIntegrationGetAllTags();
        $response = $this->json('POST', 'api/module_reminder_assigner', ['email' => $userEmail]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // We will add the module of the course which user hasn't bought, it should return false.
        $response = $this->json('POST', 'api/user_module_add', ['email' => $userEmail, 'moduleName' => 'IEA Module 1']);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
            ]);
    }
}
