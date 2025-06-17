<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlayerController; // コントローラーをuseする
use App\Http\Controllers\TeamController;   // コントローラーをuseする
use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerBattingAbilityController; // ★PlayerBattingAbilityController をuseする

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

    // Player関連のルート
    Route::get('/players/create', [PlayerController::class, 'create'])->name('players.create');
    Route::post('/players', [PlayerController::class, 'store'])->name('players.store');
    Route::get('/players/{player}/edit', [PlayerController::class, 'edit'])->name('players.edit');
    Route::patch('/players/{player}', [PlayerController::class, 'update'])->name('players.update');
    Route::delete('/players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');
    Route::get('/players/{player}', [PlayerController::class, 'show'])->name('players.show');
    Route::get('/players', [PlayerController::class, 'index'])->name('players.index');


    // チーム関連のルート
    Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::patch('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
    Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');


    // 試合関連のルート
    Route::get('/games/create', [GameController::class, 'create'])->name('games.create');
    Route::post('/games', [GameController::class, 'store'])->name('games.store');
    Route::get('/games/{game}/edit', [GameController::class, 'edit'])->name('games.edit');
    Route::patch('/games/{game}', [GameController::class, 'update'])->name('games.update');
    Route::put('/games/{game}', [GameController::class, 'update']); // PATCHとPUTの両方を受け入れる場合
    Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');
    Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');
    Route::get('/games', [GameController::class, 'index'])->name('games.index');


    // ★★★ PlayerBattingAbility のルート (修正版) ★★★
    // 親リソースの {player} ID と子リソースの {playerBattingAbility} ID を使う
    // ルートモデルバインディングを考慮し、パラメータ名は単数形に。

    // 新規能力作成フォームの表示
    Route::get('/players/{player}/batting-abilities/create', [PlayerBattingAbilityController::class, 'create'])
        ->name('players.batting-abilities.create');

    // 新規能力データの保存 (POSTメソッド、RESTfulなURL)
    Route::post('/players/{player}/batting-abilities', [PlayerBattingAbilityController::class, 'store'])
        ->name('players.batting-abilities.store');

    // 既存能力編集フォームの表示 (特定の能力IDをURLに含める)
    Route::get('/players/{player}/batting-abilities/{playerBattingAbility}/edit', [PlayerBattingAbilityController::class, 'edit'])
        ->name('players.batting-abilities.edit');

    // 既存能力データの更新 (PATCHメソッド、RESTfulなURL、特定の能力IDをURLに含める)
    Route::patch('/players/{player}/batting-abilities/{playerBattingAbility}', [PlayerBattingAbilityController::class, 'update'])
        ->name('players.batting-abilities.update');

    // 既存能力データの削除 (DELETEメソッド、RESTfulなURL、特定の能力IDをURLに含める)
    Route::delete('/players/{player}/batting-abilities/{playerBattingAbility}', [PlayerBattingAbilityController::class, 'destroy'])
        ->name('players.batting-abilities.destroy');

    // index と show は選手詳細ページで表示されるため、ここではウェブページとしてのルートは定義しません。
    // 必要であれば後から追加できます (例: Route::get('/players/{player}/batting-abilities', ...)->name('players.batting-abilities.index');)
});

// Breezeの認証ルート (通常は1つで十分です)
require __DIR__.'/auth.php';