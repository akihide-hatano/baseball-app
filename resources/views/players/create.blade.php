<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('選手新規作成') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('players.store') }}">
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

                        <div class="mb-4">
                            <x-input-label for="player_name" :value="__('選手名')" />
                            <x-text-input id="player_name" class="block mt-1 w-full" type="text" name="player_name" :value="old('player_name')" required autofocus />
                            <x-input-error :messages="$errors->get('player_name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="jersey_number" :value="__('背番号')" />
                            <x-text-input id="jersey_number" class="block mt-1 w-full" type="number" name="jersey_number" :value="old('jersey_number')" />
                            <x-input-error :messages="$errors->get('jersey_number')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="date_of_birth" :value="__('生年月日')" />
                            <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth" :value="old('date_of_birth')" />
                            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                        </div>

                        {{-- 新しいカラムの入力フィールドを追加 --}}
                        <div class="mb-4">
                            <x-input-label for="height" :value="__('身長 (cm)')" />
                            <x-text-input id="height" class="block mt-1 w-full" type="number" name="height" :value="old('height')" />
                            <x-input-error :messages="$errors->get('height')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="weight" :value="__('体重 (kg)')" />
                            <x-text-input id="weight" class="block mt-1 w-full" type="number" name="weight" :value="old('weight')" />
                            <x-input-error :messages="$errors->get('weight')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="specialty" :value="__('特技/特徴')" />
                            <x-text-input id="specialty" class="block mt-1 w-full" type="text" name="specialty" :value="old('specialty')" />
                            <x-input-error :messages="$errors->get('specialty')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('選手説明')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="hometown" :value="__('出身地')" />
                            <x-text-input id="hometown" class="block mt-1 w-full" type="text" name="hometown" :value="old('hometown')" />
                            <x-input-error :messages="$errors->get('hometown')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="team_id" :value="__('所属チーム')" />
                            <select id="team_id" name="team_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">チームを選択してください</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->team_name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('team_id')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="role" :value="__('役割')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">役割を選択してください</option>
                                @foreach ($playerRoles as $value => $label)
                                    <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="year" :value="__('成績入力年度')" />
                            <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year', $currentYear)" required />
                            <x-input-error :messages="$errors->get('year')" class="mt-2" />
                        </div>

                        <div id="batting-stats-fields" class="mt-6 p-4 border rounded-md shadow-sm bg-gray-50 {{ old('role') == '野手' ? '' : 'hidden' }}">
                            <h3 class="font-semibold text-lg text-gray-700 mb-4">{{ __('打撃成績') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="batting_games" :value="__('出場試合数')" />
                                    <x-text-input id="batting_games" class="block mt-1 w-full" type="number" name="batting_games" :value="old('batting_games')" />
                                    <x-input-error :messages="$errors->get('batting_games')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="at_bats" :value="__('打席数')" />
                                    <x-text-input id="at_bats" class="block mt-1 w-full" type="number" name="at_bats" :value="old('at_bats')" />
                                    <x-input-error :messages="$errors->get('at_bats')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="runs" :value="__('得点')" />
                                    <x-text-input id="runs" class="block mt-1 w-full" type="number" name="runs" :value="old('runs')" />
                                    <x-input-error :messages="$errors->get('runs')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="hits" :value="__('安打')" />
                                    <x-text-input id="hits" class="block mt-1 w-full" type="number" name="hits" :value="old('hits')" />
                                    <x-input-error :messages="$errors->get('hits')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="doubles" :value="__('二塁打')" />
                                    <x-text-input id="doubles" class="block mt-1 w-full" type="number" name="doubles" :value="old('doubles')" />
                                    <x-input-error :messages="$errors->get('doubles')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="triples" :value="__('三塁打')" />
                                    <x-text-input id="triples" class="block mt-1 w-full" type="number" name="triples" :value="old('triples')" />
                                    <x-input-error :messages="$errors->get('triples')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="home_runs" :value="__('本塁打')" />
                                    <x-text-input id="home_runs" class="block mt-1 w-full" type="number" name="home_runs" :value="old('home_runs')" />
                                    <x-input-error :messages="$errors->get('home_runs')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="rbi" :value="__('打点')" />
                                    <x-text-input id="rbi" class="block mt-1 w-full" type="number" name="rbi" :value="old('rbi')" />
                                    <x-input-error :messages="$errors->get('rbi')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="stolen_bases" :value="__('盗塁')" />
                                    <x-text-input id="stolen_bases" class="block mt-1 w-full" type="number" name="stolen_bases" :value="old('stolen_bases')" />
                                    <x-input-error :messages="$errors->get('stolen_bases')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="caught_stealing" :value="__('盗塁死')" />
                                    <x-text-input id="caught_stealing" class="block mt-1 w-full" type="number" name="caught_stealing" :value="old('caught_stealing')" />
                                    <x-input-error :messages="$errors->get('caught_stealing')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="walks" :value="__('四球')" />
                                    <x-text-input id="walks" class="block mt-1 w-full" type="number" name="walks" :value="old('walks')" />
                                    <x-input-error :messages="$errors->get('walks')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="strikeouts" :value="__('三振')" />
                                    <x-text-input id="strikeouts" class="block mt-1 w-full" type="number" name="strikeouts" :value="old('strikeouts')" />
                                    <x-input-error :messages="$errors->get('strikeouts')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="sacrifice_hits" :value="__('犠打')" />
                                    <x-text-input id="sacrifice_hits" class="block mt-1 w-full" type="number" name="sacrifice_hits" :value="old('sacrifice_hits')" />
                                    <x-input-error :messages="$errors->get('sacrifice_hits')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="sacrifice_flies" :value="__('犠飛')" />
                                    <x-text-input id="sacrifice_flies" class="block mt-1 w-full" type="number" name="sacrifice_flies" :value="old('sacrifice_flies')" />
                                    <x-input-error :messages="$errors->get('sacrifice_flies')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="batting_average" :value="__('打率 (例: 0.300)')" />
                                    <x-text-input id="batting_average" class="block mt-1 w-full" type="text" name="batting_average" :value="old('batting_average')" step="0.001" placeholder="0.XXX" />
                                    <x-input-error :messages="$errors->get('batting_average')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div id="pitching-stats-fields" class="mt-6 p-4 border rounded-md shadow-sm bg-gray-50 {{ old('role') == '投手' ? '' : 'hidden' }}">
                            <h3 class="font-semibold text-lg text-gray-700 mb-4">{{ __('投球成績') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="pitching_games" :value="__('登板試合数')" />
                                    <x-text-input id="pitching_games" class="block mt-1 w-full" type="number" name="pitching_games" :value="old('pitching_games')" />
                                    <x-input-error :messages="$errors->get('pitching_games')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="wins" :value="__('勝利')" />
                                    <x-text-input id="wins" class="block mt-1 w-full" type="number" name="wins" :value="old('wins')" />
                                    <x-input-error :messages="$errors->get('wins')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="losses" :value="__('敗戦')" />
                                    <x-text-input id="losses" class="block mt-1 w-full" type="number" name="losses" :value="old('losses')" />
                                    <x-input-error :messages="$errors->get('losses')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="saves" :value="__('セーブ')" />
                                    <x-text-input id="saves" class="block mt-1 w-full" type="number" name="saves" :value="old('saves')" />
                                    <x-input-error :messages="$errors->get('saves')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="holds" :value="__('ホールド')" />
                                    <x-text-input id="holds" class="block mt-1 w-full" type="number" name="holds" :value="old('holds')" />
                                    <x-input-error :messages="$errors->get('holds')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="innings_pitched_out" :value="__('投球回 (アウト数)')" />
                                    <x-text-input id="innings_pitched_out" class="block mt-1 w-full" type="number" name="innings_pitched_out" :value="old('innings_pitched_out')" />
                                    <x-input-error :messages="$errors->get('innings_pitched_out')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="hits_given" :value="__('被安打')" />
                                    <x-text-input id="hits_given" class="block mt-1 w-full" type="number" name="hits_given" :value="old('hits_given')" />
                                    <x-input-error :messages="$errors->get('hits_given')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="home_runs_given" :value="__('被本塁打')" />
                                    <x-text-input id="home_runs_given" class="block mt-1 w-full" type="number" name="home_runs_given" :value="old('home_runs_given')" />
                                    <x-input-error :messages="$errors->get('home_runs_given')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="walks_given" :value="__('与四球')" />
                                    <x-text-input id="walks_given" class="block mt-1 w-full" type="number" name="walks_given" :value="old('walks_given')" />
                                    <x-input-error :messages="$errors->get('walks_given')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="strikeouts_thrown" :value="__('奪三振')" />
                                    <x-text-input id="strikeouts_thrown" class="block mt-1 w-full" type="number" name="strikeouts_thrown" :value="old('strikeouts_thrown')" />
                                    <x-input-error :messages="$errors->get('strikeouts_thrown')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="earned_run_average" :value="__('防御率 (例: 2.50)')" />
                                    <x-text-input id="earned_run_average" class="block mt-1 w-full" type="text" name="earned_run_average" :value="old('earned_run_average')" step="0.01" placeholder="X.XX" />
                                    <x-input-error :messages="$errors->get('earned_run_average')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('登録') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const roleSelect = document.getElementById('role');
            const battingFields = document.getElementById('batting-stats-fields');
            const pitchingFields = document.getElementById('pitching-stats-fields');

            function toggleStatsFields() {
                if (roleSelect.value === '野手') {
                    battingFields.classList.remove('hidden');
                    pitchingFields.classList.add('hidden');
                    // 野手選択時、投手関連のフィールドを無効にする（フォーム送信から除外）
                    pitchingFields.querySelectorAll('input, select, textarea').forEach(field => {
                        field.setAttribute('disabled', 'disabled');
                    });
                    // 野手関連のフィールドを有効にする
                    battingFields.querySelectorAll('input, select, textarea').forEach(field => {
                        field.removeAttribute('disabled');
                    });
                } else if (roleSelect.value === '投手') {
                    pitchingFields.classList.remove('hidden');
                    battingFields.classList.add('hidden');
                    // 投手選択時、野手関連のフィールドを無効にする
                    battingFields.querySelectorAll('input, select, textarea').forEach(field => {
                        field.setAttribute('disabled', 'disabled');
                    });
                    // 投手関連のフィールドを有効にする
                    pitchingFields.querySelectorAll('input, select, textarea').forEach(field => {
                        field.removeAttribute('disabled');
                    });
                } else { // 役割が未選択の場合など
                    battingFields.classList.add('hidden');
                    pitchingFields.classList.add('hidden');
                    // どちらでもない場合、全て無効にする
                    battingFields.querySelectorAll('input, select, textarea').forEach(field => {
                        field.setAttribute('disabled', 'disabled');
                    });
                    pitchingFields.querySelectorAll('input, select, textarea').forEach(field => {
                        field.setAttribute('disabled', 'disabled');
                    });
                }
            }

            // 初期表示
            toggleStatsFields();

            // 役割選択が変更されたときに切り替え
            roleSelect.addEventListener('change', toggleStatsFields);
        });
    </script>
    @endpush
</x-app-layout>