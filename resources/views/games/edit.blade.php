<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('試合情報編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{-- フォームの送信先は games.update ルート、HTTPメソッドは PATCH --}}
                    <form method="POST" action="{{ route('games.update', $game->id) }}" id="gameEditForm">
                        @csrf
                        @method('PATCH') {{-- PUTでも可ですが、部分更新にはPATCHが推奨されます --}}

                        @if ($errors->any())
                            <div class="mb-4 text-red-600 p-4 bg-red-100 border border-red-400 rounded">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- ホームチーム選択 --}}
                            <div>
                                <x-input-label for="home_team_id" :value="__('ホームチーム')" />
                                <select id="home_team_id" name="home_team_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">選択してください</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('home_team_id', $game->home_team_id) == $team->id ? 'selected' : '' }}>
                                            {{ $team->team_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('home_team_id')" class="mt-2" />
                            </div>

                            {{-- アウェイチーム選択 --}}
                            <div>
                                <x-input-label for="away_team_id" :value="__('アウェイチーム')" />
                                <select id="away_team_id" name="away_team_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">選択してください</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('away_team_id', $game->away_team_id) == $team->id ? 'selected' : '' }}>
                                            {{ $team->team_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('away_team_id')" class="mt-2" />
                            </div>

                            {{-- 試合日 --}}
                            <div>
                                <x-input-label for="game_date" :value="__('試合日')" />
                                {{-- $game->game_date はCarbonインスタンスになっているはずなのでformat()で表示 --}}
                                <x-text-input id="game_date" class="block mt-1 w-full" type="date" name="game_date" :value="old('game_date', $game->game_date ? $game->game_date->format('Y-m-d') : '')" required />
                                <x-input-error :messages="$errors->get('game_date')" class="mt-2" />
                            </div>

                            {{-- 試合時間 --}}
                            <div>
                                <x-input-label for="game_time" :value="__('試合時間')" />
                                {{-- $game->game_time もCarbonインスタンスになっているはずなのでformat()で表示 --}}
                                <x-text-input id="game_time" class="block mt-1 w-full" type="time" name="game_time" :value="old('game_time', $game->game_time ? $game->game_time->format('H:i') : '')" required />
                                <x-input-error :messages="$errors->get('game_time')" class="mt-2" />
                            </div>

                            {{-- 球場名（SELECT） --}}
                            <div>
                                <x-input-label for="stadium" :value="__('球場名')" />
                                <select id="stadium" name="stadium" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">選択してください</option>
                                    {{-- $stadiums は GameControllerで渡されるユニークな球場名のリスト --}}
                                    @foreach ($stadiums as $stadiumName)
                                        <option value="{{ $stadiumName }}" {{ old('stadium', $game->stadium) == $stadiumName ? 'selected' : '' }}>
                                            {{ $stadiumName }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('stadium')" class="mt-2" />
                            </div>

                            {{-- ホームスコア --}}
                            <div>
                                <x-input-label for="home_score" :value="__('ホームチーム スコア')" />
                                <x-text-input id="home_score" class="block mt-1 w-full" type="number" name="home_score" :value="old('home_score', $game->home_score)" min="0" required />
                                <x-input-error :messages="$errors->get('home_score')" class="mt-2" />
                            </div>

                            {{-- アウェイスコア --}}
                            <div>
                                <x-input-label for="away_score" :value="__('アウェイチーム スコア')" />
                                <x-text-input id="away_score" class="block mt-1 w-full" type="number" name="away_score" :value="old('away_score', $game->away_score)" min="0" required />
                                <x-input-error :messages="$errors->get('away_score')" class="mt-2" />
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('試合情報を更新') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- 外部JavaScriptファイルを読み込む --}}
    <script src="{{ asset('js/player-form-toggle.js') }}"></script>
</x-app-layout>