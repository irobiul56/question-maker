<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\EducationLevelController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\MyQuestionController;
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

Route::middleware(['auth','admin']) -> group(function () {
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
    

      // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard')->middleware(['auth','admin']);
    

    Route::middleware(['auth', 'user'])->prefix('user')->group(function(){
    //Dashboard
    Route::get('/dashboard', [FrontendController::class, 'userdashboard'])->name('userdashboard');
    Route::put('/institute/update', [FrontendController::class, 'updateIn'])->name('institute.update');

    // Question routes
    Route::get('/question-making', [FrontendController::class, 'qstIndex'])->name('qstIndex');
    Route::get('/selected-question', [FrontendController::class, 'sltquestion'])->name('sltquestion');
    Route::post('/save-questions', [FrontendController::class, 'saveQuestions'])->name('save.questions');
    Route::get('/question-setting/{exam_id?}', [FrontendController::class, 'qstSetting'])->name('qstSetting');
    Route::get('/question-show/{id}', [FrontendController::class, 'qstshow'])->name('qstshow');
    Route::resource('my-questions', MyQuestionController::class);
    Route::get('/contact-us', [ContactUsController::class, 'contactus'])->name('contactus');
    Route::get('/opinion', [ContactUsController::class, 'opinion'])->name('opinion');

});

require __DIR__.'/auth.php';

