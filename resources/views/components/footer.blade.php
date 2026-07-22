<footer class="mt-16 border-t border-slate-200 bg-slate-950 text-slate-400 dark:border-slate-800">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-10 md:grid-cols-4">
            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="mb-4 flex shrink-0 items-center gap-3 group">
                    @php $zvLogo = 'logozverse.png'; @endphp
                    <img src="{{ asset($zvLogo) }}?v={{ filemtime(public_path($zvLogo)) }}" alt="Zverse" class="h-8 w-8 sm:h-10 sm:w-10 shrink-0 object-contain transition-transform duration-300 group-hover:scale-105" />
                    <div class="leading-none">
                        <p class="text-lg font-black tracking-tight text-white">Zverse</p>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-slate-500">Media & Tech</p>
                    </div>
                </a>
                <p class="max-w-sm text-sm leading-relaxed text-slate-400">
                    Portal entertainment modern untuk kamu yang ingin tetap up to date dengan musik, game, film, dan budaya pop.
                </p>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-bold uppercase tracking-[0.2em] text-white">Kategori</h4>
                <ul class="space-y-2">
                    @foreach([['games','gamepad-2','Games'],['musik','music-4','Musik'],['film','film','Film'],['entertainment','sparkles','Entertainment']] as [$cat,$icon,$label])
                    <li>
                        <a href="{{ route('category.show', $cat) }}" class="group flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-slate-400 transition-all hover:bg-slate-900 hover:text-orange-400">
                            <i data-lucide="{{ $icon }}" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110"></i>
                            <span>{{ $label }}</span>
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
