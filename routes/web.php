<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController; // コントローラーをuseする
use App\Http\Controllers\TeamController;   // コントローラーをuseする

// トップページ（ルートURL）
Route::get('/', function () {
    return view('welcome');
});

// 選手一覧ページのルートをコントローラーアクションにマッピング
Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
// 特定の選手詳細ページのルートをコントローラーアクションにマッピング
Route::get('/players/{id}', [PlayerController::class, 'show'])->name('players.show');
// チーム一覧ページのルートをコントローラーアクションにマッピング
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
// 特定のチーム詳細ページのルートをコントローラーアクションにマッピング
Route::get('/teams/{id}', [TeamController::class, 'show'])->name('teams.show');