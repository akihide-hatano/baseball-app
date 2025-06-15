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

    // 選手関連のルート (既存のまま)
    Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
    Route::get('/players/{id}', [PlayerController::class, 'show'])->name('players.show');
    Route::get('/players/create', [PlayerController::class, 'create'])->name('players.create');

    // ★★★ チーム関連のルート (CRUD 全て手動定義 - 順序とパラメータ名を修正) ★★★

    // C: Create (新規作成フォーム表示)
    Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');

    // C: Create (データ保存処理)
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');

    // U: Update (編集フォーム表示) - /teams/{team} より先に定義
    Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit'); // ★ {id} から {team} に変更 ★

    // U: Update (データ更新処理) - PATCH メソッドを使用
    Route::patch('/teams/{team}', [TeamController::class, 'update'])->name('teams.update'); // ★ {id} から {team} に変更 ★

    // D: Delete (データ削除処理) - DELETE メソッドを使用
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy'); // ★ {id} から {team} に変更 ★

    // R: Read (詳細表示) - 最も一般的な動的ルートなので、他の具体的な動的ルートの後に定義
    Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show'); // ★ {id} から {team} に変更 ★

    // R: Read (一覧表示) - 最も一般的なルートなので、他の具体的なルートの後に定義しても良いですが、
    // 通常はルートグループの先頭付近に配置されます。
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');


    // 試合関連のルート (既存のまま)
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');
});

require __DIR__.'/auth.php';