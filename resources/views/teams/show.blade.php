<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $team->team_name }} の詳細
        </h2>
    </x-slot>

    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-extrabold mb-8 text-center text-indigo-800">{{ $team->team_name }}</h1>

        {{-- チーム基本情報と優勝回数 --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                <p><strong>ニックネーム:</strong> <span class="text-blue-700">{{ $team->team_nickname }}</span></p>
                <p><strong>本拠地:</strong> <span class="text-blue-700">{{ $team->location }}</span></p>
                {{-- founded_atはnullの可能性があるため、NULLチェックを追加 --}}
                <p><strong>設立日:</strong> <span class="text-blue-700">{{ $team->founded_at ? $team->founded_at->format('Y年m月d日') : '不明' }}</span></p>
                <p><strong>所属リーグ:</strong> <span class="text-blue-700">{{ $team->league->name ?? '不明' }}</span></p>
                <p><strong>日本シリーズ優勝回数:</strong> <span class="text-blue-700">{{ $team->japan_series_titles ?? 0 }} 回</span></p>
                <p><strong>リーグ優勝回数:</strong> <span class="text-blue-700">{{ $team->league_titles ?? 0 }} 回</span></p>
                {{-- チームに所属する選手の数なども表示可能 --}}
                <p><strong>所属選手数:</strong> <span class="text-blue-700">{{ $team->players->count() }} 人</span></p>
            </div>
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
                                    {{-- @if ($latestPitchingStat) @dd($latestPitchingStat) @else @dd('この選手には投球成績がありません。player_id: ' . $player->id . ' name: ' . $player->name) @endif --}}
                                    <td class="py-3 px-4 border-b">
                                        {{ $latestPitchingStat ? number_format($latestPitchingStat->earned_run_average, 2) : '-.-' }} {{-- ★ここを修正しました★ --}}
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


        <div class="text-center">
            <a href="{{ route('teams.index') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-full text-lg transition duration-300 transform hover:scale-105">
                チーム一覧に戻る
            </a>
        </div>
    </div>
</x-app-layout>