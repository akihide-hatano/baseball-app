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

 // 1. 固定パスを持つルートや、最も具体的なアクションを先に配置
    Route::get('/players/create', [PlayerController::class, 'create'])->name('players.create');
    Route::post('/players', [PlayerController::class, 'store'])->name('players.store');

    // 2. IDを含む動的なパスの中でも、より具体的なアクション（editなど）を配置
    Route::get('/players/{player}/edit', [PlayerController::class, 'edit'])->name('players.edit');

    // 3. IDを含む動的なパスで、特定のHTTPメソッドを持つアクションを配置 (GET以外)
    Route::patch('/players/{player}', [PlayerController::class, 'update'])->name('players.update');
    Route::delete('/players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');

    // 4. IDを含む動的なパスで、最も一般的な表示アクション (show) を最後に配置
    Route::get('/players/{player}', [PlayerController::class, 'show'])->name('players.show');

    // 5. IDを含まない一般的な一覧表示ルートを、最後に配置することも可能ですが、
    // 通常は /players/{player} よりも手前に置く方が自然な場合が多いです。
    // 今回は create, store の後に置いていますが、index は /players/{player} よりも前にあれば問題ありません。
    Route::get('/players', [PlayerController::class, 'index'])->name('players.index');


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