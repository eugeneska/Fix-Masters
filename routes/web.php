<?php

use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/quiz/device', [PageController::class, 'quizDevice'])->name('quiz.device');
Route::get('/quiz/problem', [PageController::class, 'quizProblem'])->name('quiz.problem');
Route::get('/quiz/brand', [PageController::class, 'quizBrand'])->name('quiz.brand');
Route::get('/quiz/contact', [PageController::class, 'quizContact'])->name('quiz.contact');
Route::get('/thanks', [PageController::class, 'thanks'])->name('thanks');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

Route::post('/api/leads', [LeadController::class, 'store'])->name('leads.store');

Route::redirect('/quiz', '/quiz/device', 301);
Route::redirect('/quiz/step-2', '/quiz/problem', 301);
Route::redirect('/quiz/step-3', '/quiz/brand', 301);
Route::redirect('/quiz/step-3-tv', '/quiz/brand', 301);
Route::redirect('/request', '/quiz/contact', 301);

Route::redirect('/index.html', '/', 301);
Route::redirect('/pages/quiz.html', '/quiz/device', 301);
Route::redirect('/pages/quiz-step-2.html', '/quiz/problem', 301);
Route::redirect('/pages/quiz-step-3.html', '/quiz/brand', 301);
Route::redirect('/pages/quiz-step-3-tv.html', '/quiz/brand', 301);
Route::redirect('/pages/request.html', '/quiz/contact', 301);
