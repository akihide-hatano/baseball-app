<header class="bg-blue-600 text-white py-6 text-center font-inter rounded-b-lg shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center"> {{-- flex justify-between items-center を追加 --}}
        <div class="flex justify-start items-center gap-10"> {{-- タイトルと説明のコンテナ --}}
            <img src="/image/header-logo.png" class="w-12 h-12" alt="logo">
            <h1 class="text-xl font-extrabold mb-2">私のプロ野球データ管理システム</h1>
        </div>

        <div class="flex items-center space-x-6"> {{-- ナビゲーションリンクと認証関連のコンテナ --}}
            {{-- 通常のナビゲーションリンク --}}
            <a href="{{ route('home') }}" class="text-white hover:text-blue-200 font-bold text-l px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-blue-700">ホーム</a>
            <a href="{{ route('players.index') }}" class="text-white hover:text-blue-200 font-bold text-l px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-blue-700">選手一覧</a>
            <a href="{{ route('teams.index') }}" class="text-white hover:text-blue-200 font-bold text-l px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-blue-700">チーム一覧</a>
            <a href="{{ route('games.index') }}" class="text-white hover:text-blue-200 font-bold text-l px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-blue-700">試合一覧</a>

            {{-- 認証関連のリンク --}}
            @auth {{-- ログインしている場合 --}}
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:text-blue-200 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @else {{-- ログインしていない場合 --}}
                <a href="{{ route('login') }}" class="text-white hover:text-blue-200 font-bold text-lg px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-blue-700">
                    {{ __('Log in') }}
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-white hover:text-blue-200 font-bold text-lg px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-blue-700">
                        {{ __('Register') }}
                    </a>
                @endif
            @endauth
        </div>
    </div>
</header>