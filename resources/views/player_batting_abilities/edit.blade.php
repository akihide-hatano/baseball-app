<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $player->name }} の打撃能力 ({{ $playerBattingAbility->year }}年) を編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- PATCHメソッドで更新するためのフォーム --}}
                    <form method="POST" action="{{ route('players.batting-abilities.update', ['player' => $player->id, 'playerBattingAbility' => $playerBattingAbility->id]) }}">
                        @csrf
                        @method('PATCH') {{-- PATCHメソッドを指定 --}}

                        @if ($errors->any())
                            <div class="mb-4 p-3 bg-red-100 text-red-700 border border-red-400 rounded">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-4">
                            <x-input-label for="year" :value="__('年度')" />
                            <select id="year" name="year" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">選択してください</option>
                                @foreach($years as $year)
                                    {{-- old()があればold()を優先、なければ既存の能力の年を選択 --}}
                                    <option value="{{ $year }}" {{ (old('year', $playerBattingAbility->year) == $year) ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('year')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="contact_power" :value="__('ミート力')" />
                            {{-- old()がなければ既存の能力の値を表示 --}}
                            <x-text-input id="contact_power" class="block mt-1 w-full" type="number" name="contact_power" :value="old('contact_power', $playerBattingAbility->contact_power)" min="0" max="100" />
                            <x-input-error :messages="$errors->get('contact_power')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="power" :value="__('パワー')" />
                            <x-text-input id="power" class="block mt-1 w-full" type="number" name="power" :value="old('power', $playerBattingAbility->power)" min="0" max="100" />
                            <x-input-error :messages="$errors->get('power')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="speed" :value="__('走力')" />
                            <x-text-input id="speed" class="block mt-1 w-full" type="number" name="speed" :value="old('speed', $playerBattingAbility->speed)" min="0" max="100" />
                            <x-input-error :messages="$errors->get('speed')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="fielding" :value="__('守備力')" />
                            <x-text-input id="fielding" class="block mt-1 w-full" type="number" name="fielding" :value="old('fielding', $playerBattingAbility->fielding)" min="0" max="100" />
                            <x-input-error :messages="$errors->get('fielding')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="throwing" :value="__('肩力')" />
                            <x-text-input id="throwing" class="block mt-1 w-full" type="number" name="throwing" :value="old('throwing', $playerBattingAbility->throwing)" min="0" max="100" />
                            <x-input-error :messages="$errors->get('throwing')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="reaction" :value="__('捕球/反応力')" />
                            <x-text-input id="reaction" class="block mt-1 w-full" type="number" name="reaction" :value="old('reaction', $playerBattingAbility->reaction)" min="0" max="100" />
                            <x-input-error :messages="$errors->get('reaction')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="overall_rank" :value="__('総合能力ランク')" />
                            <x-text-input id="overall_rank" class="block mt-1 w-full" type="number" name="overall_rank" :value="old('overall_rank', $playerBattingAbility->overall_rank)" min="0" max="100" />
                            <x-input-error :messages="$errors->get('overall_rank')" class="mt-2" />
                        </div>

                        <div class="mb-4"> {{-- このdivも念のため含めます --}}
                            <x-input-label for="special_skills" :value="__('特殊能力 (カンマ区切り)')" />
                            {{-- ★★★ ここを修正 ★★★ --}}
                            <textarea id="special_skills" name="special_skills" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('special_skills', $playerBattingAbility->special_skills ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('special_skills')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('更新') }}
                            </x-primary-button>
                            <x-secondary-button class="ms-4" onclick="event.preventDefault(); window.history.back();">
                                {{ __('キャンセル') }}
                            </x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>