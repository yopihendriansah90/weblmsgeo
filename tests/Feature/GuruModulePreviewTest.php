<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GuruModulePreviewTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guru_can_preview_lesson_module_content_before_publishing(): void
    {
        Role::findOrCreate('guru');

        $guru = User::create([
            'name' => 'Guru Preview',
            'username' => 'guru_preview_module',
            'password' => 'password',
            'status' => 'active',
        ]);
        $guru->assignRole('guru');

        $course = Course::create([
            'title' => 'Materi Preview',
            'slug' => 'materi-preview',
            'status' => 'published',
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'type' => 'lesson',
            'title' => 'Bab Draft Preview',
            'slug' => 'bab-draft-preview',
            'content' => '<h2>Komponen SIG</h2><ol><li>Data spasial</li><li>Perangkat lunak</li></ol>',
            'status' => 'draft',
        ]);

        $this->actingAs($guru)
            ->get(route('guru.modules.preview', $module))
            ->assertOk()
            ->assertSee('Preview Tampilan Siswa')
            ->assertSee('Komponen SIG')
            ->assertSee('Data spasial')
            ->assertSee('Perangkat lunak');
    }
}
