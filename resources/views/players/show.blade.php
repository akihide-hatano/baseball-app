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

        {{-- 選手打撃能力チャート (PlayerBattingAbility) --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-indigo-700">選手打撃能力チャート</h2>
            @if(empty($playerBattingAbilitiesData))
                <p class="text-gray-600">この選手の打撃能力データはありません。</p>
            @else
                <div class="flex justify-center items-center">
                    {{-- data-batting-abilities 属性でデータを埋め込みます --}}
                    <canvas id="battingAbilityChart" class="w-full max-w-lg h-96" data-batting-abilities="{{ json_encode($playerBattingAbilitiesData) }}"></canvas>
                </div>
            @endif
        </div>

        {{-- 選手変化球チャート (PlayerPitchingAbility - Pitch Types) --}}
        <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-indigo-700">選手変化球チャート</h2>
            @if(empty($playerPitchingAbilitiesData))
                <p class="text-gray-600">この選手の変化球データはありません。</p>
            @else
                <div class="flex justify-center items-center">
                    {{-- data-pitching-abilities 属性でデータを埋め込みます --}}
                    <canvas id="pitchingAbilityChart" class="w-full max-w-lg h-96" data-pitching-abilities="{{ json_encode($playerPitchingAbilitiesData) }}"></canvas>
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
    <script src="{{ asset('js/playerAbilityChart.js') }}"></script> {{-- ★ここだけに変更★ --}}
</x-app-layout>