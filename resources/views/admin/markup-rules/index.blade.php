<x-admin-layout>
    <x-slot name="header">Regras de preço (markup)</x-slot>

    <div class="mb-4"><a href="{{ route('admin.markup-rules.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">Nova regra</a></div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Markup</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ativo</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($rules as $rule)
                        <tr>
                            <td class="px-4 py-2 font-medium">{{ $rule->nome }}</td>
                            <td class="px-4 py-2">{{ $rule->tipo }}</td>
                            <td class="px-4 py-2">{{ $rule->markup_type === 'percent' ? $rule->markup_value . '%' : 'R$ ' . number_format($rule->markup_value, 2, ',', '.') }}</td>
                            <td class="px-4 py-2">{{ $rule->ativo ? 'Sim' : 'Não' }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.markup-rules.edit', $rule) }}" class="text-indigo-600 hover:underline">Editar</a>
                                <form action="{{ route('admin.markup-rules.destroy', $rule) }}" method="post" class="inline ml-2" onsubmit="return confirm('Remover esta regra?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:underline">Excluir</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Nenhuma regra. <a href="{{ route('admin.markup-rules.create') }}" class="text-indigo-600">Criar regra</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        <div class="px-4 py-2">{{ $rules->links() }}</div>
    </div>
</x-admin-layout>
