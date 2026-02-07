<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DecisionSessionController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\AlternativeController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get(
        '/decision-sessions/{decisionSession}',
        [DecisionSessionController::class, 'show']
    )->name('decision-sessions.show');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/decision-sessions', [DecisionSessionController::class, 'index'])->name('decision-sessions.index');

    Route::post(
        '/decision-sessions/{decisionSession}/criteria',
        [CriteriaController::class, 'store']
    )->name('criteria.store');

    Route::put(
        '/criteria/{criteria}',
        [CriteriaController::class, 'update']
    )->name('criteria.update');

    Route::patch(
        '/criteria/{criteria}/toggle',
        [CriteriaController::class, 'toggle']
    )->name('criteria.toggle');

    Route::delete(
        '/criteria/{criteria}',
        [CriteriaController::class, 'destroy']
    )->name('criteria.destroy');

    // ================= ALTERNATIVES =================

    Route::post(
        '/decision-sessions/{decisionSession}/alternatives',
        [AlternativeController::class, 'store']
    )->name('alternatives.store');

    Route::put(
        '/alternatives/{alternative}',
        [AlternativeController::class, 'update']
    )->name('alternatives.update');

    Route::patch(
        '/alternatives/{alternative}/toggle',
        [AlternativeController::class, 'toggle']
    )->name('alternatives.toggle');

    Route::delete(
        '/alternatives/{alternative}',
        [AlternativeController::class, 'destroy']
    )->name('alternatives.destroy');

    Route::get('/decision-sessions/create', [DecisionSessionController::class, 'create'])->name('decision-sessions.create');
    Route::post('/decision-sessions', [DecisionSessionController::class, 'store'])->name('decision-sessions.store');

    Route::get(
        '/decision-sessions/{decisionSession}/edit',
        [DecisionSessionController::class, 'edit']
    )->name('decision-sessions.edit');

    Route::put(
        '/decision-sessions/{decisionSession}',
        [DecisionSessionController::class, 'update']
    )->name('decision-sessions.update');

    Route::patch(
        '/decision-sessions/{decisionSession}/activate',
        [DecisionSessionController::class, 'activate']
    )->name('decision-sessions.activate');

    Route::patch(
        '/decision-sessions/{decisionSession}/close',
        [DecisionSessionController::class, 'close']
    )->name('decision-sessions.close');

    Route::post(
        '/decision-sessions/{decisionSession}/assign-dm',
        [DecisionSessionController::class, 'assignDms']
    )->name('decision-sessions.assign-dm');

    Route::delete(
        '/decision-sessions/{decisionSession}',
        [DecisionSessionController::class, 'destroy']
    )->name('decision-sessions.destroy');
});

Route::middleware(['auth'])->group(function () {

    Route::post(
        '/decision-sessions/{decisionSession}/pairwise',
        [DecisionSessionController::class, 'storePairwise']
    )->name('decision-sessions.pairwise.store');
});

require __DIR__ . '/auth.php';
