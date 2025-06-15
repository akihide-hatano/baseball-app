<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            チーム情報を編集
        </h2>
    </x-slot>

    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-600">チーム情報の編集: {{ $team->team_name }}</h1>

        <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-md">
            {{-- フォームの送信先は teams.update ルート --}}
            <form action="{{ route('teams.update', $team->id) }}" method="POST">
                @csrf
                @method('PATCH') {{-- PATCHメソッドを指定 --}}

                <div class="mb-4">
                    <label for="league_id" class="block text-gray-700 text-sm font-bold mb-2">リーグ:</label>
                    <select name="league_id" id="league_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('league_id') border-red-500 @enderror">
                        <option value="">リーグを選択してください</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}" {{ (old('league_id') == $league->id || $team->league_id == $league->id) ? 'selected' : '' }}>
                                {{ $league->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('league_id')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="team_name" class="block text-gray-700 text-sm font-bold mb-2">チーム名:</label>
                    <input type="text" name="team_name" id="team_name" value="{{ old('team_name', $team->team_name) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('team_name') border-red-500 @enderror">
                    @error('team_name')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="team_nickname" class="block text-gray-700 text-sm font-bold mb-2">ニックネーム:</label>
                    <input type="text" name="team_nickname" id="team_nickname" value="{{ old('team_nickname', $team->team_nickname) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('team_nickname') border-red-500 @enderror">
                    @error('team_nickname')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="location" class="block text-gray-700 text-sm font-bold mb-2">本拠地:</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $team->location) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('location') border-red-500 @enderror">
                    @error('location')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="founded_at" class="block text-gray-700 text-sm font-bold mb-2">設立日:</label>
                    <input type="date" name="founded_at" id="founded_at" value="{{ old('founded_at', $team->founded_at->format('Y-m-d')) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('founded_at') border-red-500 @enderror">
                    @error('founded_at')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        更新
                    </button>
                    <a href="{{ route('teams.show', $team->id) }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        詳細に戻る
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>