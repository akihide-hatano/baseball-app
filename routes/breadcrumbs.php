<?php
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbGenerator;

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

?>