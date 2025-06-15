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
Route::get('/teams/create',[TeamController::class,'create'])->name('teams.create');
Route::post('/teams',[TeamController::class,'store'])->name('teams.store');

//編集フォーム表示用で表示
Route::get('teams/{id}/edit',[TeamController::class,'edit'])->name('teams.edit');
Route::patch('teams/{id}',[TeamController::class,'update'])->name('teams.update');
Route::delete('teams/{id}',[TeamController::class,'destroy'])->name('teams.destroy');

// 試合関連のルート
Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');