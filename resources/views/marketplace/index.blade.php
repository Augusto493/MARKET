<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aluguel por Temporada em Balneário Camboriú — Hospedavoce</title>
    <meta name="description" content="Alugue apartamentos e casas de temporada em Balneário Camboriú com os melhores preços. Mais de {{ $properties->total() }} opções disponíveis para você!">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%23f43f5e'/%3E%3Ctext x='16' y='22' font-family='system-ui' font-size='18' font-weight='bold' fill='white' text-anchor='middle'%3EH%3C/text%3E%3C/svg%3E" sizes="32x32">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Fallback: evita tela toda branca quando Tailwind/Vite não carrega */
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; background-color: #f3f4f6 !important; color: #111827 !important; }
        body * { box-sizing: border-box; }
        html, body { overflow-x: hidden; max-width: 100vw; }
        /* Mobile: formulário de busca em coluna, sem overflow */
        @media (max-width: 639px) {
            .hero-section { min-height: auto; padding-top: 1.5rem; padding-bottom: 1.5rem; }
            .search-bar .flex { flex-direction: column; }
            .search-bar .flex > * { width: 100% !important; max-width: 100% !important; min-width: 0 !important; }
            .search-bar .btn-buscar { width: 100%; }
        }
        header { background-color: #1e3a5f !important; color: #fff !important; }
        header a { color: rgba(255,255,255,0.95) !important; }
        header a:hover { color: #fff !important; background-color: rgba(255,255,255,0.1) !important; }
        main { color: #111827 !important; }
        main h1, main h2, main h3 { color: #111827 !important; }
        main p, main span { color: #374151 !important; }
        .search-bar { background: #fff !important; border: 1px solid #e5e7eb !important; }
        .search-bar label { color: #4b5563 !important; }
        .search-bar input, .search-bar select { background: #fff !important; border: 1px solid #d1d5db !important; color: #111827 !important; }
        .property-card { background: #fff !important; border: 1px solid #e5e7eb !important; }
        .property-card h3 { color: #111827 !important; }
        footer { background: #0f172a !important; color: #9ca3af !important; }
        footer a { color: #d1d5db !important; }
        /* Barra de stats (10+ Acomodações, etc.) */
        body > div.bg-white.border-b { background: #fff !important; border-bottom: 1px solid #e5e7eb !important; }

        /* Hero */
        .hero-section {
            background-image: linear-gradient(to bottom, rgba(0,0,0,0.50) 0%, rgba(0,0,0,0.30) 50%, rgba(0,0,0,0.55) 100%),
                url('{{ asset("images/hero-beach.jpg") }}');
            background-size: cover;
            background-position: center 45%;
            min-height: 520px;
        }

        /* Evitar ícones/emojis gigantes em qualquer tela */
        .search-bar label svg,
        .search-bar label .label-icon { width: 1rem; height: 1rem; max-width: 16px; max-height: 16px; display: inline-block; vertical-align: middle; }
        .property-card svg { width: 0.875rem; height: 0.875rem; max-width: 14px; max-height: 14px; }
        main svg { max-width: 48px; max-height: 48px; }
        .stat-number + .text-xs { font-size: 0.75rem; }
        /* Seção "Por que escolher" e CTA – ícones com tamanho controlado */
        .section-porque .icon-box svg { width: 2rem; height: 2rem; max-width: 32px; max-height: 32px; }
        .whatsapp-btn svg { width: 1.5rem; height: 1.5rem; max-width: 24px; max-height: 24px; }

        /* Card hover efeito */
        .property-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .property-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        .property-card img { transition: transform 0.5s ease; }
        .property-card:hover img { transform: scale(1.06); }

        /* Badge destaque */
        .badge-destaque {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
            padding: 4px 10px;
            border-radius: 20px;
        }

        /* Botão WhatsApp pulse */
        .whatsapp-btn { animation: pulseGreen 2.5s infinite; }
        @keyframes pulseGreen {
            0%, 100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.5); }
            50% { box-shadow: 0 0 0 10px rgba(37, 211, 102, 0); }
        }

        /* Stats contador */
        .stat-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Filtro ativo */
        .filter-chip.active { background: #1e3a5f; color: white; }
        .filter-chip { cursor: pointer; transition: all 0.2s; }

        /* Search bar flutuante */
        .search-bar {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.5);
        }

        /* Botão Buscar – sempre visível */
        .search-bar .btn-buscar {
            background-color: #f43f5e !important;
            color: #fff !important;
            min-height: 48px;
        }
        .search-bar .btn-buscar:hover { background-color: #e11d48 !important; }
        .search-bar .btn-buscar svg { color: inherit; fill: none; stroke: currentColor; }

        /* Gradient nos cards sem foto */
        .no-photo-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* Footer gradient */
        .footer-gradient {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
        }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900">

    {{-- ═══ HEADER ═══ --}}
    @include('marketplace.partials.header')

    {{-- ═══ HERO SECTION ═══ --}}
    <section class="hero-section relative flex flex-col items-center justify-center text-center w-full overflow-hidden px-3 sm:px-4 py-12 sm:py-20">
        <div class="relative z-10 w-full max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 sm:px-4 rounded-full bg-white/15 backdrop-blur-sm border border-white/20 text-white/90 text-xs sm:text-sm font-medium mb-4 sm:mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                {{ $properties->total() }} acomodações disponíveis
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-extrabold text-white leading-tight drop-shadow-lg mb-3 sm:mb-4 px-1">
                Encontre seu<br>
                <span style="color:#ff8a65;">refúgio perfeito</span> em BC
            </h1>
            <p class="text-base sm:text-lg text-white/85 max-w-xl mx-auto mb-6 sm:mb-10 drop-shadow px-1">
                Apartamentos e casas premium em Balneário Camboriú. Reserve direto, sem taxas extras.
            </p>
        </div>

        {{-- SEARCH FORM --}}
        <div class="relative z-10 w-full max-w-5xl mx-auto px-0 sm:px-4">
            <form action="{{ route('marketplace.index') }}" method="get" id="busca" class="search-bar rounded-2xl shadow-2xl p-4 md:p-5 w-full max-w-full overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-end">
                    <div class="w-full sm:flex-1 sm:min-w-0 min-w-0">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Destino
                        </label>
                        <input type="text" name="cidade"
                               placeholder="Balneário Camboriú"
                               value="{{ request('cidade', 'Balneário Camboriú') }}"
                               class="w-full min-w-0 rounded-xl border border-gray-200 px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition text-sm">
                    </div>
                    <div class="w-full sm:flex-1 sm:min-w-0 min-w-0">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Check-in
                        </label>
                        <input type="date" name="checkin" value="{{ request('checkin') }}"
                               class="w-full min-w-0 rounded-xl border border-gray-200 px-4 py-3 text-gray-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition text-sm">
                    </div>
                    <div class="w-full sm:flex-1 sm:min-w-0 min-w-0">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Check-out
                        </label>
                        <input type="date" name="checkout" value="{{ request('checkout') }}"
                               class="w-full min-w-0 rounded-xl border border-gray-200 px-4 py-3 text-gray-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition text-sm">
                    </div>
                    <div class="w-full sm:w-36 min-w-0">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Pessoas
                        </label>
                        <select name="hospedes"
                                class="w-full min-w-0 rounded-xl border border-gray-200 px-4 py-3 text-gray-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition text-sm">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ (int) request('hospedes', 2) === $i ? 'selected' : '' }}>
                                    {{ $i }} {{ $i === 1 ? 'pessoa' : 'pessoas' }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" name="buscar"
                            class="btn-buscar w-full sm:w-auto px-6 py-3 sm:px-8 bg-rose-500 hover:bg-rose-600 active:bg-rose-700 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition flex items-center justify-center gap-2 text-sm whitespace-nowrap shrink-0">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Buscar</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Link localizar reserva --}}
        <p class="relative z-10 mt-5">
            <a href="{{ route('marketplace.locate-reservation') }}"
               class="text-white/80 hover:text-white text-sm underline underline-offset-4 transition">
                Já tem reserva? Localize aqui
            </a>
        </p>
    </section>

    {{-- ═══ STATS BAR ═══ --}}
    <div class="bg-white border-b border-gray-100 shadow-sm overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center justify-center gap-4 sm:gap-8 md:gap-16 text-center">
                <div>
                    <div class="text-xl sm:text-2xl font-black text-rose-500">{{ $properties->total() }}+</div>
                    <div class="text-xs text-gray-500 font-medium mt-0.5">Acomodações</div>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-black text-rose-500">100%</div>
                    <div class="text-xs text-gray-500 font-medium mt-0.5">Verificadas</div>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-black text-rose-500">0%</div>
                    <div class="text-xs text-gray-500 font-medium mt-0.5">Taxa extra</div>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-black text-rose-500">24/7</div>
                    <div class="text-xs text-gray-500 font-medium mt-0.5">Suporte</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ LISTAGEM PRINCIPAL ═══ --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Cabeçalho da listagem + ordenação --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    @if(request('cidade') && request('cidade') !== 'Balneário Camboriú')
                        Acomodações em {{ request('cidade') }}
                    @else
                        Nossas Acomodações
                    @endif
                </h2>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $properties->total() }} {{ $properties->total() === 1 ? 'acomodação encontrada' : 'acomodações encontradas' }}
                    @if(request('checkin') && request('checkout'))
                        · {{ \Carbon\Carbon::parse(request('checkin'))->format('d/m') }} → {{ \Carbon\Carbon::parse(request('checkout'))->format('d/m') }}
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                <select onchange="window.location=this.value"
                        class="text-sm rounded-xl border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition text-gray-700">
                    <option value="{{ request()->fullUrlWithQuery(['ordem' => '']) }}">Mais relevantes</option>
                    <option value="{{ request()->fullUrlWithQuery(['ordem' => 'preco_asc']) }}" {{ request('ordem') === 'preco_asc' ? 'selected' : '' }}>Menor preço</option>
                    <option value="{{ request()->fullUrlWithQuery(['ordem' => 'preco_desc']) }}" {{ request('ordem') === 'preco_desc' ? 'selected' : '' }}>Maior preço</option>
                </select>
                <a href="{{ route('login') }}"
                   class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-rose-500 bg-rose-500 text-white hover:bg-rose-600 hover:border-rose-600 text-sm font-bold transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Anuncie seu imóvel
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-200 flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- GRID DE CARDS --}}
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
                        $photoUrl = asset($photoUrl);
                    }
                @endphp
                <a href="{{ route('marketplace.property.show', $property) }}"
                   class="property-card group block bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm">

                    {{-- FOTO --}}
                    <div style="position:relative; width:100%; padding-top:66.67%; overflow:hidden; background:linear-gradient(135deg,#667eea,#764ba2);">
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}"
                                 alt="{{ $property->nome }}"
                                 loading="lazy"
                                 style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;"
                                 onerror="this.parentElement.style.background='linear-gradient(135deg,#667eea,#764ba2)';this.remove()">
                        @endif

                        {{-- Badge destaque --}}
                        @if($property->destaque)
                            <span class="badge-destaque flex items-center gap-1 w-fit" style="position:absolute;top:12px;left:12px;">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                Preferido
                            </span>
                        @endif

                        {{-- Overlay gradiente na base para legibilidade --}}
                        <div style="position:absolute;bottom:0;left:0;right:0;height:60px;background:linear-gradient(to top,rgba(0,0,0,0.35),transparent);"></div>

                        {{-- Localização no canto inferior --}}
                        @if($property->bairro || $property->cidade)
                            <span style="position:absolute;bottom:10px;left:12px;color:white;font-size:12px;font-weight:600;text-shadow:0 1px 3px rgba(0,0,0,0.5);" class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0 opacity-90" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
                                {{ $property->bairro ?? $property->cidade }}
                            </span>
                        @endif
                    </div>

                    {{-- CONTEÚDO DO CARD --}}
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 text-base leading-snug group-hover:text-rose-600 transition line-clamp-2 mb-2">
                            {{ $property->titulo_marketing ?? $property->nome }}
                        </h3>

                        {{-- Specs com separador · --}}
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500 mb-3">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20H7m10 0v-2a3 3 0 00-5.356-1.857M17 20v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $property->capacidade_hospedes }} hóspedes
                            </span>
                            <span class="text-gray-300">·</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                {{ $property->quartos }} {{ $property->quartos === 1 ? 'quarto' : 'quartos' }}
                            </span>
                            <span class="text-gray-300">·</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                </svg>
                                {{ $property->banheiros }} {{ $property->banheiros === 1 ? 'banheiro' : 'banheiros' }}
                            </span>
                        </div>

                        {{-- Preço --}}
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                            @isset($avgPrices[$property->id])
                                <div>
                                    <span class="text-xs text-gray-400">a partir de</span><br>
                                    <span class="font-bold text-gray-900 text-base">R$ {{ number_format($avgPrices[$property->id], 2, ',', '.') }}</span>
                                    <span class="text-xs text-gray-400">/noite</span>
                                </div>
                            @else
                                <span class="text-sm text-gray-500 font-medium">Consulte disponibilidade</span>
                            @endisset
                            <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold transition">
                                Ver →
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-20 px-6 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="text-gray-600 text-lg font-semibold">Nenhum imóvel encontrado</p>
                    <p class="text-sm text-gray-400 mt-1">Tente outras datas ou destino.</p>
                    <a href="{{ route('marketplace.index') }}" class="inline-block mt-4 px-5 py-2.5 bg-rose-500 text-white rounded-xl font-semibold text-sm hover:bg-rose-600 transition">
                        Ver todas as acomodações
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Paginação --}}
        @if($properties->hasPages())
            <div class="mt-12 flex justify-center">{{ $properties->links() }}</div>
        @endif
    </main>

    {{-- ═══ SEÇÃO "POR QUE ESCOLHER" ═══ --}}
    <section class="section-porque bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">Por que alugar com a HospedaBC?</h2>
                <p class="text-gray-500 mt-2 max-w-xl mx-auto">Trabalhamos para que sua estadia em Balneário Camboriú seja inesquecível.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center group">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-rose-50 flex items-center justify-center mx-auto mb-4 group-hover:bg-rose-100 transition">
                        <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Reserva Segura</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Pagamento seguro e confirmação imediata. Sua reserva é garantida.</p>
                </div>
                <div class="text-center group">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-100 transition">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Imóveis Completos</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Todos os imóveis são verificados e equipados com o essencial para a sua estadia.</p>
                </div>
                <div class="text-center group">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4 group-hover:bg-emerald-100 transition">
                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Suporte Local 24/7</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Equipe local sempre presente para resolver qualquer situação rapidamente.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ CTA WHATSAPP ═══ --}}
    <section style="background:linear-gradient(135deg,#1e3a5f 0%,#15294a 100%); padding: 64px 16px;">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-3">Precisa de ajuda para escolher?</h2>
            <p class="text-white/70 mb-8 text-base">Nossa equipe está pronta para ajudá-lo a encontrar o imóvel perfeito para sua viagem a Balneário Camboriú.</p>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('hospedabc.whatsapp_number', '5547999256744')) }}?text=Olá!%20Preciso%20de%20ajuda%20para%20encontrar%20um%20imóvel."
               target="_blank" rel="noopener"
               class="whatsapp-btn inline-flex items-center gap-3 px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl transition text-base shadow-xl">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Falar pelo WhatsApp
            </a>
        </div>
    </section>

    {{-- ═══ FOOTER ═══ --}}
    <footer class="footer-gradient text-gray-400 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-rose-500 flex items-center justify-center text-white font-bold text-sm shadow">H</span>
                    <div>
                        <div class="text-white font-bold text-base">HospedaBC</div>
                        <div class="text-xs text-gray-500">Balneário Camboriú · SC</div>
                    </div>
                </div>
                <nav class="flex flex-wrap items-center justify-center gap-6 text-sm">
                    <a href="{{ route('marketplace.index') }}" class="hover:text-white transition">Acomodações</a>
                    <a href="{{ route('marketplace.locate-reservation') }}" class="hover:text-white transition">Localizar Reserva</a>
                    <a href="{{ route('login') }}" class="hover:text-white transition">Anunciar Imóvel</a>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('hospedabc.whatsapp_number', '5547999256744')) }}"
                       target="_blank" rel="noopener" class="hover:text-emerald-400 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        WhatsApp
                    </a>
                </nav>
            </div>
            <div class="mt-8 pt-6 border-t border-gray-800 text-center text-xs text-gray-600">
                © {{ date('Y') }} HospedaBC · Todos os direitos reservados · Balneário Camboriú, SC
            </div>
        </div>
    </footer>

</body>
</html>
