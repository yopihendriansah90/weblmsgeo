<?php

use App\Livewire\Student\CourseIndex;
use App\Livewire\Student\CourseShow;
use App\Livewire\Student\Dashboard;
use App\Livewire\Student\LearningHistory;
use App\Livewire\Student\LessonShow;
use App\Livewire\Student\Login;
use App\Livewire\Student\Profile;
use App\Livewire\Student\QuizHistory;
use App\Livewire\Student\QuizTake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('student.login');
});

Route::get('/login-siswa', Login::class)->middleware('guest')->name('student.login');

Route::post('/logout-siswa', function (Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('student.login');
})->middleware('auth')->name('student.logout');

Route::middleware(['auth', 'student'])->prefix('siswa')->name('student.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/kursus', CourseIndex::class)->name('courses');
    Route::get('/kursus/{course:slug}', CourseShow::class)->name('courses.show');
    Route::get('/materi/{lesson:slug}', LessonShow::class)->name('lessons.show');
    Route::get('/kuis/{quiz}', QuizTake::class)->name('quizzes.take');
    Route::get('/riwayat-belajar', LearningHistory::class)->name('learning-history');
    Route::get('/riwayat-kuis', QuizHistory::class)->name('quiz-history');
    Route::get('/profil', Profile::class)->name('profile');
});
