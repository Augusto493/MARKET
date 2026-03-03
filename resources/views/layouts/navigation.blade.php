@php
    $user = Auth::user();
    $isAdmin = $user->hasRole('superadmin') || $user->hasRole('admin');
    $owner = \App\Models\Owner::where('user_id', $user->id)->first();
    $isAnfitriao = (bool) $owner;
@endphp
<nav x-data="{ open: false }" class="bg-slate-900 border-b border-slate-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-white font-semibold text-lg">
                    <span class="bg-indigo-500 w-8 h-8 rounded-lg flex items-center justify-center text-sm">H</span>
                    <span class="hidden sm:inline">HospedaBC</span>
                </a>
                <div class="hidden md:flex md:ml-10 md:gap-1">
                    @if($isAdmin)
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Admin</a>
                    @endif
                    @if($isAnfitriao)
                        <a href="{{ route('anfitriao.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('anfitriao.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Área do anfitrião</a>
                    @endif
                    <a href="{{ route('marketplace.index') }}" target="_blank" class="px-3 py-2 rounded-md text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Ver site</a>
                </div>
            </div>

            <div class="hidden md:flex md:items-center md:gap-2">
                <span class="text-slate-400 text-sm">{{ $user->name }}</span>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 focus:outline-none">
                        <span class="text-sm">{{ $user->email }}</span>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-1 w-48 py-1 bg-slate-800 rounded-lg shadow-xl z-50 border border-slate-700">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700">Perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-slate-200 hover:bg-slate-700">Sair</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="flex md:hidden items-center">
                <button @click="open = !open" class="p-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-800">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path :class="{'hidden': open, 'inline-flex': !open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/><path :class="{'hidden': !open, 'inline-flex': open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden border-t border-slate-700">
        <div class="px-2 pt-2 pb-3 space-y-1">
            @if($isAdmin)<a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:bg-slate-800">Admin</a>@endif
            @if($isAnfitriao)<a href="{{ route('anfitriao.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:bg-slate-800">Área do anfitrião</a>@endif
            <a href="{{ route('marketplace.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:bg-slate-800">Ver site</a>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:bg-slate-800">Perfil</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:bg-slate-800">Sair</button></form>
        </div>
    </div>
</nav>
