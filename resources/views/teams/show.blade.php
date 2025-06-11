<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $team->team_name }} の詳細
        </h2>
    </x-slot>

    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-extrabold mb-8 text-center text-indigo-800">{{ $team->team_name }}</h1>

        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                <p><strong>ニックネーム:</strong> <span class="text-blue-700">{{ $team->team_nickname }}</span></p>
                <p><strong>本拠地:</strong> <span class="text-blue-700">{{ $team->location }}</span></p>
                <p><strong>設立日:</strong> <span class="text-blue-700">{{ $team->founded_at->format('Y年m月d日') }}</span></p>
                <p><strong>所属リーグ:</strong> <span class="text-blue-700">{{ $team->league->name ?? '不明' }}</span></p> {{-- leagueリレーションがあれば表示 --}}
                {{-- チームに所属する選手の数なども表示可能 --}}
                {{-- <p><strong>所属選手数:</strong> <span class="text-blue-700">{{ $team->players->count() }} 人</span></p> --}}
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('teams.index') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-full text-lg transition duration-300 transform hover:scale-105">
                チーム一覧に戻る
            </a>
        </div>
    </div>
</x-app-layout>