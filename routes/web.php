<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DecisionSessionController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\AlternativeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AhpPairwiseController;
use App\Http\Controllers\CriteriaAggregationController;
use App\Http\Controllers\DecisionResultController;
use App\Http\Controllers\DecisionMakerController;
use App\Http\Controllers\DecisionControlController;
use App\Http\Controllers\CriteriaScoringRuleController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/decision-sessions', [DecisionSessionController::class, 'index'])->name('decision-sessions.index');

    Route::get('/decision-sessions/create', [DecisionSessionController::class, 'create'])->name('decision-sessions.create');


    Route::get(
        '/decision-sessions/{decisionSession}/edit',
        [DecisionSessionController::class, 'edit']
    )->name('decision-sessions.edit');

    Route::post('/decision-sessions', [DecisionSessionController::class, 'store'])->name('decision-sessions.store');

    Route::get(
        '/decision-sessions/{decisionSession}/criteria',
        [CriteriaController::class, 'index']
    )->name('criteria.index');

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

    // ================= CRITERIA SCORING RULE =================
    Route::post(
        '/criteria/{criteria}/scoring-rule',
        [CriteriaScoringRuleController::class, 'store']
    )->name('criteria.scoring.store');
    Route::put(
        '/criteria/{criteria}/scoring-rule/{rule}',
        [CriteriaScoringRuleController::class, 'update']
    )->name('criteria.scoring.update');

    // ================= ALTERNATIVES =================

    Route::get(
        '/decision-sessions/{decisionSession}/alternatives',
        [AlternativeController::class, 'index']
    )->name('alternatives.index');

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

    // ================= CONTROL =================

    Route::get(
        '/decision-sessions/{decisionSession}/control',
        [DecisionControlController::class, 'index']
    )->name('control.index');

    Route::put(
        '/decision-sessions/{decisionSession}',
        [DecisionSessionController::class, 'update']
    )->name('decision-sessions.update');

    Route::patch(
        '/decision-sessions/{decisionSession}/activate',
        [DecisionSessionController::class, 'activate']
    )->name('decision-sessions.activate');

    Route::patch(
        '/decision-sessions/{decisionSession}/lock-criteria',
        [CriteriaAggregationController::class, 'lock']
    )->name('decision-sessions.lock-criteria');

    Route::patch(
        '/decision-sessions/{decisionSession}/close',
        [DecisionSessionController::class, 'close']
    )->name('decision-sessions.close');

    Route::get(
        '/decision-sessions/{decisionSession}/assign-dms',
        [DecisionSessionController::class, 'assignDms']
    )->name('decision-sessions.assign-dms');

    Route::post(
        '/decision-sessions/{decisionSession}/assign-dms',
        [DecisionSessionController::class, 'storeAssignedDms']
    )->name('decision-sessions.assign-dms.store');

    Route::delete(
        '/decision-sessions/{decisionSession}',
        [DecisionSessionController::class, 'destroy']
    )->name('decision-sessions.destroy');
});

Route::middleware(['auth'])->group(function () {


    Route::get(
        '/decision-sessions/{decisionSession}/dms',
        [DecisionMakerController::class, 'index']
    )->name('dms.index')
        ->middleware('role:dm');

    Route::get(
        '/decision-sessions/{decisionSession}/alternative-evaluations',
        [\App\Http\Controllers\AlternativeEvaluationController::class, 'index']
    )->name('alternative-evaluations.index')
        ->middleware('role:dm');

    // DM workspace: alternative evaluations (must come after workspace, and be DM-only)
    Route::post(
        '/decision-sessions/{decisionSession}/alternative-evaluations',
        [\App\Http\Controllers\AlternativeEvaluationController::class, 'store']
    )->name('alternative-evaluations.store')
        ->middleware('role:dm');

    Route::post(
        '/decision-sessions/{decisionSession}/pairwise',
        [AhpPairwiseController::class, 'store']
    )->name('decision-sessions.pairwise.store')
        ->middleware('role:dm');

    Route::get(
        '/decision-sessions/{decisionSession}/result',
        [DecisionResultController::class, 'show']
    )->name('decision-sessions.result')
        ->middleware('role:admin');
});


require __DIR__ . '/auth.php';
