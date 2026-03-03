<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <title>{{ $property->titulo_marketing ?? $property->nome }} - HospedaBC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .property-gallery { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
        @media (min-width: 640px) { .property-gallery { grid-template-columns: repeat(3, 1fr); gap: 0.5rem; } }
        @media (min-width: 1024px) { .property-gallery { grid-template-columns: repeat(4, 1fr); gap: 0.75rem; } }
        .property-gallery-item { aspect-ratio: 4/3; border-radius: 0.75rem; overflow: hidden; background: #f3f4f6; }
        .property-gallery-item img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    @include('marketplace.partials.header')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6 flex-wrap">
            <a href="{{ route('marketplace.index') }}" class="hover:text-rose-600 transition">Inicial</a>
            <span aria-hidden="true">›</span>
            <a href="{{ route('marketplace.index') }}" class="hover:text-rose-600 transition">Acomodações</a>
            <span aria-hidden="true">›</span>
            <span class="text-gray-700 font-medium truncate max-w-[180px] sm:max-w-none">{{ $property->nome }}</span>
        </nav>

        {{-- Título e resumo --}}
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $property->titulo_marketing ?? $property->nome }}</h1>
        <p class="text-gray-600 mb-6 flex flex-wrap gap-x-2 gap-y-1">
            <span>{{ $property->quartos }} quartos</span>
            <span class="text-gray-400">·</span>
            <span>{{ $property->capacidade_hospedes }} hóspedes</span>
            <span class="text-gray-400">·</span>
            <span>{{ $property->banheiros }} banheiros</span>
            <span class="text-gray-400">·</span>
            <span>{{ $property->bairro ?? $property->cidade ?? 'Centro' }}</span>
        </p>

        {{-- Galeria: fotos pequenas e organizadas em grid --}}
        @php
            $mainPhoto = $property->mainPhoto ?? $property->photos->first();
            $mainUrl = $mainPhoto?->url ?? null;
            if (!$mainUrl && is_array($property->stays_raw_data ?? null)) {
                $mainMeta = $property->stays_raw_data['_t_mainImageMeta'] ?? null;
                $mainUrl = is_array($mainMeta) ? ($mainMeta['url'] ?? null) : null;
            }
            if ($mainUrl && !\Illuminate\Support\Str::startsWith($mainUrl, ['http://', 'https://'])) {
                $mainUrl = url($mainUrl);
            }
            $galleryPhotos = $property->photos->filter(fn($p) => !empty($p->url));
            if ($mainUrl && $galleryPhotos->isEmpty()) {
                $galleryPhotos = collect([(object)['url' => $mainUrl]]);
            } elseif ($mainUrl && ($galleryPhotos->isEmpty() || $galleryPhotos->first()->url !== $mainUrl)) {
                $galleryPhotos = $galleryPhotos->prepend((object)['url' => $mainUrl]);
            }
        @endphp
        @if($galleryPhotos->isNotEmpty())
            <section class="mb-8" aria-label="Fotos do imóvel">
                <h2 class="sr-only">Fotos do imóvel</h2>
                <div class="property-gallery">
                    @foreach($galleryPhotos as $index => $photo)
                        <div class="property-gallery-item">
                            <img src="{{ $photo->url }}" alt="{{ $property->nome }} — foto {{ $index + 1 }}" loading="{{ $index < 6 ? 'eager' : 'lazy' }}" onerror="this.onerror=null; this.parentElement.classList.add('bg-gray-200'); this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%23d1d5db%22%3E%3Cpath d=%22M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z%22/%3E%3C/svg%3E';">
                        </div>
                    @endforeach
                </div>
            </section>
        @else
            <div class="mb-8 rounded-2xl overflow-hidden bg-gray-200 h-48 sm:h-64 flex items-center justify-center text-gray-400">
                <svg class="w-16 h-16 sm:w-20 sm:h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <div class="lg:col-span-2 space-y-5">
                @if($property->owner)
                    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
                        <h2 class="font-semibold text-base sm:text-lg text-gray-900 mb-1">Seu anfitrião</h2>
                        <p class="text-gray-600">{{ $property->owner->nome }}</p>
                    </section>
                @endif
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
                    <h2 class="font-semibold text-base sm:text-lg text-gray-900 mb-3">Descrição</h2>
                    <div class="prose prose-sm text-gray-600 max-w-none">{!! nl2br(e($property->descricao ?? 'Sem descrição.')) !!}</div>
                </section>
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
                    <h2 class="font-semibold text-base sm:text-lg text-gray-900 mb-3">O que este lugar oferece</h2>
                    <p class="text-gray-600 mb-3">{{ $property->quartos }} quartos · {{ $property->camas ?? '—' }} camas · {{ $property->banheiros }} banheiros · Até {{ $property->capacidade_hospedes }} hóspedes</p>
                    @if($property->amenities->count() > 0)
                        <ul class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-gray-600">
                            @foreach($property->amenities as $a)
                                <li class="flex items-center gap-2"><span class="text-rose-500">·</span> {{ $a->nome }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>
            <div class="lg:col-span-1">
                <aside class="bg-white rounded-2xl border border-gray-200 shadow-md p-5 sm:p-6 sticky top-20">
                    <div class="mb-4">
                        <span class="text-2xl font-bold text-gray-900" id="price-total">—</span>
                        <span class="text-sm text-gray-500"> total antes de impostos</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-4" id="price-detail">Selecione as datas para ver o preço.</p>
                    <form action="{{ route('marketplace.reservation.store') }}" method="post" id="reservation-form">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <div class="space-y-3">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label><input type="date" name="checkin" id="checkin" required class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label><input type="date" name="checkout" id="checkout" required class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Hóspedes</label><input type="number" name="guests_count" value="2" min="1" max="{{ $property->capacidade_hospedes }}" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></div>
                            <hr class="my-4 border-gray-100">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label><input type="text" name="guest_name" required class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email *</label><input type="email" name="guest_email" required class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label><input type="text" name="guest_phone" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></div>
                        </div>
                        <button type="submit" class="mt-6 w-full py-3.5 bg-rose-500 text-white font-bold rounded-xl hover:bg-rose-600 transition shadow-sm">Reservar</button>
                    </form>
                </aside>
            </div>
        </div>
    </main>

    @include('marketplace.partials.footer')

    <script>
        (function() {
            var form = document.getElementById('reservation-form');
            var checkin = document.getElementById('checkin');
            var checkout = document.getElementById('checkout');
            var priceTotal = document.getElementById('price-total');
            var priceDetail = document.getElementById('price-detail');
            var guestsInput = form.querySelector('input[name="guests_count"]');
            var propertyId = {{ $property->id }};
            var csrf = document.querySelector('meta[name="csrf-token"]').content;

            function formatBRL(n) {
                return 'R$ ' + (typeof n === 'number' ? n.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '—');
            }

            function updatePrice() {
                var cIn = checkin.value;
                var cOut = checkout.value;
                if (!cIn || !cOut || cIn >= cOut) {
                    priceTotal.textContent = '—';
                    priceDetail.textContent = 'Selecione as datas para ver o preço.';
                    return;
                }
                var guests = guestsInput ? parseInt(guestsInput.value, 10) : 2;
                if (isNaN(guests) || guests < 1) guests = 2;
                fetch('{{ route("marketplace.calculate.price") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ property_id: propertyId, checkin: cIn, checkout: cOut, guests_count: guests })
                }).then(function(r) { return r.json(); }).then(function(data) {
                    priceTotal.textContent = formatBRL(data.grand_total);
                    var perNight = data.nights > 0 ? data.final_total / data.nights : 0;
                    var parts = [formatBRL(perNight) + ' por noite', formatBRL(data.final_total) + ' (' + data.nights + ' noites)'];
                    if (data.cleaning_fee && data.cleaning_fee > 0) parts.push('Taxa de limpeza: ' + formatBRL(data.cleaning_fee));
                    priceDetail.textContent = parts.join(' · ');
                }).catch(function() {
                    priceTotal.textContent = '—';
                    priceDetail.textContent = 'Erro ao calcular. Tente outras datas.';
                });
            }

            checkin.addEventListener('change', updatePrice);
            checkout.addEventListener('change', updatePrice);
            if (guestsInput) guestsInput.addEventListener('change', updatePrice);
            if (checkin.value && checkout.value) updatePrice();
        })();
    </script>
</body>
</html>
