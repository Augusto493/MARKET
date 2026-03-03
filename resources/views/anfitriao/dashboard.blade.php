<x-anfitriao-layout>
    <x-slot name="header">Dashboard</x-slot>

    <p class="text-slate-600 mb-8">Olá, <strong>{{ $owner->nome }}</strong>. Conecte sua conta Stays e importe seus imóveis para publicá-los no marketplace.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Imóveis</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $owner->properties_count }}</p>
            <a href="{{ route('anfitriao.imoveis') }}" class="mt-3 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-700">Ver imóveis →</a>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Reservas</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $owner->reservations_count }}</p>
            <a href="{{ route('anfitriao.reservas') }}" class="mt-3 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-700">Ver reservas →</a>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Última sincronização</p>
            <p class="text-lg font-semibold text-slate-800 mt-1">{{ $owner->last_sync_at ? $owner->last_sync_at->format('d/m/Y H:i') : 'Nunca' }}</p>
            <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full {{ $owner->sync_status === 'ok' ? 'bg-emerald-100 text-emerald-800' : ($owner->sync_status === 'erro' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-600') }}">{{ $owner->sync_status }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Conectar Stays e importar imóveis</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('anfitriao.conectar-stays') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-sm">Conectar Stays</a>
                <form action="{{ route('anfitriao.sincronizar') }}" method="post" class="inline">@csrf<button type="submit" class="inline-flex items-center px-5 py-2.5 bg-slate-700 text-white font-medium rounded-lg hover:bg-slate-800 shadow-sm">Importar imóveis agora</button></form>
            </div>
        </div>
        <div class="p-6">
            <p class="text-slate-600 text-sm">Configure suas credenciais da Stays.net em "Conectar Stays". Se deixar em branco, o sistema usa dados de demonstração (modo mock) para você testar.</p>
            @if($owner->last_sync_error)
                <p class="text-red-600 text-sm mt-3">Último erro: {{ $owner->last_sync_error }}</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <h2 class="px-6 py-4 border-b border-slate-200 text-lg font-semibold text-slate-800">Últimos logs de sincronização</h2>
        @if($owner->syncLogs->count())
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Data</th><th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th><th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Criadas / Atualizadas</th></tr></thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($owner->syncLogs as $log)
                        <tr><td class="px-6 py-3 text-sm text-slate-600">{{ $log->started_at->format('d/m/Y H:i') }}</td><td class="px-6 py-3"><span class="px-2 py-1 text-xs rounded-full {{ $log->status === 'success' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">{{ $log->status }}</span></td><td class="px-6 py-3 text-sm text-slate-600">{{ $log->properties_created }} / {{ $log->properties_updated }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="px-6 py-8 text-slate-500 text-center">Nenhum log ainda. Clique em "Importar imóveis agora" para sincronizar.</p>
        @endif
    </div>
</x-anfitriao-layout>
