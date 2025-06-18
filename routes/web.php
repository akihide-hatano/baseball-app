<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerBattingAbilityController;
use App\Http\Controllers\PlayerPitchingAbilityController;

// トップページ（ルートURL /）へのアクセスを処理
Route::get('/', function () {
    // ユーザーが認証済み（ログイン済み）であれば、ダッシュボードへリダイレクト
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // 認証されていなければ、ログインページへリダイレクト
    return redirect()->route('login');
})->name('home');

// Breezeの認証ルート
require __DIR__.'/auth.php';

// Profileルート (Breezeの場合)
Route::middleware('auth')->group(function () {
    // ★★★ ここに dashboard ルートを追加 ★★★
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

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
    Route::put('/games/{game}', [GameController::class, 'update']);
    Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');
    Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');
    Route::get('/games', [GameController::class, 'index'])->name('games.index');

    // PlayerBattingAbility のルート
    Route::get('/players/{player}/batting-abilities/create', [PlayerBattingAbilityController::class, 'create'])
        ->name('players.batting-abilities.create');
    Route::post('/players/{player}/batting-abilities', [PlayerBattingAbilityController::class, 'store'])
        ->name('players.batting-abilities.store');
    Route::get('/players/{player}/batting-abilities/{playerBattingAbility}/edit', [PlayerBattingAbilityController::class, 'edit'])
        ->name('players.batting-abilities.edit');
    Route::patch('/players/{player}/batting-abilities/{playerBattingAbility}', [PlayerBattingAbilityController::class, 'update'])
        ->name('players.batting-abilities.update');
    Route::delete('/players/{player}/batting-abilities/{playerBattingAbility}', [PlayerBattingAbilityController::class, 'destroy'])
        ->name('players.batting-abilities.destroy');

    //ピッチャーの能力のルーティング
    Route::get('players/{player}/pitching-abilities/create',[PlayerPitchingAbilityController::class,'create'])
        ->name('players.pitching-abilities.create');
    Route::post('players/{player}/pitching-abilities',[PlayerPitchingAbilityController::class,'store'])
        ->name('players.pitching-abilities.store');
    Route::get('players/{player}/pitching-abilities/{playerPitchingAbility}/edit',[PlayerPitchingAbilityController::class,'edit'])
        ->name('players.pitching-abilities.edit');
    Route::patch('players/{player}/pitching-abilities/{playerPitchingAbility}',[PlayerPitchingAbilityController::class,'update'])
        ->name('players.pitching-abilities.update');
    Route::delete('players/{player}/pitching-abilities/{playerPitchingAbility}',[PlayerPitchingAbilityController::class,'destroy'])
        ->name('players.pitching-abilities.destroy');
});

// Breezeの認証ルート (通常は1つで十分です)
require __DIR__.'/auth.php';