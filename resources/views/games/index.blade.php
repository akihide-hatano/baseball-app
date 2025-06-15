<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            試合結果一覧
        </h2>
    </x-slot>

    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-extrabold mb-8 text-center text-indigo-800">直近の試合結果</h1>

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
                                        時刻
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        球場
                                    </th>
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
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{-- その日付の試合を時刻の新しい順にソートしてループ --}}
                                @foreach($gamesOnDate->sortByDesc('game_time') as $game)
                                    {{-- 各行にdata-href属性を追加し、カーソルをポインターにするスタイルを追加 --}}
                                    <tr class="cursor-pointer hover:bg-gray-100" data-href="{{ route('games.show', $game->id) }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($game->game_time)->format('H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $game->stadium ?? '-' }}
                                        </td>
                                        {{-- ★★★ ここをteam_nameからnicknameに変更 ★★★ --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $game->homeTeam->nickname ?? '不明' }}
                                        </td>
                                        {{-- ★★★ ここをteam_nameからnicknameに変更 ★★★ --}}
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