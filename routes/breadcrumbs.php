<?php
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbGenerator;
use App\Models\Game;

//home
Breadcrumbs::for('home',function(BreadcrumbGenerator $trail){
    $trail->push('ホーム',route('home'));
});

//home > 選手一覧
Breadcrumbs::for('players.index',function(BreadcrumbGenerator $trail){
    $trail->parent('home');
    $trail->push('選手一覧',route('players.index'));
});

//home > 選手一覧　> 選手詳細
Breadcrumbs::for('players.show',function(BreadcrumbGenerator $trail,$player){
    $trail->parent('players.index');
    $trail->push($player->name, route('players.show', $player));
});

//home > 選手一覧　> 選手作成
Breadcrumbs::for('players.create',function(BreadcrumbGenerator $trail){
    $trail->parent('players.index');
    $trail->push('選手作成', route('players.create'));
});


//home > 選手一覧　> 選手編集
Breadcrumbs::for('players.edit',function(BreadcrumbGenerator $trail,$player){
    $trail->parent('players.index');
    $trail->push($player->name, route('players.edit',$player));
});

//home > チーム一覧
Breadcrumbs::for('teams.index',function(BreadcrumbGenerator $trail){
    $trail->parent('home');
    $trail->push('チーム一覧',route('teams.index'));
});

// ホーム > チーム一覧 > [チーム名] (チーム詳細)
Breadcrumbs::for('teams.show', function (BreadcrumbGenerator $trail, $team) {
    $trail->parent('teams.index'); // 親は 'teams.index'
    $trail->push($team->team_name, route('teams.show', $team)); // チーム名を表示
});

// ホーム > チーム一覧 > チーム作成
Breadcrumbs::for('teams.create', function (BreadcrumbGenerator $trail) {
    $trail->parent('teams.index'); // 親は 'teams.index'
    $trail->push('チーム作成', route('teams.create'));
});

// ホーム > 試合一覧
Breadcrumbs::for('games.index', function (BreadcrumbGenerator $trail) {
    $trail->parent('home'); // 親は 'home'
    $trail->push('試合一覧', route('games.index'));
});

// ホーム > 試合一覧 > [試合ID/日付] (試合詳細)
Breadcrumbs::for('games.show', function (BreadcrumbGenerator $trail, Game $game) {
    $trail->parent('games.index'); // 親は 'games.index'
    // 試合を識別できる情報（例: 日付やID）を表示
    $trail->push('試合詳細 (' . $game->game_date->format('Y/m/d') . ')', route('games.show', $game));
});


// ホーム > 試合一覧 > 試合作成
Breadcrumbs::for('games.create', function (BreadcrumbGenerator $trail) {
    $trail->parent('games.index'); // 親は 'games.index'
    $trail->push('試合作成', route('games.create'));
});

// ホーム > プロフィール
Breadcrumbs::for('profile.edit', function (BreadcrumbGenerator $trail) {
    $trail->parent('home');
    $trail->push('プロフィール', route('profile.edit'));
});



?>