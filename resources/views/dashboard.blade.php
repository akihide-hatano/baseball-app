<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- ★★★ ここからアプリの流れ/できることを追加 ★★★ --}}
                    <div class="p-6 bg-indigo-50 rounded-lg shadow-inner">
                        <h3 class="text-2xl font-bold mb-4 text-indigo-800 border-b-2 border-indigo-700 pb-2">
                            このアプリでできること
                        </h3>

                        <p class="mb-4 text-gray-700">
                            「私のプロ野球データ管理システム」へようこそ！このアプリでは、野球に関する様々なデータを一元的に管理・閲覧・分析できます。
                        </p>

                        <ul class="list-disc list-inside space-y-6 text-gray-800">
                            <li class="flex items-start">
                                <span class="text-indigo-600 mr-2 text-xl">⚾</span>
                                <div class="flex-grow">
                                    <strong class="text-xl">選手情報の管理:</strong>
                                    <p>選手の基本情報（背番号、所属チーム、役割など）を登録し、<a href="{{ route('players.index') }}" class="text-blue-600 hover:underline font-semibold">選手一覧</a>から詳細を確認、編集、削除できます。</p>
                                    <div class="mt-2 flex space-x-2"> {{-- ボタンを横並びにするためにflexとspace-x-2を追加 --}}
                                        <a href="{{ route('players.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            選手一覧へ
                                        </a>
                                        {{-- 選手新規作成ルートがあれば追加 --}}
                                        @if (Route::has('players.create'))
                                            <a href="{{ route('players.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                選手を新規作成する
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="text-indigo-600 mr-2 text-xl">🏟️</span>
                                <div class="flex-grow">
                                    <strong class="text-xl">チーム情報の管理:</strong>
                                    <p>所属するチームの基本情報を登録し、<a href="{{ route('teams.index') }}" class="text-blue-600 hover:underline font-semibold">チーム一覧</a>から詳細を確認、編集、削除できます。</p>
                                    <div class="mt-2 flex space-x-2">
                                        <a href="{{ route('teams.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            チーム一覧へ
                                        </a>
                                        {{-- チーム新規作成ルートがあれば追加 --}}
                                        @if (Route::has('teams.create'))
                                            <a href="{{ route('teams.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                チームを新規作成する
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="text-indigo-600 mr-2 text-xl">📊</span>
                                <div class="flex-grow">
                                    <strong class="text-xl">打撃・投手能力の記録:</strong>
                                    <p>各選手の年度ごとの打撃能力（ミート、パワーなど）や投手能力（球速、変化球など）を細かく記録・管理できます。<br>
                                    <span class="text-sm text-gray-500">（選手詳細ページから各能力の追加・編集が可能です。）</span></p>
                                    <div class="mt-2">
                                        <a href="{{ route('players.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            選手一覧から能力を記録・編集する
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="text-indigo-600 mr-2 text-xl">📈</span>
                                <div class="flex-grow">
                                    <strong class="text-xl">年度別成績の管理:</strong>
                                    <p>打撃成績（打率、本塁打など）や投球成績（防御率、奪三振など）を年度別に登録し、選手の成長を追跡できます。<br>
                                    <span class="text-sm text-gray-500">（選手詳細ページから各成績の追加・編集が可能です。）</span></p>
                                    <div class="mt-2">
                                        <a href="{{ route('players.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            選手一覧から成績を記録・編集する
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="text-indigo-600 mr-2 text-xl">⚔️</span>
                                <div class="flex-grow">
                                    <strong class="text-xl">試合結果の記録:</strong>
                                    <p>チームが行った試合の結果や詳細を記録し、<a href="{{ route('games.index') }}" class="text-blue-600 hover:underline font-semibold">試合一覧</a>から確認できます。</p>
                                    <div class="mt-2 flex space-x-2">
                                        <a href="{{ route('games.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            試合一覧へ
                                        </a>
                                        {{-- 試合新規作成ルートがあれば追加 --}}
                                        @if (Route::has('games.create'))
                                            <a href="{{ route('games.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                試合結果を新規記録する
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="text-indigo-600 mr-2 text-xl">⭐</span>
                                <div class="flex-grow">
                                    <strong class="text-xl">グラフによる可視化:</strong>
                                    <p>記録された能力データは、レーダーチャートや棒グラフなどで視覚的に分かりやすく表示され、選手の強みや成長を直感的に把握できます。</p>
                                    <div class="mt-2">
                                        <a href="{{ route('players.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            選手一覧からデータを見る・分析する
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>