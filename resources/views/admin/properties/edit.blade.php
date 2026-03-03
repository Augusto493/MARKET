<x-admin-layout>
    <x-slot name="header">Editar imóvel</x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('admin.properties.index') }}" class="text-indigo-600 hover:underline mb-4 inline-block">← Voltar</a>

        <form action="{{ route('admin.properties.update', $property) }}" method="post" class="bg-white shadow rounded-lg p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome *</label>
                    <input type="text" name="nome" value="{{ old('nome', $property->nome) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="descricao" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('descricao', $property->descricao) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Título marketing</label>
                    <input type="text" name="titulo_marketing" value="{{ old('titulo_marketing', $property->titulo_marketing) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center"><input type="checkbox" name="publicado_marketplace" value="1" {{ old('publicado_marketplace', $property->publicado_marketplace) ? 'checked' : '' }} class="rounded border-gray-300"> Publicado no marketplace</label>
                    <label class="flex items-center"><input type="checkbox" name="ativo" value="1" {{ old('ativo', $property->ativo) ? 'checked' : '' }} class="rounded border-gray-300"> Ativo</label>
                    <label class="flex items-center"><input type="checkbox" name="destaque" value="1" {{ old('destaque', $property->destaque) ? 'checked' : '' }} class="rounded border-gray-300"> Destaque</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prioridade</label>
                    <input type="number" name="prioridade" value="{{ old('prioridade', $property->prioridade) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </div>
            <div class="mt-6 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Salvar</button>
                <a href="{{ route('admin.properties.index') }}" class="px-4 py-2 border border-gray-300 rounded-md">Cancelar</a>
            </div>
        </form>
    </div>
</x-admin-layout>
