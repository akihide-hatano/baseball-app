<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $player->name }} の詳細</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased">
    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-extrabold mb-8 text-center text-indigo-800">{{ $player->name }}</h1>

        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                <p><strong>背番号:</strong> <span class="text-blue-700">{{ $player->jersey_number }}</span></p>
                <p><strong>ポジション:</strong> <span class="text-blue-700">{{ $player->primary_position_name }}</span></p>
                <p><strong>利き腕:</strong> <span class="text-blue-700">{{ $player->dominant_arm }}</span></p>
                <p><strong>専門:</strong> <span class="text-blue-700">{{ $player->specialty }}</span></p>
                <p><strong>身長:</strong> <span class="text-blue-700">{{ $player->height }} cm</span></p>
                <p><strong>体重:</strong> <span class="text-blue-700">{{ $player->weight }} kg</span></p>
                <p><strong>誕生日:</strong> <span class="text-blue-700">{{ $player->date_of_birth }}</span></p>
                <p><strong>出身地:</strong> <span class="text-blue-700">{{ $player->hometown }}</span></p>
                <p><strong>プロ入り年:</strong> <span class="text-blue-700">{{ $player->pro_debut_year }}</span></p>
                <p><strong>年俸:</strong> <span class="text-blue-700">¥{{ number_format($player->salary) }}</span></p>
            </div>
            </div>

        <div class="text-center">
            <a href="{{ route('players.index') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-full text-lg transition duration-300 transform hover:scale-105">
                選手一覧に戻る
            </a>
        </div>
    </div>
</body>
</html>