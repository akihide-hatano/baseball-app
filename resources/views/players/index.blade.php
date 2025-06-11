<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>選手一覧</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-600">選手一覧</h1>

        {{-- ★ここからチーム選択フォームの追加★ --}}
        <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
            <form action="{{ route('players.index') }}" method="GET" class="flex items-center space-x-4">
                <label for="team_id" class="text-lg font-medium text-gray-700">チームで絞り込む:</label>
                <select name="team_id" id="team_id" class="block w-full md:w-auto p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">全てのチーム</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                            {{ $team->team_name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    検索
                </button>
                @if(request('team_id'))
                    <a href="{{ route('players.index') }}" class="text-blue-600 hover:underline text-sm">絞り込みを解除</a>
                @endif
            </form>
        </div>
        {{-- ★ここまでチーム選択フォームの追加★ --}}

        @if($players->isEmpty())
            <p class="text-center text-gray-600">選手データがありません。</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($players as $player)
                    <div class="bg-white rounded-lg shadow-md p-6 transform transition-transform hover:scale-105">
                        <h2 class="text-xl font-semibold mb-2 text-indigo-700">{{ $player->name }}</h2>
                        <p><strong>所属チーム:</strong> <span class="text-blue-700">{{ $player->Team->team_name ?? '所属不明' }}</span></p> {{-- currentTeamに変更 --}}
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
                {{ $players->links() }}
            </div>
        @endif
    </div>
</body>
</html>