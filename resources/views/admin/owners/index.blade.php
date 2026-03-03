<x-admin-layout>
    <x-slot name="header">Owners (Clientes Stays)</x-slot>

    <div class="flex justify-end mb-6">
        <a href="{{ route('admin.owners.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-md hover:bg-indigo-700 border-2 border-indigo-600 hover:border-indigo-700 transition">Novo owner</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nome</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Anfitrião (login)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Sync</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Imóveis</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($owners as $owner)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $owner->nome }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $owner->user ? $owner->user->email : '—' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $owner->email }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $owner->sync_status === 'ok' ? 'bg-emerald-100 text-emerald-800' : ($owner->sync_status === 'erro' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-600') }}">{{ $owner->sync_status }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $owner->properties_count }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.owners.show', $owner) }}" class="text-indigo-600 hover:underline">Ver</a>
                                <a href="{{ route('admin.owners.edit', $owner) }}" class="text-indigo-600 hover:underline ml-2">Editar</a>
                                <form action="{{ route('admin.owners.test-connection', $owner) }}" method="post" class="inline ml-2">
                                    @csrf
                                    <button type="submit" class="text-indigo-600 hover:underline">Testar</button>
                                </form>
                                <form action="{{ route('admin.owners.sync', $owner) }}" method="post" class="inline ml-2">
                                    @csrf
                                    <button type="submit" class="text-indigo-600 hover:underline">Importar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Nenhum owner. <a href="{{ route('admin.owners.create') }}" class="text-indigo-600 font-medium">Cadastrar</a> e use "Importar" para trazer imóveis.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-2">{{ $owners->links() }}</div>
        </div>
    </div>
</x-admin-layout>
