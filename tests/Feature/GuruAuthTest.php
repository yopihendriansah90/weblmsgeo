<?php

namespace Tests\Feature;

use App\Livewire\Student\Login;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GuruAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guru_can_login_with_username(): void
    {
        Role::findOrCreate('guru');

        $user = User::create([
            'name' => 'Guru Test',
            'username' => 'guru_test',
            'email' => 'guru@test.local',
            'password' => 'password',
            'status' => 'active',
        ]);
        $user->assignRole('guru');
        Teacher::create(['user_id' => $user->id, 'status' => 'active']);

        Livewire::test(Login::class)
            ->set('username', 'guru_test')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('guru.dashboard'));
    }
}
