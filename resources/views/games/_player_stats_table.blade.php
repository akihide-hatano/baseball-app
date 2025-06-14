{{-- resources/views/games/_player_stats_table.blade.php --}}

<table class="min-w-full bg-white border border-gray-200 rounded-lg mb-6">
    <thead class="bg-blue-100 text-blue-800">
        <tr>
            <th class="py-3 px-4 border-b text-left">背番号</th>
            <th class="py-3 px-4 border-b text-left">選手名</th>
            <th class="py-3 px-4 border-b text-left">役割</th>
            <th class="py-3 px-4 border-b text-left">出場</th>
            <th class="py-3 px-4 border-b text-left">打順</th>
            <th class="py-3 px-4 border-b text-left">ポジション</th>
            @php
                $firstStat = $playerGameStats->first();
                // $firstStat->player が null でないことを確認
                $isPitcherTable = $firstStat && $firstStat->player && $firstStat->player->role === '投手';
            @endphp

            @if ($isPitcherTable)
                <th class="py-3 px-4 border-b text-left">投球回</th>
                <th class="py-3 px-4 border-b text-left">自責点</th>
                <th class="py-3 px-4 border-b text-left">奪三振</th>
                <th class="py-3 px-4 border-b text-left">与四球</th>
                <th class="py-3 px-4 border-b text-left">被安打</th>
                <th class="py-3 px-4 border-b text-left">被本塁打</th>
                <th class="py-3 px-4 border-b text-left">勝</th>
                <th class="py-3 px-4 border-b text-left">負</th>
                <th class="py-3 px-4 border-b text-left">S</th>
                <th class="py-3 px-4 border-b text-left">H</th>
                <th class="py-3 px-4 border-b text-left">投球数</th>
            @else
                <th class="py-3 px-4 border-b text-left">打席</th>
                <th class="py-3 px-4 border-b text-left">打数</th>
                <th class="py-3 px-4 border-b text-left">安打</th>
                <th class="py-3 px-4 border-b text-left">二塁打</th>
                <th class="py-3 px-4 border-b text-left">三塁打</th>
                <th class="py-3 px-4 border-b text-left">本塁打</th>
                <th class="py-3 px-4 border-b text-left">打点</th>
                <th class="py-3 px-4 border-b text-left">盗塁</th>
                <th class="py-3 px-4 border-b text-left">三振</th>
                <th class="py-3 px-4 border-b text-left">四球</th>
                <th class="py-3 px-4 border-b text-left">死球</th>
                <th class="py-3 px-4 border-b text-left">犠打</th>
                <th class="py-3 px-4 border-b text-left">犠飛</th>
                <th class="py-3 px-4 border-b text-left">併殺打</th>
                <th class="py-3 px-4 border-b text-left">失策</th>
                <th class="py-3 px-4 border-b text-left">得点</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($playerGameStats as $stat)
            <tr class="hover:bg-gray-100 {{ $loop->even ? 'bg-gray-50' : '' }}">
                <td class="py-3 px-4 border-b">{{ $stat->player->jersey_number ?? '-' }}</td>
                <td class="py-3 px-4 border-b">
                    <a href="{{ route('players.show', $stat->player->id) }}" class="text-blue-600 hover:underline">
                        {{ $stat->player->name ?? '不明' }}
                    </a>
                </td>
                <td class="py-3 px-4 border-b">{{ $stat->player->role ?? '-' }}</td>
                <td class="py-3 px-4 border-b">{{ $stat->is_starter ? 'スタメン' : '途中出場' }}</td>
                <td class="py-3 px-4 border-b">{{ $stat->batting_order ?? '-' }}</td>
                <td class="py-3 px-4 border-b">{{ $stat->position->name ?? '-' }}</td>

                {{-- 投手なら投球成績、野手なら打撃成績のセル --}}
                @if ($stat->player->role === '投手')
                    <td class="py-3 px-4 border-b">{{ number_format($stat->innings_pitched, 1) }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->earned_runs }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->strikeouts_pitched }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->walks_allowed }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->hits_allowed }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->home_runs_allowed }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->is_winner_pitcher ? '○' : '-' }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->is_loser_pitcher ? '●' : '-' }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->is_save_pitcher ? 'S' : '-' }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->is_hold_pitcher ? 'H' : '-' }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->pitches_thrown }}</td>
                @else
                    <td class="py-3 px-4 border-b">{{ $stat->plate_appearances }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->at_bats }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->hits }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->doubles }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->triples }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->home_runs }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->rbi }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->stolen_bases }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->strikeouts }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->walks }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->hit_by_pitch }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->sac_bunts }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->sac_flies }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->double_plays }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->errors }}</td>
                    <td class="py-3 px-4 border-b">{{ $stat->runs_scored }}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>