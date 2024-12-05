<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentimentAnalysisController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/analyze', function () {
    return view('sentiment.analyze');
})->name('analyze.form');

Route::post('/analyze', [SentimentAnalysisController::class, 'analyze'])->name('analyze');
Route::get('/history', [SentimentAnalysisController::class, 'history'])->name('history');
Route::get('/statistics', [SentimentAnalysisController::class, 'statistics'])->name('statistics');