<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $team->team_name }} の詳細
        </h2>
    </x-slot>

    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-extrabold mb-8 text-center text-indigo-800">{{ $team->team_name }}</h1>

        {{-- ★★★ ここに成功メッセージを表示するコードを追加 ★★★ --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">成功!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 2.65a1.2 1.2 0 1 1-1.697-1.697l2.65-2.651-2.65-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.183l2.651-2.651a1.2 1.2 0 1 1 1.697 1.697L11.819 10l2.65 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif
        {{-- ★★★ ここまで成功メッセージのコード ★★★ --}}
        {{-- ★★★ ここに一般的なエラーメッセージを表示するコードを追加 ★★★ --}}
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">エラー!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 2.65a1.2 1.2 0 1 1-1.697-1.697l2.65-2.651-2.65-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.183l2.651-2.651a1.2 1.2 0 1 1 1.697 1.697L11.819 10l2.65 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif
        {{-- ★★★ ここまで一般的なエラーメッセージのコード ★★★ --}}


        {{-- チーム基本情報と優勝回数 --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                <p><strong>ニックネーム:</strong> <span class="text-blue-700">{{ $team->nickname ?? $team->team_nickname }}</span></p> {{-- nicknameアクセサ考慮 --}}
                <p><strong>本拠地:</strong> <span class="text-blue-700">{{ $team->location }}</span></p>
                {{-- founded_atはnullの可能性があるため、NULLチェックを追加 --}}
                <p><strong>設立日:</strong> <span class="text-blue-700">{{ $team->founded_at ? $team->founded_at->format('Y年m月d日') : '不明' }}</span></p>
                <p><strong>所属リーグ:</strong> <span class="text-blue-700">{{ $team->league->name ?? '不明' }}</span></p>
                <p><strong>日本シリーズ優勝回数:</strong> <span class="text-blue-700">{{ $team->japan_series_titles ?? 0 }} 回</span></p>
                <p><strong>リーグ優勝回数:</strong> <span class="text-blue-700">{{ $team->league_titles ?? 0 }} 回</span></p>
                {{-- チームに所属する選手の数なども表示可能 --}}
                <p><strong>所属選手数:</strong> <span class="text-blue-700">{{ $team->players->count() }} 人</span></p>
            </div>

            {{-- ★★★ 編集・削除ボタンを追加/確認 ★★★ --}}
            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('teams.edit', $team->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                    編集
                </a>
                <form action="{{ route('teams.destroy', $team->id) }}" method="POST" onsubmit="return confirm('本当にこのチームを削除しますか？');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                        削除
                    </button>
                </form>
            </div>
            {{-- ★★★ 編集・削除ボタンここまで ★★★ --}}

        </div>


        {{-- 歴代成績の表示セクション --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-indigo-700">歴代成績</h2>
            {{-- yearlyTeamStatsがnullまたは空のコレクションの場合のチェック --}}
            @if(empty($team->yearlyTeamStats) || $team->yearlyTeamStats->isEmpty())
                <p class="text-gray-600">このチームの年度別成績データはありません。</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-blue-100 text-blue-800">
                            <tr>
                                <th class="py-3 px-4 border-b text-left">年度</th>
                                <th class="py-3 px-4 border-b text-left">勝</th>
                                <th class="py-3 px-4 border-b text-left">敗</th>
                                <th class="py-3 px-4 border-b text-left">分</th>
                                <th class="py-3 px-4 border-b text-left">勝率</th>
                                <th class="py-3 px-4 border-b text-left">順位</th>
                                <th class="py-3 px-4 border-b text-left">リーグ結果</th>
                                <th class="py-3 px-4 border-b text-left">ポストシーズン結果</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($team->yearlyTeamStats as $stat)
                                <tr class="hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : '' }}">
                                    <td class="py-3 px-4 border-b">{{ $stat->year }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->wins }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->losses }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->draws }}</td>
                                    <td class="py-3 px-4 border-b">{{ number_format($stat->winning_percentage, 3) }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->rank }}位</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->league_result }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->postseason_result }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- 直近の試合結果セクション --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-indigo-700">直近の試合結果</h2>
            @if($recentGames->isEmpty())
                <p class="text-gray-600">このチームの試合データはありません。</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-purple-100 text-purple-800">
                            <tr>
                                <th class="py-3 px-4 border-b text-left">日付</th>
                                <th class="py-3 px-4 border-b text-left">時刻</th>
                                <th class="py-3 px-4 border-b text-left">球場</th>
                                <th class="py-3 px-4 border-b text-left">ホームチーム</th>
                                <th class="py-3 px-4 border-b text-left">アウェイチーム</th>
                                <th class="py-3 px-4 border-b text-left">スコア</th>
                                <th class="py-3 px-4 border-b text-left">結果</th>
                                <th class="py-3 px-4 border-b text-left">詳細</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentGames as $game)
                                @php
                                    $displayResult = '-';
                                    $rowClass = ''; // 行の背景色クラス

                                    // 表示しているチームがホームチームの場合
                                    if ($game->home_team_id === $team->id) {
                                        if ($game->home_score !== null && $game->away_score !== null) {
                                            if ($game->home_score > $game->away_score) {
                                                $displayResult = '勝ち';
                                                $rowClass = 'bg-green-50'; // 勝利時の背景色
                                            } elseif ($game->home_score < $game->away_score) {
                                                $displayResult = '負け';
                                                $rowClass = 'bg-red-50'; // 敗北時の背景色
                                            } else {
                                                $displayResult = '引き分け';
                                                $rowClass = 'bg-gray-50'; // 引き分け時の背景色
                                            }
                                        }
                                    }
                                    // 表示しているチームがアウェイチームの場合
                                    elseif ($game->away_team_id === $team->id) {
                                        if ($game->home_score !== null && $game->away_score !== null) {
                                            if ($game->away_score > $game->home_score) {
                                                $displayResult = '勝ち';
                                                $rowClass = 'bg-green-50'; // 勝利時の背景色
                                            } elseif ($game->away_score < $game->home_score) {
                                                $displayResult = '負け';
                                                $rowClass = 'bg-red-50'; // 敗北時の背景色
                                            } else {
                                                $displayResult = '引き分け';
                                                $rowClass = 'bg-gray-50'; // 引き分け時の背景色
                                            }
                                        }
                                    }
                                @endphp
                                <tr class="hover:bg-gray-100 {{ $rowClass }}">
                                    <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($game->game_date)->format('Y年m月d日') }}</td>
                                    <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($game->game_time)->format('H:i') }}</td>
                                    <td class="py-3 px-4 border-b">{{ $game->stadium ?? '-' }}</td>
                                    <td class="py-3 px-4 border-b">{{ $game->homeTeam->nickname ?? $game->homeTeam->team_name }}</td> {{-- nickname考慮 --}}
                                    <td class="py-3 px-4 border-b">{{ $game->awayTeam->nickname ?? $game->awayTeam->team_name }}</td> {{-- nickname考慮 --}}
                                    <td class="py-3 px-4 border-b">
                                        @if($game->home_score !== null && $game->away_score !== null)
                                            {{ $game->home_score }} - {{ $game->away_score }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 border-b font-semibold">
                                        @if ($displayResult == '勝ち')
                                            <span class="text-green-700">{{ $displayResult }}</span>
                                        @elseif ($displayResult == '負け')
                                            <span class="text-red-700">{{ $displayResult }}</span>
                                        @else
                                            <span class="text-gray-700">{{ $displayResult }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 border-b flex items-center justify-center whitespace-nowrap">
                                        <a href="{{ route('games.show', $game->id) }}" class="inline-block bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-1 px-3 rounded-full text-sm transition duration-300">
                                            詳細
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>


        {{-- 所属選手一覧セクション --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-indigo-700">所属選手一覧</h2>
            @if($team->players->isEmpty())
                <p class="text-gray-600">このチームには所属選手がいません。</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-green-100 text-green-800">
                            <tr>
                                <th class="py-3 px-4 border-b text-left">背番号</th>
                                <th class="py-3 px-4 border-b text-left">選手名</th>
                                <th class="py-3 px-4 border-b text-left">打率</th>
                                <th class="py-3 px-4 border-b text-left">HR</th>
                                <th class="py-3 px-4 border-b text-left">打点</th>
                                <th class="py-3 px-4 border-b text-left">防御率</th>
                                <th class="py-3 px-4 border-b text-left">勝利</th>
                                <th class="py-3 px-4 border-b text-left">敗北</th>
                                <th class="py-3 px-4 border-b text-left">セーブ</th>
                                <th class="py-3 px-4 border-b text-left">詳細</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($team->players as $player)
                                <tr class="hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : '' }}">
                                    <td class="py-3 px-4 border-b">{{ $player->jersey_number ?? '-' }}</td>
                                    <td class="py-3 px-4 border-b">
                                        <a href="{{ route('players.show', $player->id) }}" class="text-blue-600 hover:underline">
                                            {{ $player->name }}
                                        </a>
                                    </td>

                                    {{-- 打撃成績の表示 (role条件なし) --}}
                                    @php
                                        $latestBattingStat = $player->yearlyBattingStats->first();
                                    @endphp
                                    <td class="py-3 px-4 border-b">
                                        {{ $latestBattingStat ? number_format($latestBattingStat->batting_average, 3) : '-.-' }}
                                    </td>
                                    <td class="py-3 px-4 border-b">
                                        {{ $latestBattingStat ? $latestBattingStat->home_runs : '-' }}
                                    </td>
                                    <td class="py-3 px-4 border-b">
                                        {{ $latestBattingStat ? $latestBattingStat->rbi : '-' }}
                                    </td>

                                    {{-- 投球成績の表示 (role条件なし) --}}
                                    @php
                                        $latestPitchingStat = $player->yearlyPitchingStats->first();
                                    @endphp
                                    <td class="py-3 px-4 border-b">
                                        {{ $latestPitchingStat ? number_format($latestPitchingStat->earned_run_average, 2) : '-.-' }}
                                    </td>
                                    <td class="py-3 px-4 border-b">
                                        {{ $latestPitchingStat ? $latestPitchingStat->wins : '-' }}
                                    </td>
                                    <td class="py-3 px-4 border-b">
                                        {{ $latestPitchingStat ? $latestPitchingStat->losses : '-' }}
                                    </td>
                                    <td class="py-3 px-4 border-b">
                                        {{ $latestPitchingStat ? $latestPitchingStat->saves : '-' }}
                                    </td>

                                    <td class="py-3 px-4 border-b">
                                        <a href="{{ route('players.show', $player->id) }}" class="text-blue-500 hover:text-blue-700">見る</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>


        <div class="text-center mt-8">
            <a href="{{ route('teams.index') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-full text-lg transition duration-300 transform hover:scale-105">
                チーム一覧に戻る
            </a>
        </div>
    </div>
</x-app-layout>