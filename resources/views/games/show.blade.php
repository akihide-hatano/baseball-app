<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            試合詳細
        </h2>
    </x-slot>

    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-extrabold mb-8 text-center text-indigo-800">試合詳細</h1>

        {{-- 試合基本情報 --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-indigo-700">試合情報</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                <p><strong>日付:</strong> <span class="text-blue-700">{{ \Carbon\Carbon::parse($game->game_date)->format('Y年m月d日') }}</span></p>
                <p><strong>時刻:</strong> <span class="text-blue-700">{{ \Carbon\Carbon::parse($game->game_time)->format('H:i') }}</span></p>
                <p class="md:col-span-2"><strong>球場:</strong> <span class="text-blue-700">{{ $game->stadium ?? '-' }}</span></p>

                <p><strong>ホームチーム:</strong> <span class="text-blue-700">{{ $game->homeTeam->team_name ?? '不明' }}</span></p>
                <p><strong>アウェイチーム:</strong> <span class="text-blue-700">{{ $game->awayTeam->team_name ?? '不明' }}</span></p>

                <p><strong>スコア:</strong>
                    <span class="text-blue-700">
                        @if($game->home_score !== null && $game->away_score !== null)
                            {{ $game->home_score }} - {{ $game->away_score }}
                        @else
                            未定
                        @endif
                    </span>
                </p>
                <p><strong>結果:</strong>
                    {{-- ★ここを修正：表示テキストとクラスの適用方法★ --}}
                    @php
                        $displayResultText = '-';
                        $resultTextColorClass = 'text-gray-700'; // デフォルト

                        if ($game->game_result === 'Home Win') {
                            $displayResultText = ($game->homeTeam->team_name ?? 'ホームチーム') . 'の勝ち';
                            $resultTextColorClass = 'text-green-700';
                        } elseif ($game->game_result === 'Away Win') {
                            $displayResultText = ($game->awayTeam->team_name ?? 'アウェイチーム') . 'の勝ち';
                            $resultTextColorClass = 'text-blue-700'; // アウェイ勝利は区別のため青に
                        } elseif ($game->game_result === '引き分け') { // データベースの 'Draw' がシーダーで '引き分け' になっている場合
                            $displayResultText = '引き分け';
                            $resultTextColorClass = 'text-gray-700';
                        } elseif ($game->game_result === 'Draw') { // データベースの 'Draw' がそのままの場合
                            $displayResultText = '引き分け';
                            $resultTextColorClass = 'text-gray-700';
                        }
                    @endphp
                    <span class="font-semibold {{ $resultTextColorClass }}">
                        {{ $displayResultText }}
                    </span>
                </p>
            </div>
        </div>

        {{-- 試合別選手成績 --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-indigo-700">試合別選手成績</h2>
            @if($game->gamePlayerStats->isEmpty())
                <p class="text-gray-600">この試合の選手成績データはありません。</p>
            @else
                {{-- ホームチームとアウェイチームでテーブルを分ける、またはチームでグループ化する --}}
                @php
                    $homeTeamStats = $game->gamePlayerStats->filter(function($stat) use ($game) {
                        return $stat->team_id === $game->home_team_id;
                    });
                    $awayTeamStats = $game->gamePlayerStats->filter(function($stat) use ($game) {
                        return $stat->team_id === $game->away_team_id;
                    });
                @endphp

                @if($homeTeamStats->isNotEmpty())
                    <h3 class="text-xl font-bold mb-3 text-purple-700">{{ $game->homeTeam->team_name ?? 'ホームチーム' }}</h3>
                    @include('games._player_stats_table', ['playerGameStats' => $homeTeamStats])
                @endif

                @if($awayTeamStats->isNotEmpty())
                    <h3 class="text-xl font-bold mb-3 mt-6 text-purple-700">{{ $game->awayTeam->team_name ?? 'アウェイチーム' }}</h3>
                    @include('games._player_stats_table', ['playerGameStats' => $awayTeamStats])
                @endif
            @endif
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('teams.show', $game->home_team_id) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full text-lg transition duration-300 transform hover:scale-105 mr-4">
                ホームチーム詳細に戻る
            </a>
            <a href="{{ route('teams.show', $game->away_team_id) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full text-lg transition duration-300 transform hover:scale-105 mr-4">
                アウェイチーム詳細に戻る
            </a>
            <a href="{{ route('teams.index') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-full text-lg transition duration-300 transform hover:scale-105">
                チーム一覧に戻る
            </a>
        </div>
    </div>
</x-app-layout>