<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            チーム一覧
        </h2>
    </x-slot>

    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-600">プロ野球チーム一覧</h1>

        @if($teams->isEmpty())
            <p class="text-center text-gray-600">チームデータがありません。</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($teams as $team)
                    <div class="bg-white rounded-lg shadow-md p-6 transform transition-transform hover:scale-105">
                        <h2 class="text-xl font-semibold mb-2 text-indigo-700">{{ $team->team_name }}</h2>
                        <p class="text-sm text-gray-600">ニックネーム: {{ $team->team_nickname }}</p>
                        <p class="text-sm text-gray-600">本拠地: {{ $team->location }}</p>
                        <p class="text-sm text-gray-600 mb-4">設立: {{ $team->founded_at->format('Y年m月d日') }}</p>
                        <a href="{{ route('teams.show', $team->id) }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full text-sm transition duration-300">
                            詳細を見る
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- ページネーションが必要な場合、Controllerのindexメソッドでpaginate()を使う --}}
            {{-- <div class="mt-8">
                {{ $teams->links() }}
            </div> --}}
        @endif
    </div>
</x-app-layout>