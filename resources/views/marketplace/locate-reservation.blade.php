<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Localizar Reserva - HospedaBC</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; } </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    @include('marketplace.partials.header')

    <main class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Buscar pelo código</h1>
        <p class="text-gray-600 mb-6">Digite o código da sua reserva (ex.: RES-XXXXXXXX) para visualizar os detalhes.</p>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-xl border border-red-200">{{ session('error') }}</div>
        @endif

        <form action="" method="get" id="locate-form" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Código da reserva</label>
            <input type="text" name="codigo" placeholder="RES-XXXXXXXX" value="{{ old('codigo') }}"
                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                required maxlength="20">
            <button type="submit" class="mt-4 w-full py-3 bg-rose-500 text-white font-semibold rounded-xl hover:bg-rose-600 transition">Localizar Reserva</button>
        </form>

        <script>
            document.getElementById('locate-form').addEventListener('submit', function(e) {
                e.preventDefault();
                var codigo = this.querySelector('input[name="codigo"]').value.trim().toUpperCase();
                if (codigo) {
                    window.location.href = '{{ url("marketplace/reserva/codigo") }}/' + encodeURIComponent(codigo);
                }
            });
        </script>

        <p class="mt-6 text-center text-sm text-gray-500">
            <a href="{{ route('marketplace.index') }}" class="text-rose-600 hover:text-rose-700 font-medium">Voltar ao início</a>
        </p>
    </main>

    @include('marketplace.partials.footer')
</body>
</html>
