<x-admin-layout>
    <x-slot name="header">Imóveis</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Imóvel</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Publicado</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($properties as $p)
                        <tr>
                            <td class="px-4 py-2 font-medium">{{ $p->nome }}</td>
                            <td class="px-4 py-2">{{ $p->owner->nome ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $p->publicado_marketplace ? 'Sim' : 'Não' }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.properties.show', $p) }}" class="text-indigo-600 hover:underline">Ver</a>
                                <a href="{{ route('admin.properties.edit', $p) }}" class="text-indigo-600 hover:underline ml-2">Editar</a>
                                <form action="{{ route('admin.properties.toggle-status', $p) }}" method="post" class="inline ml-2">@csrf<button type="submit" class="text-indigo-600 hover:underline">{{ $p->publicado_marketplace ? 'Despublicar' : 'Publicar' }}</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Nenhum imóvel. Cadastre um Owner e use "Importar imóveis" para sincronizar (modo mock).</td></tr>
                    @endforelse
                </tbody>
            </table>
        <div class="px-4 py-2">{{ $properties->links() }}</div>
    </div>
</x-admin-layout>
