<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithCompany(): User
    {
        $user = User::factory()->create();
        $company = Company::create([
            'name' => 'Test Company',
            'inn' => '123456789',
            'contact_name' => 'Test',
            'phone' => '+998901234567',
            'email' => 'test@company.com',
            'status' => 'active',
        ]);
        $company->users()->attach($user->id, ['role_in_company' => 'owner']);
        $user->update(['current_company_id' => $company->id]);

        return $user;
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = $this->createUserWithCompany();

        $response = $this
            ->actingAs($user)
            ->get('/cabinet/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->createUserWithCompany();

        $response = $this
            ->actingAs($user)
            ->put('/cabinet/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
    }

    public function test_password_can_be_updated(): void
    {
        $user = $this->createUserWithCompany();

        $response = $this
            ->actingAs($user)
            ->put('/cabinet/profile/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();
    }
}
