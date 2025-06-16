<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新規試合登録') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{-- フォームのIDを追加して、将来的にJavaScriptで操作しやすくする --}}
                    <form method="POST" action="{{ route('games.store') }}" id="gameCreateForm">
                        @csrf

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
                                        <option value="{{ $team->id }}" {{ old('home_team_id') == $team->id ? 'selected' : '' }}>
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
                                        <option value="{{ $team->id }}" {{ old('away_team_id') == $team->id ? 'selected' : '' }}>
                                            {{ $team->team_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('away_team_id')" class="mt-2" />
                            </div>

                            {{-- 試合日 --}}
                            <div>
                                <x-input-label for="game_date" :value="__('試合日')" />
                                <x-text-input id="game_date" class="block mt-1 w-full" type="date" name="game_date" :value="old('game_date', $currentDate)" required />
                                <x-input-error :messages="$errors->get('game_date')" class="mt-2" />
                            </div>

                            {{-- 試合時間 --}}
                            <div>
                                <x-input-label for="game_time" :value="__('試合時間')" />
                                <x-text-input id="game_time" class="block mt-1 w-full" type="time" name="game_time" :value="old('game_time', $currentTime)" required />
                                <x-input-error :messages="$errors->get('game_time')" class="mt-2" />
                            </div>

                            {{-- 球場名（SELECTに変更） --}}
                            <div>
                                <x-input-label for="location" :value="__('球場名')" />
                                <select id="stadium" name="stadium" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">選択してください</option>
                                    @foreach ($stadiums as $stadiumName)
                                        <option value="{{ $stadiumName }}" {{ old('stadium') == $stadiumName ? 'selected' : '' }}>
                                            {{ $stadiumName }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>

                            {{-- ホームスコア --}}
                            <div>
                                <x-input-label for="home_score" :value="__('ホームチーム スコア')" />
                                <x-text-input id="home_score" class="block mt-1 w-full" type="number" name="home_score" :value="old('home_score')" min="0" />
                                <x-input-error :messages="$errors->get('home_score')" class="mt-2" />
                            </div>

                            {{-- アウェイスコア --}}
                            <div>
                                <x-input-label for="away_score" :value="__('アウェイチーム スコア')" />
                                <x-text-input id="away_score" class="block mt-1 w-full" type="number" name="away_score" :value="old('away_score')" min="0" />
                                <x-input-error :messages="$errors->get('away_score')" class="mt-2" />
                            </div>

                            {{-- MVP選手 --}}
                            <div>
                                <x-input-label for="mvp_player_id" :value="__('MVP選手 (任意)')" />
                                <select id="mvp_player_id" name="mvp_player_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">選択しない</option>
                                    @foreach ($players as $player)
                                        <option value="{{ $player->id }}" {{ old('mvp_player_id') == $player->id ? 'selected' : '' }}>
                                            {{ $player->name }} ({{ $player->team->team_name ?? '不明' }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('mvp_player_id')" class="mt-2" />
                            </div>

                            {{-- 勝利投手 --}}
                            <div>
                                <x-input-label for="pitcher_of_record_id" :value="__('勝利投手 (任意)')" />
                                <select id="pitcher_of_record_id" name="pitcher_of_record_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">選択しない</option>
                                    @foreach ($players as $player)
                                        {{-- 投手に絞る場合は $player->role == '投手' などでフィルタリング --}}
                                        <option value="{{ $player->id }}" {{ old('pitcher_of_record_id') == $player->id ? 'selected' : '' }}>
                                            {{ $player->name }} ({{ $player->team->team_name ?? '不明' }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('pitcher_of_record_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('試合を登録') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>