<footer class="mt-16 border-t border-slate-200 bg-slate-950 text-slate-400 dark:border-slate-800">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-10 md:grid-cols-4">
            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="mb-4 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-linear-to-br from-orange-500 via-orange-500 to-purple-600 shadow-lg shadow-orange-500/20">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 6h14"/>
                            <path d="M7 6l5 6-5 6"/>
                            <path d="M17 6l-5 6 5 6"/>
                        </svg>
                    </div>
                    <span class="text-xl font-black tracking-tight text-white">Zverse</span>
                </a>
                <p class="mb-6 max-w-sm text-sm leading-relaxed text-slate-400">
                    Portal entertainment modern untuk kamu yang ingin tetap up to date dengan musik, game, film, dan budaya pop.
                </p>
                <div class="flex items-center gap-3">
                    @foreach([['x','twitter'],['instagram','instagram'],['youtube','youtube'],['play-circle','twitch']] as [$icon,$label])
                    <a href="#" aria-label="{{ ucfirst($label) }}"
                       class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-800 bg-slate-900 text-slate-400 transition-all hover:border-orange-500 hover:bg-orange-500/10 hover:text-orange-400">
                        <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
                    </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-bold uppercase tracking-[0.2em] text-white">Kategori</h4>
                <ul class="space-y-2">
                    @foreach([['games','Games'],['musik','Musik'],['film','Film'],['entertainment','Entertainment']] as [$cat,$label])
                    <li>
                        <a href="{{ route('category.show', $cat) }}" class="text-sm text-slate-400 transition-colors hover:text-orange-400">
                            {{ $label }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-bold uppercase tracking-[0.2em] text-white">Tentang</h4>
                <ul class="space-y-2">
                    @foreach(['Tentang Kami','Tim Editor','Kirim Artikel','Hubungi Kami'] as $item)
                    <li>
                        <a href="#" class="text-sm text-slate-400 transition-colors hover:text-orange-400">{{ $item }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-slate-800 pt-6 sm:flex-row">
            <p class="text-xs text-slate-600">© {{ date('Y') }} Zverse — Portal Media & Entertainment Modern. Semua hak dilindungi.</p>
            <div class="flex items-center gap-4">
                @foreach(['Kebijakan Privasi','Syarat & Ketentuan'] as $item)
                <a href="#" class="text-xs text-slate-600 transition-colors hover:text-slate-400">{{ $item }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
