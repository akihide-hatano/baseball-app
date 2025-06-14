<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlayerController; // コントローラーをuseする
use App\Http\Controllers\TeamController;   // コントローラーをuseする
use App\Http\Controllers\GameController;

// トップページ（ルートURL）
Route::get('/', function () {
    return view('welcome');
});

// ★★★ ここを追加または確認 ★★★
Route::get('/dashboard', function () {
    return view('dashboard'); // resources/views/dashboard.blade.php が存在すれば
})->middleware(['auth', 'verified'])->name('dashboard');

// Breezeの認証ルート
require __DIR__.'/auth.php';

// Profileルート (Breezeの場合)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 選手一覧ページのルートをコントローラーアクションにマッピング
Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
// 特定の選手詳細ページのルートをコントローラーアクションにマッピング
Route::get('/players/{id}', [PlayerController::class, 'show'])->name('players.show');
// チーム一覧ページのルートをコントローラーアクションにマッピング
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
// 特定のチーム詳細ページのルートをコントローラーアクションにマッピング
Route::get('/teams/{id}', [TeamController::class, 'show'])->name('teams.show');

// 試合関連のルート
Route::get('/games', [GameController::class, 'index'])->name('games.index'); // ★★★ この行を追加 ★★★
Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');