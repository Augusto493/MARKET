<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Resultados da busca - HospedaBC</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
        /* Contraste da barra de filtros */
        .search-filters-bar .filter-label,
        .search-filters-bar .filter-title { color: #374151 !important; }
        .search-filters-bar input,
        .search-filters-bar select { border-color: #d1d5db !important; color: #111827 !important; }
        .search-filters-bar .btn-aplicar { background-color: #e11d48 !important; color: #fff !important; }
        .search-filters-bar .btn-aplicar:hover { background-color: #be123c !important; }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    @include('marketplace.partials.header')

    {{-- Barra de busca compacta (estilo VRBO/Airbnb) --}}
    <div class="bg-white border-b border-gray-200 shadow-sm sticky top-14 md:top-16 z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form action="{{ route('marketplace.search') }}" method="get" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[140px]">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Destino</label>
                    <input type="text" name="cidade" placeholder="Cidade ou região" value="{{ $cidade ?? '' }}" class="w-full rounded-xl border-2 border-gray-300 px-3 py-2.5 text-gray-900 bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Check-in</label>
                    <input type="date" name="checkin" value="{{ $checkin ?? '' }}" class="rounded-xl border-2 border-gray-300 px-3 py-2.5 text-gray-900 bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Check-out</label>
                    <input type="date" name="checkout" value="{{ $checkout ?? '' }}" class="rounded-xl border-2 border-gray-300 px-3 py-2.5 text-gray-900 bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">
                </div>
                <div class="w-24">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Hóspedes</label>
                    <input type="number" name="hospedes" value="{{ $hospedes ?? 2 }}" min="1" class="w-full rounded-xl border-2 border-gray-300 px-3 py-2.5 text-gray-900 bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">
                </div>
                @if(isset($preco_min) && $preco_min !== '')<input type="hidden" name="preco_min" value="{{ $preco_min }}">@endif
                @if(isset($preco_max) && $preco_max !== '')<input type="hidden" name="preco_max" value="{{ $preco_max }}">@endif
                @if(isset($ordem) && $ordem !== '')<input type="hidden" name="ordem" value="{{ $ordem }}">@endif
                <button type="submit" class="px-6 py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-semibold rounded-xl transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Buscar
                </button>
            </form>
            <div class="search-filters-bar flex flex-wrap items-center gap-4 mt-4 pt-4 border-t border-gray-200">
                <span class="filter-title text-sm font-semibold text-gray-800">Filtros</span>
                <form action="{{ route('marketplace.search') }}" method="get" class="flex flex-wrap gap-3 items-center">
                    <input type="hidden" name="cidade" value="{{ $cidade ?? '' }}">
                    <input type="hidden" name="checkin" value="{{ $checkin ?? '' }}">
                    <input type="hidden" name="checkout" value="{{ $checkout ?? '' }}">
                    <input type="hidden" name="hospedes" value="{{ $hospedes ?? 2 }}">
                    <input type="hidden" name="ordem" value="{{ $ordem ?? '' }}">
                    <label class="filter-label text-sm font-medium text-gray-700">Preço min</label>
                    <input type="number" name="preco_min" value="{{ $preco_min ?? '' }}" placeholder="R$" min="0" step="10" class="w-28 rounded-lg border-2 border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    <label class="filter-label text-sm font-medium text-gray-700">max</label>
                    <input type="number" name="preco_max" value="{{ $preco_max ?? '' }}" placeholder="R$" min="0" step="10" class="w-28 rounded-lg border-2 border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    <span class="filter-label text-sm font-semibold text-gray-800">Ordenar</span>
                    <select name="ordem" class="rounded-lg border-2 border-gray-300 px-3 py-2 text-sm font-medium text-gray-900 bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500" onchange="this.form.submit()">
                        <option value="" {{ ($ordem ?? '') === '' ? 'selected' : '' }}>Relevância</option>
                        <option value="preco_asc" {{ ($ordem ?? '') === 'preco_asc' ? 'selected' : '' }}>Preço: menor primeiro</option>
                        <option value="preco_desc" {{ ($ordem ?? '') === 'preco_desc' ? 'selected' : '' }}>Preço: maior primeiro</option>
                    </select>
                    <button type="submit" class="btn-aplicar px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition">Aplicar</button>
                </form>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('marketplace.index') }}" class="hover:text-rose-600 transition">Inicial</a>
            <span>/</span>
            <a href="{{ route('marketplace.index') }}" class="hover:text-rose-600 transition">Acomodações</a>
            <span>/</span>
            <span class="text-gray-700 font-medium">Resultados</span>
        </nav>
        <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Resultados da busca</h1>
            <p class="text-gray-500">{{ $properties->total() }} {{ $properties->total() === 1 ? 'acomodação' : 'acomodações' }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($properties as $property)
                @php
                    $photo = $property->mainPhoto ?? $property->photos->first();
                    $photoUrl = $photo?->url ?? null;
                    if (!$photoUrl && is_array($property->stays_raw_data ?? null)) {
                        $mainMeta = $property->stays_raw_data['_t_mainImageMeta'] ?? null;
                        $photoUrl = is_array($mainMeta) ? ($mainMeta['url'] ?? null) : null;
                    }
                    if ($photoUrl && !\Illuminate\Support\Str::startsWith($photoUrl, ['http://', 'https://'])) {
                        $photoUrl = url($photoUrl);
                    }
                @endphp
                <a href="{{ route('marketplace.property.show', $property) }}" class="group block bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:border-gray-200 transition-all duration-300">
                    <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" alt="{{ $property->nome }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                            <div class="hidden absolute inset-0 bg-gray-200 flex items-center justify-center">
                                <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
                            </div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
                            </div>
                        @endif
                        @if($property->destaque)
                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-gray-900/80 text-white text-xs font-semibold rounded-lg backdrop-blur-sm">Preferido dos hóspedes</span>
                        @endif
                        <span class="absolute bottom-3 left-3 p-2 rounded-full bg-white/90 shadow hover:bg-white transition" aria-label="Compartilhar">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        </span>
                    </div>
                    <div class="p-4 md:p-5">
                        <h2 class="font-semibold text-gray-900 text-lg leading-snug group-hover:text-rose-600 transition line-clamp-2">{{ $property->titulo_marketing ?? $property->nome }}</h2>
                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
                            <span class="flex items-center gap-1"><svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>{{ $property->capacidade_hospedes }} {{ $property->capacidade_hospedes === 1 ? 'Hóspede' : 'Hóspedes' }}</span>
                            <span class="flex items-center gap-1"><svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>{{ $property->banheiros }} {{ $property->banheiros === 1 ? 'Banheiro' : 'Banheiros' }}</span>
                            <span class="flex items-center gap-1"><svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>{{ $property->quartos }} {{ $property->quartos === 1 ? 'Quarto' : 'Quartos' }}</span>
                            <span class="flex items-center gap-1"><svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>{{ $property->bairro ?? $property->cidade ?? 'Centro' }}</span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            @isset($avgPrices[$property->id])
                                <p class="text-sm text-gray-600">A partir de <span class="font-bold text-gray-900">R$ {{ number_format($avgPrices[$property->id], 2, ',', '.') }}</span> por noite</p>
                            @else
                                <p class="text-sm font-medium text-gray-600">Preço sob consulta</p>
                            @endisset
                        </div>
                        <div class="mt-4">
                            <span class="inline-block w-full py-2.5 text-center rounded-xl bg-gray-100 group-hover:bg-gray-200 text-gray-700 font-medium text-sm transition">Detalhes</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-20 px-6 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <p class="text-gray-600 text-lg font-medium">Nenhum imóvel encontrado</p>
                    <p class="text-sm text-gray-500 mt-1">Ajuste os filtros ou datas e tente novamente.</p>
                    <a href="{{ route('marketplace.index') }}" class="inline-block mt-6 px-6 py-3 bg-rose-500 text-white font-semibold rounded-xl hover:bg-rose-600 transition">Ver todas as acomodações</a>
                </div>
            @endforelse
        </div>
        @if($properties->hasPages())
            <div class="mt-12 flex justify-center">{{ $properties->links() }}</div>
        @endif
    </main>

    @include('marketplace.partials.footer')
</body>
</html>
