<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('選手情報') }} > {{ $player->name }} > {{ __('投手能力の追加') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        {{ $player->name }} の新しい投手能力を追加
                    </div>
                    <div class="mt-6 text-gray-500">
                        以下のフォームに必要事項を入力して、投手能力データを登録してください。
                    </div>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('players.pitching-abilities.store', $player->id) }}">
                        @csrf

                        {{-- 隠しフィールド: player_id --}}
                        <input type="hidden" name="player_id" value="{{ $player->id }}">

                        {{-- 年度 --}}
                        <div class="mb-4">
                            <label for="year" class="block text-gray-700 text-sm font-bold mb-2">年度:</label>
                            <select name="year" id="year" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('year') border-red-500 @enderror">
                                <option value="">選択してください</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                            @error('year')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 球速 (average_velocity) --}}
                        <div class="mb-4">
                            <label for="average_velocity" class="block text-gray-700 text-sm font-bold mb-2">球速 (km/h):</label>
                            <input type="number" name="average_velocity" id="average_velocity" min="50" max="200" step="1"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('average_velocity') border-red-500 @enderror"
                                value="{{ old('average_velocity') }}">
                            @error('average_velocity')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- スタミナ (pitch_stamina) --}}
                        <div class="mb-4">
                            <label for="pitch_stamina" class="block text-gray-700 text-sm font-bold mb-2">スタミナ (1-99):</label>
                            <input type="number" name="pitch_stamina" id="pitch_stamina" min="1" max="99"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('pitch_stamina') border-red-500 @enderror"
                                value="{{ old('pitch_stamina') }}">
                            @error('pitch_stamina')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- コントロール (pitch_control) --}}
                        <div class="mb-4">
                            <label for="pitch_control" class="block text-gray-700 text-sm font-bold mb-2">コントロール (1-99):</label>
                            <input type="number" name="pitch_control" id="pitch_control" min="1" max="99"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('pitch_control') border-red-500 @enderror"
                                value="{{ old('pitch_control') }}">
                            @error('pitch_control')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 変化球の種類とレベル (pitch_type_1 から pitch_type_7) --}}
                        <h3 class="text-lg font-semibold mt-6 mb-3 text-gray-800">変化球の種類とレベル</h3>
                        @for ($i = 1; $i <= 7; $i++)
                            @php
                                // コントローラーから渡された固定リスト $fixedPitchTypes を使用
                                // $i は1から始まるので、配列のインデックスは $i - 1
                                $currentPitchName = $allPitchTypes[$i - 1] ?? '未設定'; // 範囲外の場合は'未設定'
                            @endphp
                            <div class="mb-4 p-3 border rounded-md shadow-sm">
                                <label class="block text-gray-700 text-sm font-bold mb-2">変化球 {{ $i }}:</label>
                                <div class="flex space-x-4">
                                    <div class="flex-1">
                                        <label class="block text-gray-600 text-xs mb-1">球種名:</label>
                                        {{-- selectをspanとhidden inputに置き換え --}}
                                        <span class="block w-full py-2 px-3 text-gray-700 leading-tight">{{ $currentPitchName }}</span>
                                        <input type="hidden" name="pitch_type_{{ $i }}_name" value="{{ $currentPitchName }}">
                                        {{-- @error('pitch_type_' . $i . '_name') はhidden inputなので通常は不要ですが、残しておいても害はありません --}}
                                        @error('pitch_type_' . $i . '_name')
                                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="w-20">
                                        <label for="pitch_type_{{ $i }}_level" class="block text-gray-600 text-xs mb-1">レベル (0-7):</label>
                                        <input type="number" name="pitch_type_{{ $i }}_level" id="pitch_type_{{ $i }}_level" min="0" max="7"
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('pitch_type_' . $i . '_level') border-red-500 @enderror"
                                            value="{{ old('pitch_type_' . $i . '_level',0 ) }}">
                                        @error('pitch_type_' . $i . '_level')
                                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endfor

                        {{-- 総合ランク (overall_rank) --}}
                        <div class="mb-4">
                            <label for="overall_rank" class="block text-gray-700 text-sm font-bold mb-2">総合ランク (1-99):</label>
                            <input type="number" name="overall_rank" id="overall_rank" min="1" max="99"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('overall_rank') border-red-500 @enderror"
                                value="{{ old('overall_rank') }}">
                            @error('overall_rank')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 特殊能力 (special_skills) --}}
                        <div class="mb-4">
                            <label for="special_skills" class="block text-gray-700 text-sm font-bold mb-2">特殊能力:</label>
                            <textarea name="special_skills" id="special_skills" rows="3"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('special_skills') border-red-500 @enderror"
                                placeholder="例: ノビA, キレ○">{{ old('special_skills') }}</textarea>
                            @error('special_skills')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 送信ボタン --}}
                        <div class="flex items-center justify-between mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                投手能力を登録
                            </button>
                            <a href="{{ route('players.show', $player->id) }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                キャンセル
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>