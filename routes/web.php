<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\EducationLevelController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\TypeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth') -> group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        
        Route::prefix('settings')->group(function () {
        Route::resource('board', BoardController::class);
        Route::resource('level', LevelController::class);
        Route::resource('type', TypeController::class);
        Route::resource('education', EducationLevelController::class);
        Route::resource('class', ClassController::class);
        Route::put('/class/update/{education}', [ClassController::class, 'update'])->name('class.bulk-update');
        Route::resource('subject', SubjectController::class);
        Route::get('/classes/by-education/{id}', [SubjectController::class, 'getClassesByEducation'])->name('classes.by-education');
        Route::resource('chapter', ChapterController::class);
        Route::resource('topic', TopicController::class);
        Route::resource('question', QuestionController::class);


        });

    });
    Route::prefix('user')->group(function(){
        Route::get('/question-making', [FrontendController::class, 'qstIndex'])-> name('qstIndex');
        Route::get('/selected-question', [FrontendController::class, 'sltquestion'])-> name('sltquestion');

    });

require __DIR__.'/auth.php';

