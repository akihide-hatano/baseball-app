<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $player->name }} の詳細
        </h2>
    </x-slot>

    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-extrabold mb-8 text-center text-indigo-800">{{ $player->name }}</h1>

        {{-- 選手基本情報 --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-indigo-700">基本情報</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                <p><strong>背番号:</strong> <span class="text-blue-700">{{ $player->jersey_number ?? '-' }}</span></p>
                <p><strong>所属チーム:</strong> <span class="text-blue-700">{{ $player->team->team_name ?? '不明' }}</span></p>
                <p><strong>役割:</strong> <span class="text-blue-700">{{ $player->role ?? '-' }}</span></p>
                <p><strong>生年月日:</strong> <span class="text-blue-700">{{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->format('Y年m月d日') : '不明' }}</span></p>
                <p><strong>身長:</strong> <span class="text-blue-700">{{ $player->height ?? '-' }} cm</span></p>
                <p><strong>体重:</strong> <span class="text-blue-700">{{ $player->weight ?? '-' }} kg</span></p>
                <p><strong>利き腕/投打:</strong> <span class="text-blue-700">{{ $player->specialty ?? '-' }}</span></p>
                <p class="md:col-span-2"><strong>出身地:</strong> <span class="text-blue-700">{{ $player->hometown ?? '-' }}</span></p>
                <p class="md:col-span-2"><strong>説明:</strong> <span class="text-blue-700">{{ $player->description ?? '-' }}</span></p>
            </div>
        </div>

        {{-- 打撃能力チャートと総合ランクチャートのFlexコンテナ --}}
        <div class="flex flex-col md:flex-row gap-8 mb-8">
            {{-- 選手打撃能力チャート (PlayerBattingAbility) --}}
            <div class="bg-white shadow-xl rounded-lg p-8 flex-1">
                <h2 class="text-2xl font-bold mb-4 text-indigo-700">選手打撃能力チャート</h2>
                @if(empty($playerBattingAbilitiesData))
                    <p class="text-gray-600">この選手の打撃能力データはありません。</p>
                @else
                    <div class="flex justify-center items-center">
                        <canvas id="battingAbilityChart" class="w-full max-w-lg h-96" data-batting-abilities="{{ json_encode($playerBattingAbilitiesData) }}"></canvas>
                    </div>
                @endif
            </div>

            {{-- 総合ランクチャート (レイアウト変更によりFlexアイテムに) --}}
            <div class="bg-white shadow-xl rounded-lg p-8 flex-1">
                <h2 class="text-2xl font-bold mb-4 text-indigo-700">野手総合能力ランク (平均との比較)</h2>
                @if(empty($playerOverallRankData))
                    <p class="text-gray-600">この選手の野手総合能力ランクデータはありません。</p>
                @else
                    <div class="flex justify-center items-center">
                        <canvas id="overallRankChart" class="w-full max-w-lg h-96" data-overall-rank="{{ json_encode($playerOverallRankData) }}"></canvas>
                    </div>
                    @if (isset($playerOverallRankData['data'][0]))
                        @php
                            $rankValue = $playerOverallRankData['data'][0];
                            $rankText = '';
                            if ($rankValue >= 90) {
                                $rankText = 'Sランク';
                            } elseif ($rankValue >= 80) {
                                $rankText = 'Aランク';
                            } elseif ($rankValue >= 70) {
                                $rankText = 'Bランク';
                            } elseif ($rankValue >= 60) {
                                $rankText = 'Cランク';
                            } else {
                                $rankText = 'Dランク';
                            }
                        @endphp
                        <p class="text-center text-3xl font-bold text-red-600 mt-4">
                            {{ $player->name }}の野手総合ランク: {{ $rankText }} ({{ $rankValue }})
                        </p>
                    @endif
                @endif
            </div>
        </div>
                    {{-- ★★★ ここから追加 ★★★ --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-indigo-700">打撃能力データ管理</h2>

            {{-- 新しい打撃能力を追加するボタン --}}
            <div class="mb-6">
                <a href="{{ route('players.batting-abilities.create', $player->id) }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-full text-lg transition duration-300 transform hover:scale-105">
                    新しい打撃能力を追加
                </a>
            </div>

            {{-- 年度別打撃能力の一覧表示（PlayerBattingAbilityモデルのデータ） --}}
            <h3 class="text-xl font-bold mb-4 text-indigo-600">登録済み打撃能力一覧</h3>
            @if($player->battingAbilities->isEmpty()) {{-- ★ リレーション名が正しいか確認してください (例: playerBattingAbilities) ★ --}}
                <p class="text-gray-600">この選手の打撃能力データはまだ登録されていません。</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-purple-100 text-purple-800">
                            <tr>
                                <th class="py-3 px-4 border-b text-left">年度</th>
                                <th class="py-3 px-4 border-b text-left">ミート</th>
                                <th class="py-3 px-4 border-b text-left">パワー</th>
                                <th class="py-3 px-4 border-b text-left">走力</th>
                                <th class="py-3 px-4 border-b text-left">守備</th>
                                <th class="py-3 px-4 border-b text-left">肩</th>
                                <th class="py-3 px-4 border-b text-left">捕球</th>
                                <th class="py-3 px-4 border-b text-left">総合ランク</th>
                                <th class="py-3 px-4 border-b text-left">特殊能力</th>
                                <th class="py-3 px-4 border-b text-left">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($player->battingAbilities as $stat) {{-- ★ リレーション名が正しいか確認してください ★ --}}
                                <tr class="hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : '' }}">
                                    <td class="py-3 px-4 border-b">{{ $stat->year }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->contact_power }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->power }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->speed }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->fielding }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->throwing }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->reaction }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->overall_rank }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->special_skills ?? '-' }}</td>
                                    <td class="py-3 px-4 border-b flex space-x-2">
                                        {{-- 編集ボタン --}}
                                        <a href="{{ route('players.batting-abilities.edit', ['player' => $player->id, 'playerBattingAbility' => $stat->id]) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-sm">
                                            編集
                                        </a>

                                        {{-- 削除フォーム --}}
                                        <form action="{{ route('players.batting-abilities.destroy', ['player' => $player->id, 'playerBattingAbility' => $stat->id]) }}" method="POST" id="battingDeleteForm">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded text-sm">
                                                削除
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        {{-- ★★★ ここまで追加 ★★★ --}}

        {{-- 投手能力チャート群のFlexコンテナ (新しいセクション) --}}
        <div class="flex flex-col md:flex-row gap-8 mb-8">
            {{-- 選手変化球チャート --}}
            <div class="bg-white shadow-xl rounded-lg p-8 flex-1">
                <h2 class="text-2xl font-bold mb-4 text-indigo-700">投手変化球チャート</h2>
                @if(empty($playerPitchingAbilitiesData))
                    <p class="text-gray-600">この選手の投手変化球データはありません。</p>
                @else
                    <div class="flex justify-center items-center">
                        <canvas id="pitchingAbilityChart" class="w-full max-w-lg h-96" data-pitching-abilities="{{ json_encode($playerPitchingAbilitiesData) }}"></canvas>
                    </div>
                @endif
            </div>

            {{-- 選手基本投球能力チャート (スタミナ, コントロール) を棒グラフで --}}
            <div class="bg-white shadow-xl rounded-lg p-8 flex-1">
                <h2 class="text-2xl font-bold mb-4 text-indigo-700">投手基本能力 (スタミナ, コントロール)</h2>
                @if(empty($playerPitchingFundamentalAbilitiesData))
                    <p class="text-gray-600">この選手の投手基本能力データはありません。</p>
                @else
                    <div class="flex justify-center items-center">
                        <canvas id="pitchingFundamentalAbilityChart" class="w-full max-w-lg h-96" data-pitching-fundamental-abilities="{{ json_encode($playerPitchingFundamentalAbilitiesData) }}"></canvas>
                    </div>
                @endif
            </div>
        </div>

        {{-- 球速と投手総合ランクチャートのFlexコンテナ --}}
        <div class="flex flex-col md:flex-row gap-8 mb-8">
            {{-- ★球速比較チャート (新しいセクション)★ --}}
            <div class="bg-white shadow-xl rounded-lg p-8 flex-1">
                <h2 class="text-2xl font-bold mb-4 text-indigo-700">球速 (チーム・全体との比較)</h2>
                @if(empty($playerPitchingVelocityComparisonData))
                    <p class="text-gray-600">この選手の球速比較データはありません。</p>
                @else
                    <div class="flex justify-center items-center">
                        <canvas id="pitchingVelocityComparisonChart" class="w-full max-w-lg h-96" data-pitching-velocity-comparison="{{ json_encode($playerPitchingVelocityComparisonData) }}"></canvas>
                    </div>
                    @if (isset($playerPitchingVelocityComparisonData['data'][0]))
                        <p class="text-center text-3xl font-bold text-blue-600 mt-4">
                            {{ $player->name }}の球速: {{ $playerPitchingVelocityComparisonData['data'][0] }} km/h
                        </p>
                    @endif
                @endif
            </div>

            {{-- 投手総合ランクチャート --}}
            <div class="bg-white shadow-xl rounded-lg p-8 flex-1">
                <h2 class="text-2xl font-bold mb-4 text-indigo-700">投手総合能力ランク (平均との比較)</h2>
                @if(empty($playerPitchingOverallRankData))
                    <p class="text-gray-600">この選手の投手総合能力ランクデータはありません。</p>
                @else
                    <div class="flex justify-center items-center">
                        <canvas id="pitchingOverallRankChart" class="w-full max-w-lg h-96" data-pitching-overall-rank="{{ json_encode($playerPitchingOverallRankData) }}"></canvas>
                    </div>
                    @if (isset($playerPitchingOverallRankData['data'][0]))
                        @php
                            $rankValue = $playerPitchingOverallRankData['data'][0];
                            $rankText = '';
                            if ($rankValue >= 90) {
                                $rankText = 'Sランク';
                            } elseif ($rankValue >= 80) {
                                $rankText = 'Aランク';
                            } elseif ($rankValue >= 70) {
                                $rankText = 'Bランク';
                            } elseif ($rankValue >= 60) {
                                $rankText = 'Cランク';
                            } else {
                                $rankText = 'Dランク';
                            }
                        @endphp
                        <p class="text-center text-3xl font-bold text-red-600 mt-4">
                            {{ $player->name }}の投手総合ランク: {{ $rankText }} ({{ $rankValue }})
                        </p>
                    @endif
                @endif
            </div>
        </div>

        {{-- ★★★ ここから投手能力データ管理を追加 ★★★ --}}
<div class="bg-white shadow-xl rounded-lg p-8 mb-8">
    <h2 class="text-2xl font-bold mb-4 text-indigo-700">投手能力データ管理</h2>

    {{-- 新しい投手能力を追加するボタン --}}
    <div class="mb-6">
        <a href="{{ route('players.pitching-abilities.create', $player->id) }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-full text-lg transition duration-300 transform hover:scale-105">
            新しい投手能力を追加
        </a>
    </div>

    {{-- 年度別投手能力の一覧表示（PlayerPitchingAbilityモデルのデータ） --}}
    <h3 class="text-xl font-bold mb-4 text-indigo-600">登録済み投手能力一覧</h3>
    @if($player->pitchingAbilities->isEmpty()) {{-- ここでリレーション名を `pitchingAbilities` と仮定 --}}
        <p class="text-gray-600">この選手の投手能力データはまだ登録されていません。</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-indigo-100 text-indigo-800"> {{-- 色を打撃能力と変えてみました --}}
                    <tr>
                        <th class="py-3 px-4 border-b text-left">年度</th>
                        <th class="py-3 px-4 border-b text-left">球速(平均)</th>
                        <th class="py-3 px-4 border-b text-left">スタミナ</th>
                        <th class="py-3 px-4 border-b text-left">コントロール</th>
                        @for ($i = 1; $i <= 7; $i++)
                            <th class="py-3 px-4 border-b text-left">変化球{{ $i }}</th>
                        @endfor
                        <th class="py-3 px-4 border-b text-left">総合ランク</th>
                        <th class="py-3 px-4 border-b text-left">特殊能力</th>
                        <th class="py-3 px-4 border-b text-left">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($player->pitchingAbilities as $stat) {{-- ここでリレーション名を `pitchingAbilities` と仮定 --}}
                        <tr class="hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : '' }}">
                            <td class="py-3 px-4 border-b">{{ $stat->year ?? '-' }}</td>
                            <td class="py-3 px-4 border-b">{{ $stat->average_velocity ?? '-' }}</td>
                            <td class="py-3 px-4 border-b">{{ $stat->pitch_stamina ?? '-' }}</td>
                            <td class="py-3 px-4 border-b">{{ $stat->pitch_control ?? '-' }}</td>
                            @for ($i = 1; $i <= 7; $i++)
                                @php
                                    $pitchTypeField = 'pitch_type_' . $i;
                                    $pitchInfo = $stat->$pitchTypeField;
                                @endphp
                                <td class="py-3 px-4 border-b">{{ $pitchInfo ?? '-' }}</td>
                            @endfor
                            <td class="py-3 px-4 border-b">{{ $stat->overall_rank ?? '-' }}</td>
                            <td class="py-3 px-4 border-b">{{ $stat->special_skills ?? '-' }}</td>
                            <td class="py-3 px-4 border-b flex space-x-2">
                                {{-- 編集ボタン --}}
                                <a href="{{ route('players.pitching-abilities.edit', ['player' => $player->id, 'playerPitchingAbility' => $stat->id]) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-sm">
                                    編集
                                </a>

                                {{-- 削除フォーム --}}
                                <form action="{{ route('players.pitching-abilities.destroy', ['player' => $player->id, 'playerPitchingAbility' => $stat->id]) }}" method="POST" onsubmit="return confirm('本当にこの投手能力データを削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded text-sm">
                                        削除
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
{{-- ★★★ ここまで投手能力データ管理を追加 ★★★ --}}

        {{-- ★旧「球速」セクションを削除、またはこのコメントのように残す★ --}}
        {{-- 以前の球速単体表示のセクションは削除しました。比較グラフが代わりに表示されます。 --}}
        {{-- 必要であれば、ここに旧コードをコメントアウトして残しておくことも可能です --}}


        {{-- 年度別打撃成績 --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-indigo-700">年度別打撃成績</h2>
            @if($player->yearlyBattingStats->isEmpty())
                <p class="text-gray-600">この選手の打撃成績データはありません。</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-blue-100 text-blue-800">
                            <tr>
                                <th class="py-3 px-4 border-b text-left">年度</th>
                                <th class="py-3 px-4 border-b text-left">試合</th>
                                <th class="py-3 px-4 border-b text-left">打席</th>
                                <th class="py-3 px-4 border-b text-left">打数</th>
                                <th class="py-3 px-4 border-b text-left">安打</th>
                                <th class="py-3 px-4 border-b text-left">二塁打</th>
                                <th class="py-3 px-4 border-b text-left">三塁打</th>
                                <th class="py-3 px-4 border-b text-left">本塁打</th>
                                <th class="py-3 px-4 border-b text-left">打点</th>
                                <th class="py-3 px-4 border-b text-left">盗塁</th>
                                <th class="py-3 px-4 border-b text-left">四球</th>
                                <th class="py-3 px-4 border-b text-left">三振</th>
                                <th class="py-3 px-4 border-b text-left">打率</th>
                                <th class="py-3 px-4 border-b text-left">出塁率</th>
                                <th class="py-3 px-4 border-b text-left">長打率</th>
                                <th class="py-3 px-4 border-b text-left">OPS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($player->yearlyBattingStats as $stat)
                                <tr class="hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : '' }}">
                                    <td class="py-3 px-4 border-b">{{ $stat->year }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->games }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->plate_appearances }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->at_bats }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->hits }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->doubles }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->triples }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->home_runs }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->rbi }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->stolen_bases }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->walks }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->strikeouts }}</td>
                                    <td class="py-3 px-4 border-b">{{ number_format($stat->batting_average, 3) }}</td>
                                    <td class="py-3 px-4 border-b">{{ number_format($stat->on_base_percentage, 3) }}</td>
                                    <td class="py-3 px-4 border-b">{{ number_format($stat->slugging_percentage, 3) }}</td>
                                <td class="py-3 px-4 border-b">{{ number_format($stat->ops, 3) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- 年度別投球成績 --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-indigo-700">年度別投球成績</h2>
            @if($player->yearlyPitchingStats->isEmpty())
                <p class="text-gray-600">この選手の投球成績データはありません。</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-blue-100 text-blue-800">
                            <tr>
                                <th class="py-3 px-4 border-b text-left">年度</th>
                                <th class="py-3 px-4 border-b text-left">登板</th>
                                <th class="py-3 px-4 border-b text-left">先発</th>
                                <th class="py-3 px-4 border-b text-left">勝利</th>
                                <th class="py-3 px-4 border-b text-left">敗北</th>
                                <th class="py-3 px-4 border-b text-left">セーブ</th>
                                <th class="py-3 px-4 border-b text-left">ホールド</th>
                                <th class="py-3 px-4 border-b text-left">投球回</th>
                                <th class="py-3 px-4 border-b text-left">被安打</th>
                                <th class="py-3 px-4 border-b text-left">被本塁打</th>
                                <th class="py-3 px-4 border-b text-left">与四球</th>
                                <th class="py-3 px-4 border-b text-left">奪三振</th>
                                <th class="py-3 px-4 border-b text-left">自責点</th>
                                <th class="py-3 px-4 border-b text-left">防御率</th>
                                <th class="py-3 px-4 border-b text-left">WHIP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($player->yearlyPitchingStats as $stat)
                                <tr class="hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : '' }}">
                                    <td class="py-3 px-4 border-b">{{ $stat->year }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->games }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->starts }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->wins }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->losses }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->saves }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->holds }}</td>
                                    <td class="py-3 px-4 border-b">{{ number_format($stat->innings_pitched, 1) }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->hits_allowed }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->home_runs_allowed }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->walks_allowed }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->strikeouts_pitched }}</td>
                                    <td class="py-3 px-4 border-b">{{ $stat->earned_runs }}</td>
                                    <td class="py-3 px-4 border-b">{{ number_format($stat->earned_run_average, 2) }}</td>
                                    <td class="py-3 px-4 border-b">{{ number_format($stat->whip, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="text-center mt-8">
            @if ($player->team_id)
                <a href="{{ route('teams.show', $player->team_id) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full text-lg transition duration-300 transform hover:scale-105 mr-4">
                    所属チーム詳細に戻る
                </a>
            @else
                <span class="inline-block bg-gray-400 text-white font-bold py-3 px-6 rounded-full text-lg mr-4">
                    所属チーム情報なし
                </span>
            @endif
            <a href="{{ route('players.index') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-full text-lg transition duration-300 transform hover:scale-105">
                選手一覧に戻る
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/playerAbilityChart.js') }}"></script>
    <script src="{{ asset('js/form-confirmation.js') }}"></script>
</x-app-layout>