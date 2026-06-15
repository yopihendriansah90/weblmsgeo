<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizStep;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $superAdminRole = Role::findOrCreate('super_admin');
            Role::findOrCreate('guru');
            Role::findOrCreate('siswa');

            $admin = User::updateOrCreate(
                ['username' => 'admin'],
                ['name' => 'Super Admin', 'email' => 'admin@gmail.com', 'password' => 'password', 'status' => 'active'],
            );
            $admin->syncRoles([$superAdminRole]);

            $course = Course::updateOrCreate(
                ['slug' => 'sistem-informasi-geografis'],
                [
                    'title' => 'Sistem Informasi Geografis',
                    'description' => 'Materi pengantar SIG untuk memahami data spasial, komponen SIG, dan penerapannya.',
                    'status' => 'published',
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ],
            );

            $module = Module::updateOrCreate(
                ['course_id' => $course->id, 'slug' => 'pendahuluan-sig'],
                ['title' => 'Pendahuluan SIG', 'type' => 'lesson', 'description' => 'Dasar konsep SIG.', 'sort_order' => 1, 'status' => 'published'],
            );

            $module->update([
                'content' => '<h2>Pengertian SIG</h2><p>Sistem Informasi Geografis adalah sistem berbasis komputer untuk mengumpulkan, mengelola, menganalisis, dan menyajikan data yang memiliki referensi lokasi di permukaan bumi.</p><h2>Komponen SIG</h2><ul><li>Data spasial dan atribut.</li><li>Perangkat keras.</li><li>Perangkat lunak.</li><li>Manusia dan metode analisis.</li></ul>',
                'estimated_duration' => 20,
                'published_at' => now(),
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);

            $quizModule = Module::updateOrCreate(
                ['course_id' => $course->id, 'slug' => 'quiz-materi'],
                [
                    'title' => 'Quiz Materi',
                    'type' => 'quiz',
                    'description' => 'Quiz akhir materi.',
                    'content' => null,
                    'estimated_duration' => null,
                    'sort_order' => 999,
                    'status' => 'published',
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ],
            );

            $quiz = Quiz::updateOrCreate(
                ['module_id' => $quizModule->id, 'title' => 'Kuis Akhir Pengertian SIG'],
                [
                    'description' => 'Kuis berurutan berisi essay, penjodohan teks, table checklist, dan penjodohan gambar-teks.',
                    'mode' => 'practice',
                    'allow_retake' => false,
                    'max_attempts' => 1,
                    'status' => 'published',
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ],
            );

            foreach ($this->quizSteps() as $step) {
                QuizStep::updateOrCreate(
                    ['quiz_id' => $quiz->id, 'sort_order' => $step['sort_order']],
                    [
                        'title' => $step['title'],
                        'type' => $step['type'],
                        'instruction' => $step['instruction'],
                        'content_payload' => $step['content_payload'],
                        'answer_mode' => $step['answer_mode'] ?? null,
                        'is_required' => true,
                        'show_result_after_submit' => true,
                        'allow_next_after_submit' => true,
                        'status' => 'published',
                    ],
                );
            }
        });

        $this->call([
            GuruAccountSeeder::class,
            SiswaAccountSeeder::class,
        ]);
    }

    private function quizSteps(): array
    {
        return [
            [
                'title' => 'Essay: Manfaat SIG',
                'type' => 'essay',
                'instruction' => 'Jawab dengan singkat dan jelas.',
                'sort_order' => 1,
                'content_payload' => ['question' => 'Jelaskan dua manfaat SIG dalam kehidupan sehari-hari.'],
            ],
            [
                'title' => 'Penjodohan Teks',
                'type' => 'text_matching',
                'instruction' => 'Pasangkan istilah dengan pengertian yang tepat.',
                'sort_order' => 2,
                'content_payload' => [
                    'items' => [
                        ['key' => 'spasial', 'label' => 'Data spasial'],
                        ['key' => 'atribut', 'label' => 'Data atribut'],
                        ['key' => 'layer', 'label' => 'Layer peta'],
                    ],
                    'options' => [
                        ['key' => 'lokasi', 'label' => 'Data yang menunjukkan lokasi atau bentuk objek'],
                        ['key' => 'keterangan', 'label' => 'Data keterangan non-lokasi'],
                        ['key' => 'tema', 'label' => 'Lapisan informasi tematik pada peta'],
                    ],
                    'pairs' => [
                        ['item_key' => 'spasial', 'correct_option_key' => 'lokasi'],
                        ['item_key' => 'atribut', 'correct_option_key' => 'keterangan'],
                        ['item_key' => 'layer', 'correct_option_key' => 'tema'],
                    ],
                ],
            ],
            [
                'title' => 'Table Checklist',
                'type' => 'table_checklist',
                'instruction' => 'Pilih satu kategori paling tepat untuk setiap baris.',
                'sort_order' => 3,
                'answer_mode' => 'single_answer_per_row',
                'content_payload' => [
                    'columns' => [
                        ['id' => 'hardware', 'label' => 'Hardware'],
                        ['id' => 'software', 'label' => 'Software'],
                        ['id' => 'data', 'label' => 'Data'],
                    ],
                    'rows' => [
                        ['id' => 'gps', 'label' => 'GPS'],
                        ['id' => 'qgis', 'label' => 'QGIS'],
                        ['id' => 'citra', 'label' => 'Citra satelit'],
                    ],
                    'correct_cells' => [
                        ['row_id' => 'gps', 'column_id' => 'hardware'],
                        ['row_id' => 'qgis', 'column_id' => 'software'],
                        ['row_id' => 'citra', 'column_id' => 'data'],
                    ],
                ],
            ],
            [
                'title' => 'Penjodohan Gambar dan Teks',
                'type' => 'image_text_matching',
                'instruction' => 'Pasangkan objek visual dengan kategori SIG.',
                'sort_order' => 4,
                'content_payload' => [
                    'items' => [
                        ['key' => 'peta', 'label' => 'Gambar peta tematik', 'image_url' => 'https://placehold.co/320x180?text=Peta', 'alt' => 'Peta tematik'],
                        ['key' => 'satelit', 'label' => 'Gambar citra satelit', 'image_url' => 'https://placehold.co/320x180?text=Citra+Satelit', 'alt' => 'Citra satelit'],
                    ],
                    'options' => [
                        ['key' => 'peta_tematik', 'label' => 'Peta tematik'],
                        ['key' => 'citra_satelit', 'label' => 'Citra satelit'],
                    ],
                    'pairs' => [
                        ['item_key' => 'peta', 'correct_option_key' => 'peta_tematik'],
                        ['item_key' => 'satelit', 'correct_option_key' => 'citra_satelit'],
                    ],
                ],
            ],
        ];
    }
}
