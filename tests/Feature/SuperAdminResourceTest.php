<?php

namespace Tests\Feature;

use App\Filament\Resources\SuperAdmins\SuperAdminResource;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuperAdminResourceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_super_admin_resource_only_lists_super_admin_accounts(): void
    {
        Role::findOrCreate('super_admin');
        Role::findOrCreate('guru');

        $superAdmin = User::create([
            'name' => 'Root Admin',
            'username' => 'root_admin',
            'email' => 'root@example.com',
            'password' => 'password',
            'status' => 'active',
        ]);
        $superAdmin->assignRole('super_admin');

        $teacher = User::create([
            'name' => 'Guru Biasa',
            'username' => 'guru_biasa',
            'email' => 'guru@example.com',
            'password' => 'password',
            'status' => 'active',
        ]);
        $teacher->assignRole('guru');

        $this->actingAs($superAdmin);

        $usernames = SuperAdminResource::getEloquentQuery()->pluck('username');

        $this->assertTrue($usernames->contains('root_admin'));
        $this->assertFalse($usernames->contains('guru_biasa'));
    }
}
