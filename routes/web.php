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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Breezeの認証ルート
require __DIR__.'/auth.php';

// Profileルート (Breezeの場合)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // R: Read (一覧表示) - 通常、最も一般的なルートなので先頭に配置されることが多いですが、
    Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
    // C: Create (新規作成フォーム表示)
    Route::get('/players/create', [PlayerController::class, 'create'])->name('players.create');
    // C: Create (データ保存処理) - POST メソッドを使用
    Route::post('/players', [PlayerController::class, 'store'])->name('players.store');
    // U: Update (編集フォーム表示)
    Route::get('/players/{player}/edit', [PlayerController::class, 'edit'])->name('players.edit'); // ★ 追加/修正 ★
    // U: Update (データ更新処理) - PATCH メソッドを使用
    Route::patch('/players/{player}', [PlayerController::class, 'update'])->name('players.update'); // ★ 追加 ★
    // D: Delete (データ削除処理) - DELETE メソッドを使用
    Route::delete('/players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy'); // ★ 追加 ★
    // R: Read (詳細表示) - 最も一般的な動的ルートなので、他の具体的な動的ルートの後に定義
    Route::get('/players/{player}', [PlayerController::class, 'show'])->name('players.show'); // ★ {id} から {player} に変更 ★


    // チーム関連のルート (既存のまま。これは正しく並んでいます)
    Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::patch('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
    Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');


    // 試合関連のルート (既存のまま)
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');
});

require __DIR__.'/auth.php';