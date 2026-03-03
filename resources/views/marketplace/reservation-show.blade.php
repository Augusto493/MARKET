<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reserva {{ $reservation->codigo ?? '#' . $reservation->id }} - HospedaBC</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; } </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    @include('marketplace.partials.header')

    <main class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-200">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 md:p-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Solicitação de reserva recebida</h1>
            @if($reservation->codigo ?? null)
                <p class="text-gray-600 mb-6">Código da reserva: <strong class="text-rose-600">{{ $reservation->codigo }}</strong></p>
            @endif
            <p class="text-gray-600 mb-6">Número: <strong>#{{ $reservation->id }}</strong></p>

            <div class="space-y-3 text-gray-700">
                <p><strong>Imóvel:</strong> {{ $reservation->property->titulo_marketing ?? $reservation->property->nome }}</p>
                <p><strong>Check-in:</strong> {{ $reservation->checkin_date->format('d/m/Y') }}</p>
                <p><strong>Check-out:</strong> {{ $reservation->checkout_date->format('d/m/Y') }}</p>
                <p><strong>Total:</strong> R$ {{ number_format($reservation->final_total, 2, ',', '.') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($reservation->status) }}</p>
            </div>

            <p class="mt-6 text-gray-600">Entraremos em contato em breve para confirmar sua reserva.</p>
            <a href="{{ route('marketplace.index') }}" class="inline-block mt-6 px-6 py-3 bg-rose-500 text-white font-semibold rounded-xl hover:bg-rose-600 transition">Ver mais imóveis</a>
        </div>
    </main>

    @include('marketplace.partials.footer')
</body>
</html>
