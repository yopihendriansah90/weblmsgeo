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
use App\Livewire\Guru\CourseForm;
use App\Livewire\Guru\ModuleForm;
use App\Livewire\Guru\ModuleIndex;
use App\Livewire\Guru\QuizForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('super_admin')) return redirect('/admin');
        if (auth()->user()->hasRole('guru')) return redirect('/guru');
        if (auth()->user()->hasRole('siswa')) return redirect('/siswa');
    }
    return redirect()->route('login');
});

Route::get('/guru', function () {
    return redirect()->route('guru.dashboard');
});

Route::get('/guru/login', Login::class)->middleware('guest')->name('guru.login');

Route::get('/login', Login::class)->middleware('guest')->name('login');
Route::get('/login-siswa', function() { return redirect()->route('login'); })->name('student.login');

Route::post('/logout', function (Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::post('/logout-siswa', function (Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('student.login');
})->middleware('auth')->name('student.logout');

Route::post('/guru/logout', function (Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('guru.login');
})->middleware('auth')->name('guru.logout');

Route::middleware(['auth', 'student'])->prefix('siswa')->name('student.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('student.dashboard');
    })->name('home');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/kursus', CourseIndex::class)->name('courses');
    Route::get('/kursus/{course:slug}', CourseShow::class)->name('courses.show');
    Route::get('/materi/{module:slug}', LessonShow::class)->name('modules.show');
    Route::get('/kuis/{quiz}', QuizTake::class)->name('quizzes.take');
    Route::get('/riwayat-belajar', LearningHistory::class)->name('learning-history');
    Route::get('/riwayat-kuis', QuizHistory::class)->name('quiz-history');
    Route::get('/profil', Profile::class)->name('profile');
});

Route::middleware(['auth', 'guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Guru\Dashboard::class)->name('dashboard');
    Route::get('/materi', \App\Livewire\Guru\CourseIndex::class)->name('courses.index');
    Route::get('/materi/create', CourseForm::class)->name('courses.create');
    Route::get('/materi/{course}/edit', CourseForm::class)->name('courses.edit');
    Route::get('/materi/{course}/bab', ModuleIndex::class)->name('modules.index');
    Route::get('/materi/{course}/bab/create', ModuleForm::class)->name('modules.create');
    Route::get('/bab/{module}/edit', ModuleForm::class)->name('modules.edit');
    Route::get('/bab/{module}/quiz', QuizForm::class)->name('quizzes.create');
    Route::get('/bab/{module}/quiz/{quiz}', QuizForm::class)->name('quizzes.edit');
    Route::post('/editor-image-upload', function (Request $request) {
        $request->validate([
            'file' => ['required', 'image', 'max:5120'],
        ]);

        $path = $request->file('file')->store('lesson-content', 'public');

        return response()->json([
            'location' => '/storage/'.$path,
        ]);
    })->name('editor-image-upload');
});
