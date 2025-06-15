<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            チーム一覧
        </h2>
    </x-slot>

    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-600">プロ野球チーム一覧</h1>

        {{-- ★★★ ここから検索フォームを追加 (変更なし) ★★★ --}}
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold mb-4 text-gray-800">チームを検索</h3>
            <form action="{{ route('teams.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:gap-4 items-end">
                {{-- リーグ選択ドロップダウン --}}
                <div>
                    <label for="league_id" class="block text-sm font-medium text-gray-700">リーグ</label>
                    <select id="league_id" name="league_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">全てのリーグ</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}" @selected(request('league_id') == $league->id)>
                                {{ $league->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- チーム名フリーワード検索 --}}
                <div>
                    <label for="search_team_name" class="block text-sm font-medium text-gray-700">チーム名検索</label>
                    <input type="text" id="search_team_name" name="search_team_name" value="{{ request('search_team_name') }}" placeholder="チーム名を入力" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                {{-- 検索ボタン --}}
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        検索
                    </button>
                    {{-- 検索条件をクリアするボタン --}}
                    @if(request('league_id') || request('search_team_name'))
                        <a href="{{ route('teams.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            クリア
                        </a>
                    @endif
                </div>
            </form>
        </div>
        {{-- ★★★ ここまで検索フォームを追加 ★★★ --}}

        {{-- ★★★ Bladeでリーグごとにグループ化して表示 ★★★ --}}
        @php
            // コントローラから渡された $teams コレクションをリーグ名でグループ化
            // リーグが存在しないチームは「不明なリーグ」にグループ化される
            $groupedTeamsByLeague = $teams->groupBy(function($team) {
                return $team->league->name ?? '不明なリーグ';
            })->sortKeys(); // リーグ名をアルファベット順にソート (セ・リーグ、パ・リーグなど)
        @endphp

        @if($groupedTeamsByLeague->isEmpty())
            <p class="text-center text-gray-600">表示できるチームデータがありません。</p>
        @else
            @foreach ($groupedTeamsByLeague as $leagueName => $teamsInLeague)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4 text-center text-purple-700">{{ $leagueName }}</h2> {{-- リーグ名の見出し --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($teamsInLeague as $team)
                            <div class="bg-white rounded-lg shadow-md p-6 transform transition-transform hover:scale-105">
                                <h2 class="text-xl font-semibold mb-2 text-indigo-700">{{ $team->team_name }}</h2>
                                <p class="text-sm text-gray-600">ニックネーム: {{ $team->nickname ?? $team->team_nickname }}</p>
                                <p class="text-sm text-gray-600">本拠地: {{ $team->location }}</p>
                                <p class="text-sm text-gray-600 mb-4">設立: {{ $team->founded_at->format('Y年m月d日') }}</p>
                                <p class="text-sm text-gray-600 mb-4">リーグ: {{ $team->league->name ?? '不明' }}</p>
                                <a href="{{ route('teams.show', $team->id) }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full text-sm transition duration-300">
                                    詳細を見る
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</x-app-layout>