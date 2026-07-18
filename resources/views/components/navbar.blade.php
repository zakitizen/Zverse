<nav class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl shadow-sm shadow-slate-200/30 dark:border-slate-800 dark:bg-slate-950/80">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        
        {{-- Logo Zverse Baru --}}
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3 group">
            @php $zvLogo = 'logozverse.png'; @endphp
            <img src="{{ asset($zvLogo) }}?v={{ filemtime(public_path($zvLogo)) }}" alt="Zverse" class="h-8 w-8 sm:h-10 sm:w-10 shrink-0 object-contain transition-transform duration-300 group-hover:scale-105" />
            <div class="leading-none">
                <p class="text-lg font-black tracking-tight text-slate-900 dark:text-white">Zverse</p>
                <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400">Media & Tech</p>
            </div>
        </a>

        {{-- Desktop Menu --}}
        <div class="hidden items-center gap-1 md:flex">
            @foreach([['games','gamepad-2','Games'],['musik','music-4','Musik'],['film','film','Film'],['entertainment','sparkles','Entertainment']] as [$cat,$icon,$label])
                <a href="{{ route('category.show', $cat) }}"
                   class="flex items-center gap-1.5 rounded-xl px-3 py-2 text-sm font-semibold transition-all {{ request()->is('category/'.$cat) ? 'bg-sky-50 text-sky-600 shadow-sm dark:bg-sky-500/10 dark:text-sky-400' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                    <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            {{-- Search Bar Desktop --}}
            <form action="{{ route('search') }}" method="GET" class="hidden items-center sm:flex">
                <div class="relative">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari artikel..."
                           class="w-44 rounded-full border border-slate-200 bg-slate-50 py-2 pl-9 pr-4 text-sm font-medium text-slate-700 outline-none transition-all focus:w-56 focus:border-sky-400 focus:ring-4 focus:ring-sky-500/10 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200">
                </div>
            </form>

            {{-- User Menu & Login Button --}}
            @auth
                <div class="relative">
                    <button id="user-menu-btn" type="button" class="flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-1.5 py-1 pr-2.5 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900/80 dark:hover:bg-slate-800">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br {{ auth()->user()->avatar_color }} text-xs font-black text-white">
                            {{ strtoupper(substr(auth()->user()->display_name, 0, 1)) }}
                        </div>
                        <span class="hidden max-w-24 truncate text-sm font-semibold text-slate-700 sm:block dark:text-slate-200">{{ auth()->user()->display_name }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div id="user-menu" class="absolute right-0 top-full mt-2 hidden w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
                        <div class="border-b border-slate-100 bg-sky-50 px-4 py-3 dark:border-slate-800 dark:bg-sky-500/10">
                            <p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ auth()->user()->display_name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ '@'.auth()->user()->username }}</p>
                        </div>
                        <div class="p-2 space-y-1">
                            @if(auth()->user()->role === 'pewarta')
                                <a href="{{ route('pewarta.dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-sky-50 hover:text-sky-700 dark:text-slate-200 dark:hover:bg-sky-500/10 dark:hover:text-sky-400">
                                    <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                                    <span>Dashboard Pewarta</span>
                                </a>
                            @elseif(auth()->user()->role === 'redaksi')
                                <a href="{{ route('redaksi.dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-sky-50 hover:text-sky-700 dark:text-slate-200 dark:hover:bg-sky-500/10 dark:hover:text-sky-400">
                                    <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                                    <span>Dashboard Redaksi</span>
                                </a>
                            @endif

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-rose-600 transition-colors hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                    <i data-lucide="log-out" class="h-4 w-4"></i>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-2 rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-sky-500/20 transition-colors hover:bg-sky-600">
                    <i data-lucide="log-in" class="h-4 w-4"></i>
                    <span>Masuk</span>
                </a>
            @endauth

            {{-- Hamburger Button Mobile --}}
            <button id="mobile-menu-btn" type="button" class="rounded-full p-2 text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 md:hidden dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                <i data-lucide="menu" class="h-5 w-5"></i>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="hidden border-t border-slate-200 bg-white/95 px-4 py-4 shadow-sm backdrop-blur md:hidden dark:border-slate-800 dark:bg-slate-950/95">
        <form action="{{ route('search') }}" method="GET" class="mb-4">
            <div class="relative">
                <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input type="search" name="q" placeholder="Cari artikel..." class="w-full rounded-full border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-4 text-sm font-medium text-slate-700 outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-500/10 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200">
            </div>
        </form>
        <div class="flex flex-col gap-1">
            @auth
                @if(auth()->user()->role === 'pewarta')
                    <a href="{{ route('pewarta.dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-sky-50 hover:text-sky-700 dark:text-slate-200 dark:hover:bg-sky-500/10 dark:hover:text-sky-400">
                        <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                        <span>Dashboard Pewarta</span>
                    </a>
                @elseif(auth()->user()->role === 'redaksi')
                    <a href="{{ route('redaksi.dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-sky-50 hover:text-sky-700 dark:text-slate-200 dark:hover:bg-sky-500/10 dark:hover:text-sky-400">
                        <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                        <span>Dashboard Redaksi</span>
                    </a>
                @endif
            @endauth
            @foreach([['games','gamepad-2','Games'],['musik','music-4','Musik'],['film','film','Film'],['entertainment','sparkles','Entertainment']] as [$cat,$icon,$label])
                
            @endforeach
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
        document.getElementById('mobile-menu')?.classList.toggle('hidden');
    });

    document.getElementById('user-menu-btn')?.addEventListener('click', (e) => {
        e.stopPropagation();
        document.getElementById('user-menu')?.classList.toggle('hidden');
    });

    document.addEventListener('click', () => {
        document.getElementById('user-menu')?.classList.add('hidden');
        document.getElementById('mobile-menu')?.classList.add('hidden');
    });
</script>