<x-admin-layout>
    <x-slot name="header">Editar regra de preço</x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('admin.markup-rules.index') }}" class="text-indigo-600 hover:underline mb-4 inline-block">← Voltar</a>

        <form action="{{ route('admin.markup-rules.update', $rule) }}" method="post" class="bg-white shadow rounded-lg p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome *</label>
                    <input type="text" name="nome" value="{{ old('nome', $rule->nome) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo *</label>
                    <select name="tipo" id="tipo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="global" {{ old('tipo', $rule->tipo) === 'global' ? 'selected' : '' }}>Global</option>
                        <option value="owner" {{ old('tipo', $rule->tipo) === 'owner' ? 'selected' : '' }}>Por owner</option>
                        <option value="property" {{ old('tipo', $rule->tipo) === 'property' ? 'selected' : '' }}>Por imóvel</option>
                    </select>
                </div>
                <div id="owner_field" style="display:{{ $rule->tipo === 'owner' ? 'block' : 'none' }}">
                    <label class="block text-sm font-medium text-gray-700">Owner</label>
                    <select name="owner_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">-- Selecione --</option>
                        @foreach($owners as $o)<option value="{{ $o->id }}" {{ old('owner_id', $rule->owner_id) == $o->id ? 'selected' : '' }}>{{ $o->nome }}</option>@endforeach
                    </select>
                </div>
                <div id="property_field" style="display:{{ $rule->tipo === 'property' ? 'block' : 'none' }}">
                    <label class="block text-sm font-medium text-gray-700">Imóvel</label>
                    <select name="property_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">-- Selecione --</option>
                        @foreach($properties as $p)<option value="{{ $p->id }}" {{ old('property_id', $rule->property_id) == $p->id ? 'selected' : '' }}>{{ $p->nome }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo de markup *</label>
                    <select name="markup_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="percent" {{ old('markup_type', $rule->markup_type) === 'percent' ? 'selected' : '' }}>Percentual</option>
                        <option value="fixed" {{ old('markup_type', $rule->markup_type) === 'fixed' ? 'selected' : '' }}>Valor fixo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Valor *</label>
                    <input type="number" name="markup_value" value="{{ old('markup_value', $rule->markup_value) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="flex items-center">
                    <input type="hidden" name="ativo" value="0">
                    <input type="checkbox" name="ativo" value="1" {{ old('ativo', $rule->ativo) ? 'checked' : '' }} class="rounded border-gray-300"> Ativo
                </div>
            </div>
            <div class="mt-6 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Salvar</button>
                <a href="{{ route('admin.markup-rules.index') }}" class="px-4 py-2 border border-gray-300 rounded-md">Cancelar</a>
            </div>
        </form>

        <script>
            document.getElementById('tipo').addEventListener('change', function() {
                document.getElementById('owner_field').style.display = this.value === 'owner' ? 'block' : 'none';
                document.getElementById('property_field').style.display = this.value === 'property' ? 'block' : 'none';
            });
        </script>
    </div>
</x-admin-layout>
