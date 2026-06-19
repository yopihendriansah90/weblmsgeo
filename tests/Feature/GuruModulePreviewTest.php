<?php

namespace Tests\Feature;

use App\Livewire\Guru\ModuleForm;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
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
            ->get(route('guru.modules.preview', [
                'module' => $module,
                'back' => route('guru.modules.edit', $module),
            ]))
            ->assertOk()
            ->assertSee('Preview Guru')
            ->assertSee('Kembali')
            ->assertSee(route('guru.modules.edit', $module))
            ->assertSee('Perjalanan pembelajaran materi')
            ->assertSee('Komponen SIG')
            ->assertSee('Data spasial')
            ->assertSee('Perangkat lunak');
    }

    public function test_save_and_preview_updates_lesson_before_returning_preview_url(): void
    {
        Role::findOrCreate('guru');

        $guru = User::create([
            'name' => 'Guru Save Preview',
            'username' => 'guru_save_preview',
            'password' => 'password',
            'status' => 'active',
        ]);
        $guru->assignRole('guru');

        $course = Course::create([
            'title' => 'Materi Save Preview',
            'slug' => 'materi-save-preview',
            'status' => 'published',
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'type' => 'lesson',
            'title' => 'Bab Lama',
            'slug' => 'bab-lama',
            'content' => '<p>Konten lama</p>',
            'status' => 'draft',
            'sort_order' => 1,
        ]);

        $this->actingAs($guru);

        Livewire::test(ModuleForm::class, ['course' => $course, 'module' => $module])
            ->set('title', 'Bab Baru')
            ->set('slug', 'bab-baru')
            ->set('content', '<p>Konten terbaru untuk preview</p>')
            ->call('saveAndPreview');

        $module->refresh();

        $this->assertSame('Bab Baru', $module->title);
        $this->assertSame('bab-baru', $module->slug);
        $this->assertSame('<p>Konten terbaru untuk preview</p>', $module->content);
    }
}
