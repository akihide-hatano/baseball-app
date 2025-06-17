<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('選手情報編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('players.update', $player->id) }}" id="playerCreationForm">
                        @csrf
                        @method('PATCH') {{-- PUTメソッドを使用 --}}

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
                            <x-text-input id="player_name" class="block mt-1 w-full" type="text" name="player_name" :value="old('player_name', $player->name)" required autofocus />
                            <x-input-error :messages="$errors->get('player_name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="jersey_number" :value="__('背番号')" />
                            <x-text-input id="jersey_number" class="block mt-1 w-full" type="number" name="jersey_number" :value="old('jersey_number', $player->jersey_number)" />
                            <x-input-error :messages="$errors->get('jersey_number')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="date_of_birth" :value="__('生年月日')" />
                            <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth" :value="old('date_of_birth', $player->date_of_birth ? $player->date_of_birth->format('Y-m-d') : '')" />
                            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="height" :value="__('身長 (cm)')" />
                            <x-text-input id="height" class="block mt-1 w-full" type="number" name="height" :value="old('height', $player->height)" />
                            <x-input-error :messages="$errors->get('height')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="weight" :value="__('体重 (kg)')" />
                            <x-text-input id="weight" class="block mt-1 w-full" type="number" name="weight" :value="old('weight', $player->weight)" />
                            <x-input-error :messages="$errors->get('weight')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="specialty" :value="__('特技/特徴')" />
                            <x-text-input id="specialty" class="block mt-1 w-full" type="text" name="specialty" :value="old('specialty', $player->specialty)" />
                            <x-input-error :messages="$errors->get('specialty')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('選手説明')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $player->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="hometown" :value="__('出身地')" />
                            <x-text-input id="hometown" class="block mt-1 w-full" type="text" name="hometown" :value="old('hometown', $player->hometown)" />
                            <x-input-error :messages="$errors->get('hometown')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="team_id" :value="__('所属チーム')" />
                            <select id="team_id" name="team_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">チームを選択してください</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('team_id', $player->team_id) == $team->id ? 'selected' : '' }}>
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
                                    <option value="{{ $value }}" {{ old('role', $player->role) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="year" :value="__('成績入力年度')" />
                            {{-- 編集時、最新の成績の年度、または現在の年をデフォルトにする --}}
                            <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year', ($player->role == '野手' && $player->yearlyBattingStats->first()) ? $player->yearlyBattingStats->first()->year : (($player->role == '投手' && $player->yearlyPitchingStats->first()) ? $player->yearlyPitchingStats->first()->year : $currentYear))" required />
                            <x-input-error :messages="$errors->get('year')" class="mt-2" />
                        </div>

                        {{-- 打撃成績フィールド --}}
                        {{-- old('role', $player->role) で初期表示をコントロール --}}
                        <div id="batting-stats-fields" class="mt-6 p-4 border rounded-md shadow-sm bg-gray-50 {{ old('role', $player->role) == '野手' ? '' : 'hidden' }}">
                            <h3 class="font-semibold text-lg text-gray-700 mb-4">{{ __('打撃成績') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @php
                                    $battingStat = $player->yearlyBattingStats->first();
                                @endphp
                                <div>
                                    <x-input-label for="games" :value="__('出場試合数')" />
                                    <x-text-input id="games" class="block mt-1 w-full" type="number" name="games" :value="old('games', $battingStat->games ?? '')" />
                                    <x-input-error :messages="$errors->get('games')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="plate_appearances" :value="__('打席数')" />
                                    <x-text-input id="plate_appearances" class="block mt-1 w-full" type="number" name="plate_appearances" :value="old('plate_appearances', $battingStat->plate_appearances ?? '')" />
                                    <x-input-error :messages="$errors->get('plate_appearances')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="at_bats" :value="__('打数')" />
                                    <x-text-input id="at_bats" class="block mt-1 w-full" type="number" name="at_bats" :value="old('at_bats', $battingStat->at_bats ?? '')" />
                                    <x-input-error :messages="$errors->get('at_bats')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="runs_scored" :value="__('得点')" />
                                    <x-text-input id="runs_scored" class="block mt-1 w-full" type="number" name="runs_scored" :value="old('runs_scored', $battingStat->runs_scored ?? '')" />
                                    <x-input-error :messages="$errors->get('runs_scored')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="hits" :value="__('安打')" />
                                    <x-text-input id="hits" class="block mt-1 w-full" type="number" name="hits" :value="old('hits', $battingStat->hits ?? '')" />
                                    <x-input-error :messages="$errors->get('hits')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="doubles" :value="__('二塁打')" />
                                    <x-text-input id="doubles" class="block mt-1 w-full" type="number" name="doubles" :value="old('doubles', $battingStat->doubles ?? '')" />
                                    <x-input-error :messages="$errors->get('doubles')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="triples" :value="__('三塁打')" />
                                    <x-text-input id="triples" class="block mt-1 w-full" type="number" name="triples" :value="old('triples', $battingStat->triples ?? '')" />
                                    <x-input-error :messages="$errors->get('triples')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="home_runs" :value="__('本塁打')" />
                                    <x-text-input id="home_runs" class="block mt-1 w-full" type="number" name="home_runs" :value="old('home_runs', $battingStat->home_runs ?? '')" />
                                    <x-input-error :messages="$errors->get('home_runs')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="rbi" :value="__('打点')" />
                                    <x-text-input id="rbi" class="block mt-1 w-full" type="number" name="rbi" :value="old('rbi', $battingStat->rbi ?? '')" />
                                    <x-input-error :messages="$errors->get('rbi')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="stolen_bases" :value="__('盗塁')" />
                                    <x-text-input id="stolen_bases" class="block mt-1 w-full" type="number" name="stolen_bases" :value="old('stolen_bases', $battingStat->stolen_bases ?? '')" />
                                    <x-input-error :messages="$errors->get('stolen_bases')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="caught_stealing" :value="__('盗塁死')" />
                                    <x-text-input id="caught_stealing" class="block mt-1 w-full" type="number" name="caught_stealing" :value="old('caught_stealing', $battingStat->caught_stealing ?? '')" />
                                    <x-input-error :messages="$errors->get('caught_stealing')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="walks" :value="__('四球')" />
                                    <x-text-input id="walks" class="block mt-1 w-full" type="number" name="walks" :value="old('walks', $battingStat->walks ?? '')" />
                                    <x-input-error :messages="$errors->get('walks')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="strikeouts" :value="__('三振')" />
                                    <x-text-input id="strikeouts" class="block mt-1 w-full" type="number" name="strikeouts" :value="old('strikeouts', $battingStat->strikeouts ?? '')" />
                                    <x-input-error :messages="$errors->get('strikeouts')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="sac_bunts" :value="__('犠打')" />
                                    <x-text-input id="sac_bunts" class="block mt-1 w-full" type="number" name="sac_bunts" :value="old('sac_bunts', $battingStat->sac_bunts ?? '')" />
                                    <x-input-error :messages="$errors->get('sac_bunts')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="sac_flies" :value="__('犠飛')" />
                                    <x-text-input id="sac_flies" class="block mt-1 w-full" type="number" name="sac_flies" :value="old('sac_flies', $battingStat->sac_flies ?? '')" />
                                    <x-input-error :messages="$errors->get('sac_flies')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="double_plays" :value="__('併殺打')" />
                                    <x-text-input id="double_plays" class="block mt-1 w-full" type="number" name="double_plays" :value="old('double_plays', $battingStat->double_plays ?? '')" />
                                    <x-input-error :messages="$errors->get('double_plays')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="errors" :value="__('失策')" />
                                    <x-text-input id="errors" class="block mt-1 w-full" type="number" name="errors" :value="old('errors', $battingStat->errors ?? '')" />
                                    <x-input-error :messages="$errors->get('errors')" class="mt-2" />
                                </div>
                                {{-- 打率はコントローラーで計算されるため、readonlyにする --}}
                                <div>
                                    <x-input-label for="batting_average" :value="__('打率 (自動計算)')" />
                                    <x-text-input id="batting_average" class="block mt-1 w-full bg-gray-100" type="text" name="batting_average" :value="old('batting_average', sprintf('%.3f', $battingStat->batting_average ?? 0.000))" readonly />
                                    <x-input-error :messages="$errors->get('batting_average')" class="mt-2" />
                                </div>
                                {{-- 出塁率、長打率、OPSも同様にreadonlyにする --}}
                                <div>
                                    <x-input-label for="on_base_percentage" :value="__('出塁率 (自動計算)')" />
                                    <x-text-input id="on_base_percentage" class="block mt-1 w-full bg-gray-100" type="text" name="on_base_percentage" :value="old('on_base_percentage', sprintf('%.3f', $battingStat->on_base_percentage ?? 0.000))" readonly />
                                    <x-input-error :messages="$errors->get('on_base_percentage')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="slugging_percentage" :value="__('長打率 (自動計算)')" />
                                    <x-text-input id="slugging_percentage" class="block mt-1 w-full bg-gray-100" type="text" name="slugging_percentage" :value="old('slugging_percentage', sprintf('%.3f', $battingStat->slugging_percentage ?? 0.000))" readonly />
                                    <x-input-error :messages="$errors->get('slugging_percentage')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="ops" :value="__('OPS (自動計算)')" />
                                    <x-text-input id="ops" class="block mt-1 w-full bg-gray-100" type="text" name="ops" :value="old('ops', sprintf('%.3f', $battingStat->ops ?? 0.000))" readonly />
                                    <x-input-error :messages="$errors->get('ops')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="ops_plus" :value="__('OPS+ (例: 100.0)')" />
                                    <x-text-input id="ops_plus" class="block mt-1 w-full" type="text" name="ops_plus" :value="old('ops_plus', $battingStat->ops_plus ?? '')" step="0.1" placeholder="X.X" />
                                    <x-input-error :messages="$errors->get('ops_plus')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="wrc_plus" :value="__('wRC+ (例: 100.0)')" />
                                    <x-text-input id="wrc_plus" class="block mt-1 w-full" type="text" name="wrc_plus" :value="old('wrc_plus', $battingStat->wrc_plus ?? '')" step="0.1" placeholder="X.X" />
                                    <x-input-error :messages="$errors->get('wrc_plus')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- 投球成績フィールド --}}
                        {{-- old('role', $player->role) で初期表示をコントロール --}}
                        <div id="pitching-stats-fields" class="mt-6 p-4 border rounded-md shadow-sm bg-gray-50 {{ old('role', $player->role) == '投手' ? '' : 'hidden' }}">
                            <h3 class="font-semibold text-lg text-gray-700 mb-4">{{ __('投球成績') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @php
                                    $pitchingStat = $player->yearlyPitchingStats->first();
                                @endphp
                                <div>
                                    <x-input-label for="games" :value="__('登板試合数')" />
                                    <x-text-input id="games" class="block mt-1 w-full" type="number" name="games" :value="old('games', $pitchingStat->games ?? '')" />
                                    <x-input-error :messages="$errors->get('games')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="starts" :value="__('先発登板数')" />
                                    <x-text-input id="starts" class="block mt-1 w-full" type="number" name="starts" :value="old('starts', $pitchingStat->starts ?? '')" />
                                    <x-input-error :messages="$errors->get('starts')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="wins" :value="__('勝利')" />
                                    <x-text-input id="wins" class="block mt-1 w-full" type="number" name="wins" :value="old('wins', $pitchingStat->wins ?? '')" />
                                    <x-input-error :messages="$errors->get('wins')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="losses" :value="__('敗戦')" />
                                    <x-text-input id="losses" class="block mt-1 w-full" type="number" name="losses" :value="old('losses', $pitchingStat->losses ?? '')" />
                                    <x-input-error :messages="$errors->get('losses')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="saves" :value="__('セーブ')" />
                                    <x-text-input id="saves" class="block mt-1 w-full" type="number" name="saves" :value="old('saves', $pitchingStat->saves ?? '')" />
                                    <x-input-error :messages="$errors->get('saves')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="holds" :value="__('ホールド')" />
                                    <x-text-input id="holds" class="block mt-1 w-full" type="number" name="holds" :value="old('holds', $pitchingStat->holds ?? '')" />
                                    <x-input-error :messages="$errors->get('holds')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="innings_pitched" :value="__('投球回 (小数可)')" />
                                    <x-text-input id="innings_pitched" class="block mt-1 w-full" type="number" name="innings_pitched" :value="old('innings_pitched', $pitchingStat->innings_pitched ?? '')" step="0.1" />
                                    <x-input-error :messages="$errors->get('innings_pitched')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="hits_allowed" :value="__('被安打')" />
                                    <x-text-input id="hits_allowed" class="block mt-1 w-full" type="number" name="hits_allowed" :value="old('hits_allowed', $pitchingStat->hits_allowed ?? '')" />
                                    <x-input-error :messages="$errors->get('hits_allowed')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="home_runs_allowed" :value="__('被本塁打')" />
                                    <x-text-input id="home_runs_allowed" class="block mt-1 w-full" type="number" name="home_runs_allowed" :value="old('home_runs_allowed', $pitchingStat->home_runs_allowed ?? '')" />
                                    <x-input-error :messages="$errors->get('home_runs_allowed')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="walks_allowed" :value="__('与四球')" />
                                    <x-text-input id="walks_allowed" class="block mt-1 w-full" type="number" name="walks_allowed" :value="old('walks_allowed', $pitchingStat->walks_allowed ?? '')" />
                                    <x-input-error :messages="$errors->get('walks_allowed')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="hit_by_pitch_allowed" :value="__('与死球')" />
                                    <x-text-input id="hit_by_pitch_allowed" class="block mt-1 w-full" type="number" name="hit_by_pitch_allowed" :value="old('hit_by_pitch_allowed', $pitchingStat->hit_by_pitch_allowed ?? '')" />
                                    <x-input-error :messages="$errors->get('hit_by_pitch_allowed')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="strikeouts_pitched" :value="__('奪三振')" />
                                    <x-text-input id="strikeouts_pitched" class="block mt-1 w-full" type="number" name="strikeouts_pitched" :value="old('strikeouts_pitched', $pitchingStat->strikeouts_pitched ?? '')" />
                                    <x-input-error :messages="$errors->get('strikeouts_pitched')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="runs_allowed" :value="__('失点')" />
                                    <x-text-input id="runs_allowed" class="block mt-1 w-full" type="number" name="runs_allowed" :value="old('runs_allowed', $pitchingStat->runs_allowed ?? '')" />
                                    <x-input-error :messages="$errors->get('runs_allowed')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="earned_runs" :value="__('自責点')" />
                                    <x-text-input id="earned_runs" class="block mt-1 w-full" type="number" name="earned_runs" :value="old('earned_runs', $pitchingStat->earned_runs ?? '')" />
                                    <x-input-error :messages="$errors->get('earned_runs')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="pitches_thrown" :value="__('総投球数')" />
                                    <x-text-input id="pitches_thrown" class="block mt-1 w-full" type="number" name="pitches_thrown" :value="old('pitches_thrown', $pitchingStat->pitches_thrown ?? '')" />
                                    <x-input-error :messages="$errors->get('pitches_thrown')" class="mt-2" />
                                </div>

                                {{-- 防御率、WHIP、K/BB は自動計算されるため readonly にする --}}
                                <div>
                                    <x-input-label for="earned_run_average" :value="__('防御率 (自動計算)')" />
                                    <x-text-input id="earned_run_average" class="block mt-1 w-full bg-gray-100" type="text" name="earned_run_average" :value="old('earned_run_average', sprintf('%.2f', $pitchingStat->earned_run_average ?? 0.00))" readonly />
                                    <x-input-error :messages="$errors->get('earned_run_average')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="whip" :value="__('WHIP (自動計算)')" />
                                    <x-text-input id="whip" class="block mt-1 w-full bg-gray-100" type="text" name="whip" :value="old('whip', sprintf('%.2f', $pitchingStat->whip ?? 0.00))" readonly />
                                    <x-input-error :messages="$errors->get('whip')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="strikeout_walk_ratio" :value="__('K/BB (自動計算)')" />
                                    <x-text-input id="strikeout_walk_ratio" class="block mt-1 w-full bg-gray-100" type="text" name="strikeout_walk_ratio" :value="old('strikeout_walk_ratio', sprintf('%.2f', $pitchingStat->strikeout_walk_ratio ?? 0.00))" readonly />
                                    <x-input-error :messages="$errors->get('strikeout_walk_ratio')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('更新') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- 外部JavaScriptファイルを読み込む --}}
    <script src="{{ asset('js/player-form-toggle.js') }}"></script>
    <script src="{{asset('js/form-confirmation.js')}}"></script>
</x-app-layout>