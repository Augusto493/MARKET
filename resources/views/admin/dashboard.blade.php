<x-admin-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Imóveis publicados</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $propertiesCount }}</p>
            <a href="{{ route('admin.properties.index') }}" class="mt-2 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-700">Ver →</a>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Reservas</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $reservationsCount }}</p>
            <a href="{{ route('admin.reservations.index') }}" class="mt-2 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-700">Ver →</a>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Owners ativos</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $ownersCount }}</p>
            <a href="{{ route('admin.owners.index') }}" class="mt-2 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-700">Ver →</a>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Erros de sync (7 dias)</p>
            <p class="text-3xl font-bold {{ $errosSync > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-1">{{ $errosSync }}</p>
            @if($errosSync > 0)<a href="{{ route('admin.owners.index') }}" class="mt-2 inline-block text-sm font-medium text-amber-700 hover:text-amber-800">Ver owners e logs →</a>@endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Receita bruta</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">R$ {{ number_format($receitaBruta, 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Markup estimado</p>
            <p class="text-xl font-bold text-slate-800 mt-1">R$ {{ number_format($markupGanho, 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Taxa conversão (leads → confirmadas)</p>
            <p class="text-xl font-bold text-slate-800 mt-1">{{ $taxaConversao }}%</p>
        </div>
    </div>

    @if(isset($ownersWithCounts) && $ownersWithCounts->isNotEmpty())
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <h2 class="px-6 py-4 border-b border-slate-200 text-lg font-semibold text-slate-800">Por anfitrião</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nome</th><th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Imóveis</th><th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Sync</th><th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Ações</th></tr></thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($ownersWithCounts as $o)
                            <tr>
                                <td class="px-6 py-3 font-medium text-slate-800">{{ $o->nome }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $o->properties_count }}</td>
                                <td class="px-6 py-3"><span class="px-2 py-1 text-xs rounded-full {{ $o->sync_status === 'ok' ? 'bg-emerald-100 text-emerald-800' : ($o->sync_status === 'erro' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-600') }}">{{ $o->sync_status ?? '—' }}</span></td>
                                <td class="px-6 py-3"><a href="{{ route('admin.owners.show', $o) }}" class="text-indigo-600 hover:underline text-sm">Ver</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-admin-layout>
