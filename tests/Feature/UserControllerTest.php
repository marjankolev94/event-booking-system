<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** 
     * Test user can register successfully
     */
    public function test_user_can_register_successfully()
    {
        $payload = [
            'name' => 'Marjan Kolev',
            'email' => 'marjankolev1994@yahoo.com',
            'password' => '123456789',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('message')
                     ->has('user', fn ($json) =>
                        $json->where('email', 'marjankolev1994@yahoo.com')->etc()
                     )
            );

        $this->assertDatabaseHas('users', ['email' => 'marjankolev1994@yahoo.com']);
    }

    /** 
     * Test user can register with invalid data
     */
    public function test_registration_fails_with_invalid_data()
    {
        $payload = [
            'name' => '',     
            'email' => 'not-an-email',
            'password' => '',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** 
     * Test user can login successfully
     */
    public function test_user_can_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'marjankolev1994@yahoo.com',
            'password' => bcrypt('123456789'),
        ]);

        $payload = [
            'email' => 'marjankolev1994@yahoo.com',
            'password' => '123456789',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('message')
                     ->has('user')
                     ->has('token')
            );
    }

    /** 
     * Test user can login with invalid data
     */
    public function test_login_fails_with_invalid_data()
    {
        $payload = [
            'email' => 'invalidemail@yahoo.com',
            'password' => 'invalidemail',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Invalid credentials, or the User does not exists.']);
    }
}