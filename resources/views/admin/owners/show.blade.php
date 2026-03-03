<x-admin-layout>
    <x-slot name="header">Owner: {{ $owner->nome }}</x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('admin.owners.index') }}" class="text-indigo-600 hover:underline mb-4 inline-block">← Voltar</a>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <p><strong>Email:</strong> {{ $owner->email }}</p>
            <p><strong>Telefone:</strong> {{ $owner->telefone }}</p>
            <p><strong>Sync:</strong> {{ $owner->sync_status }} @if($owner->last_sync_at) ({{ $owner->last_sync_at->format('d/m/Y H:i') }}) @endif</p>
            @if($owner->last_sync_error)
                <p class="text-red-600">
                    @if(preg_match('/^\s*<\s*!?\s*doctype\s+html/i', $owner->last_sync_error))
                        Erro de sincronização (o servidor retornou uma página de erro). Use «Testar conexão» para atualizar a mensagem.
                    @else
                        {{ Str::limit($owner->last_sync_error, 500) }}
                    @endif
                </p>
            @endif
            <div class="mt-4 flex gap-2">
                <a href="{{ route('admin.owners.edit', $owner) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Editar</a>
                <form action="{{ route('admin.owners.test-connection', $owner) }}" method="post" class="inline">@csrf<button type="submit" class="px-4 py-2 border border-gray-300 rounded-md">Testar conexão</button></form>
                <form action="{{ route('admin.owners.sync', $owner) }}" method="post" class="inline">@csrf<button type="submit" class="px-4 py-2 border border-green-600 text-green-700 rounded-md">Importar imóveis</button></form>
            </div>
        </div>

        <h3 class="font-medium mb-2">Imóveis ({{ $owner->properties->count() }})</h3>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nome</th><th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Publicado</th><th></th></tr></thead>
                <tbody>
                    @foreach($owner->properties as $p)
                        <tr><td class="px-4 py-2">{{ $p->nome }}</td><td class="px-4 py-2">{{ $p->publicado_marketplace ? 'Sim' : 'Não' }}</td><td><a href="{{ route('admin.properties.show', $p) }}" class="text-indigo-600">Ver</a></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h3 class="font-medium mt-6 mb-2">Últimos logs de sync</h3>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Data</th><th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th><th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Criadas/Atualizadas</th></tr></thead>
                <tbody>
                    @foreach($owner->syncLogs as $log)
                        <tr><td class="px-4 py-2">{{ $log->started_at->format('d/m/Y H:i') }}</td><td class="px-4 py-2">{{ $log->status }}</td><td class="px-4 py-2">{{ $log->properties_created }}/{{ $log->properties_updated }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
