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
                        @php
                            $displayResultText = '-';
                            $resultTextColorClass = 'text-gray-700'; // デフォルト

                            if ($game->game_result === 'Home Win') {
                                $displayResultText = ($game->homeTeam->team_name ?? 'ホームチーム') . 'の勝ち';
                                $resultTextColorClass = 'text-green-700';
                            } elseif ($game->game_result === 'Away Win') {
                                $displayResultText = ($game->awayTeam->team_name ?? 'アウェイチーム') . 'の勝ち';
                                $resultTextColorClass = 'text-blue-700';
                            } elseif ($game->game_result === '引き分け' || $game->game_result === 'Draw') {
                                $displayResultText = '引き分け';
                                $resultTextColorClass = 'text-gray-700';
                            }
                        @endphp
                        <span class="font-semibold {{ $resultTextColorClass }}">
                            {{ $displayResultText }}
                        </span>
                    </p>
                </div>
                <div class="flex justify-end mt-4 space-x-2">
                        {{-- 編集ボタン --}}
                        <a href="{{ route('games.edit', $game->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('編集') }}
                        </a>

                        {{-- ★削除フォームを追加/修正★ --}}
                        <form action="{{ route('games.destroy', $game->id) }}" method="POST" id="gameDeleteForm">
                            @csrf
                            @method('DELETE') {{-- DELETEメソッドを使用 --}}
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('削除') }}
                            </button>
                        </form>
                </div>
        </div>

        {{-- 試合別選手成績 --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-indigo-700">試合別選手成績</h2>
            @if($game->gamePlayerStats->isEmpty())
                <p class="text-gray-600">この試合の選手成績データはありません。</p>
            @else
                @php
                    $homePitchers = $game->gamePlayerStats->filter(function($stat) use ($game) {
                        return $stat->team_id === $game->home_team_id && ($stat->player->role ?? '') === '投手';
                    });
                    $homeBatters = $game->gamePlayerStats->filter(function($stat) use ($game) {
                        return $stat->team_id === $game->home_team_id && ($stat->player->role ?? '') !== '投手';
                    });
                    $awayPitchers = $game->gamePlayerStats->filter(function($stat) use ($game) {
                        return $stat->team_id === $game->away_team_id && ($stat->player->role ?? '') === '投手';
                    });
                    $awayBatters = $game->gamePlayerStats->filter(function($stat) use ($game) {
                        return $stat->team_id === $game->away_team_id && ($stat->player->role ?? '') !== '投手';
                    });
                @endphp

                @if($homePitchers->isNotEmpty() || $homeBatters->isNotEmpty())
                    <h3 class="text-xl font-bold mb-3 text-purple-700">{{ $game->homeTeam->team_name ?? 'ホームチーム' }}</h3>
                    @if($homePitchers->isNotEmpty())
                        <h4 class="text-lg font-semibold mb-2 text-gray-700">投手陣</h4>
                        @include('games._player_stats_table', ['playerGameStats' => $homePitchers])
                    @endif
                    @if($homeBatters->isNotEmpty())
                        <h4 class="text-lg font-semibold mb-2 text-gray-700 mt-4">野手陣</h4>
                        @include('games._player_stats_table', ['playerGameStats' => $homeBatters])
                    @endif
                @endif

                @if($awayPitchers->isNotEmpty() || $awayBatters->isNotEmpty())
                    <h3 class="text-xl font-bold mb-3 mt-6 text-purple-700">{{ $game->awayTeam->team_name ?? 'アウェイチーム' }}</h3>
                    @if($awayPitchers->isNotEmpty())
                        <h4 class="text-lg font-semibold mb-2 text-gray-700">投手陣</h4>
                        @include('games._player_stats_table', ['playerGameStats' => $awayPitchers])
                    @endif
                    @if($awayBatters->isNotEmpty())
                        <h4 class="text-lg font-semibold mb-2 text-gray-700 mt-4">野手陣</h4>
                        @include('games._player_stats_table', ['playerGameStats' => $awayBatters])
                    @endif
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
    <script src="{{asset('js/form-confirmation.js')}}"></script>
</x-app-layout>