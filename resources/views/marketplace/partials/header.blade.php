{{-- Header HospedaBC --}}
<header class="bg-[#1e3a5f] text-white sticky top-0 z-50 shadow-lg overflow-hidden" style="background-color:#1e3a5f!important;color:#fff!important;">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 min-w-0">
        <div class="flex items-center justify-between h-14 sm:h-16 md:h-18 gap-2 min-h-0">

            {{-- Logo --}}
            <a href="{{ route('marketplace.index') }}" class="flex items-center gap-2 sm:gap-2.5 group min-w-0 flex-shrink">
                <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-rose-500 group-hover:bg-rose-400 flex items-center justify-center text-white font-extrabold text-xs sm:text-sm shadow transition flex-shrink-0">H</span>
                <span class="text-base sm:text-lg md:text-xl font-extrabold tracking-tight truncate">HospedaBC</span>
            </a>

            {{-- NAV desktop --}}
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('marketplace.index') }}"
                   class="px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Acomodações
                </a>
                <a href="{{ route('marketplace.search') }}"
                   class="px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Busca avançada
                </a>
                <a href="{{ route('marketplace.locate-reservation') }}"
                   class="px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Minha Reserva
                </a>
            </nav>

            {{-- Ações --}}
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="hidden sm:flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition">
                        Reservas
                    </a>
                    <a href="{{ route('anfitriao.dashboard') }}"
                       class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-semibold text-sm transition shadow">
                        Painel
                    </a>
                    <form action="{{ route('logout') }}" method="post" class="hidden sm:inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 rounded-lg text-sm text-white/70 hover:text-white hover:bg-white/10 transition">
                            Sair
                        </button>
                    </form>
                @else
                    <a href="{{ route('marketplace.locate-reservation') }}"
                       class="hidden sm:flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Minha reserva
                    </a>
                    <a href="{{ route('login') }}"
                       class="px-3 py-2 sm:px-4 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-semibold text-xs sm:text-sm transition shadow flex-shrink-0 whitespace-nowrap">
                        Anunciar<span class="hidden sm:inline"> imóvel</span>
                    </a>
                @endauth
            </div>

        </div>
    </div>
</header>
