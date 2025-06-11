<x-app-layout> {{-- ★既存のlayouts/app.blade.php を使用★ --}}

    {{-- Page Heading (オプション: もしlayouts/app.blade.phpの$headerスロットを使いたい場合) --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            選手一覧
        </h2>
    </x-slot>

    {{-- Page Content (これは layouts/app.blade.php の $slot に入ります) --}}
    <div class="container mx-auto p-4"> {{-- このdivはレイアウトのmainタグ内のcontainerと重複するので、削除しても良い --}}
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-600">選手一覧</h1>

        {{-- チーム選択フォーム --}}
        <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
            <form action="{{ route('players.index') }}" method="GET" class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4">
                <label for="team_id" class="text-lg font-medium text-gray-700 w-full md:w-auto">チームで絞り込む:</label>
                <select name="team_id" id="team_id" class="block w-full md:w-auto p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">全てのチーム</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                            {{ $team->team_name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus::ring-offset-2 focus:ring-blue-500 w-full md:w-auto">
                    検索
                </button>
                @if(request('team_id'))
                    <a href="{{ route('players.index') }}" class="text-blue-600 hover:underline text-sm w-full md:w-auto text-center md:text-left mt-2 md:mt-0">絞り込みを解除</a>
                @endif
            </form>
        </div>

        @if($players->isEmpty())
            <p class="text-center text-gray-600">選手データがありません。</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($players as $player)
                    <div class="bg-white rounded-lg shadow-md p-6 transform transition-transform hover:scale-105">
                        <h2 class="text-xl font-semibold mb-2 text-indigo-700">{{ $player->name }}</h2>
                        <p><strong>所属チーム:</strong> <span class="text-blue-700">{{ $player->Team->team_name ?? '所属不明' }}</span></p>
                        <p class="text-sm text-gray-600">背番号: {{ $player->jersey_number ?? '未設定' }}</p>
                        <p class="text-sm text-gray-600">身長: {{ $player->height }}cm / 体重: {{ $player->weight }}kg</p>
                        <p class="text-sm text-gray-600">専門: {{ $player->specialty }}</p>
                        <p class="text-sm text-gray-600 mb-4">出身: {{ $player->hometown }}</p>
                        <a href="{{ route('players.show', $player->id) }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full text-sm transition duration-300">
                            詳細を見る
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $players->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</x-app-layout> {{-- ★既存のlayouts/app.blade.php を使用★ --}}