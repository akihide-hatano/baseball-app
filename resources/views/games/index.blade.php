<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            試合結果一覧
        </h2>
    </x-slot>

    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-extrabold mb-8 text-center text-indigo-800">直近の試合結果</h1>

        {{-- ★★★ ここから検索フォームを追加 ★★★ --}}
        <div class="bg-white shadow-xl rounded-lg p-6 mb-8">
            <h3 class="text-xl font-bold mb-4 text-gray-800">試合を検索</h3>
            <form action="{{ route('games.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:gap-4 items-end">
                {{-- チーム選択ドロップダウン --}}
                <div>
                    <label for="team_id" class="block text-sm font-medium text-gray-700">チーム名</label>
                    <select id="team_id" name="team_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">全てのチーム</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" @selected(request('team_id') == $team->id)>
                                {{ $team->nickname ?? $team->team_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 日付入力フィールド --}}
                <div>
                    <label for="search_date" class="block text-sm font-medium text-gray-700">実施日</label>
                    <input type="date" id="search_date" name="search_date" value="{{ request('search_date') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                {{-- 検索ボタン --}}
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        検索
                    </button>
                    {{-- 検索条件をクリアするボタン --}}
                    @if(request('team_id') || request('search_date'))
                        <a href="{{ route('games.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            クリア
                        </a>
                    @endif
                </div>
            </form>
        </div>
        {{-- ★★★ ここまで検索フォームを追加 ★★★ --}}

        @if($groupedGames->isEmpty())
            <p class="text-gray-600 text-center">表示できる試合がありません。</p>
        @else
            {{-- 日付の新しい順にグループをループ --}}
            @foreach($groupedGames as $date => $gamesOnDate)
                <div class="bg-white shadow-xl rounded-lg overflow-hidden mb-8">
                    <h3 class="text-2xl font-bold p-4 bg-gray-100 text-gray-800">{{ $date }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ホームチーム
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        アウェイチーム
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        スコア
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        結果
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        球場
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{-- その日付の試合を時刻の新しい順にソートしてループ --}}
                                @foreach($gamesOnDate->sortByDesc('game_time') as $game)
                                    {{-- 各行にdata-href属性を追加し、カーソルをポインターにするスタイルを追加 --}}
                                    <tr class="cursor-pointer hover:bg-gray-100" data-href="{{ route('games.show', $game->id) }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $game->homeTeam->nickname ?? '不明' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $game->awayTeam->nickname ?? '不明' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($game->home_score !== null && $game->away_score !== null)
                                            {{ $game->home_score }} - {{ $game->away_score }}
                                            @else
                                            未定
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $displayResultText = '-';
                                                $resultTextColorClass = 'text-gray-700'; // デフォルト
                                                if ($game->game_result === 'Home Win') {
                                                    $displayResultText = ($game->homeTeam->nickname ?? 'ホームチーム') . 'の勝ち';
                                                    $resultTextColorClass = 'text-green-700';
                                                } elseif ($game->game_result === 'Away Win') {
                                                    $displayResultText = ($game->awayTeam->nickname ?? 'アウェイチーム') . 'の勝ち';
                                                    $resultTextColorClass = 'text-blue-700'; // 青色に変更 (相手チームの勝ちなので)
                                                } elseif ($game->game_result === '引き分け' || $game->game_result === 'Draw') {
                                                    $displayResultText = '引き分け';
                                                    $resultTextColorClass = 'text-gray-700';
                                                } else {
                                                    $displayResultText = '不明';
                                                    $resultTextColorClass = 'text-gray-500';
                                                }
                                                @endphp
                                            <span class="font-semibold {{ $resultTextColorClass }}">
                                                {{ $displayResultText }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $game->stadium ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="text-center mt-8">
            <a href="{{ route('teams.index') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-full text-lg transition duration-300 transform hover:scale-105">
                チーム一覧に戻る
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rows = document.querySelectorAll('tr[data-href]');

            rows.forEach(row => {
                row.addEventListener('click', function () {
                    window.location.href = this.dataset.href;
                });
            });
        });
    </script>
</x-app-layout>