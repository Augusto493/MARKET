<x-admin-layout>
    <x-slot name="header">Editar owner</x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('admin.owners.index') }}" class="text-indigo-600 hover:underline mb-4 inline-block">← Voltar</a>

        <form action="{{ route('admin.owners.update', $owner) }}" method="post" class="bg-white shadow rounded-lg p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Usuário (anfitrião)</label>
                    <select name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">— Nenhum —</option>
                        @foreach($users as $u)
                            @if(!$u->owner || $u->owner->id === $owner->id)<option value="{{ $u->id }}" {{ old('user_id', $owner->user_id) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>@endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome *</label>
                    <input type="text" name="nome" value="{{ old('nome', $owner->nome) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('nome')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $owner->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Telefone</label>
                    <input type="text" name="telefone" value="{{ old('telefone', $owner->telefone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="active" {{ old('status', $owner->status) === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ old('status', $owner->status) === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stays Base URL</label>
                    <input type="url" name="stays_base_url" value="{{ old('stays_base_url', $owner->stays_base_url) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Client ID</label>
                    <input type="text" name="stays_client_id" value="{{ old('stays_client_id', $owner->stays_client_id) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Client Secret (deixe em branco para não alterar)</label>
                    <input type="password" name="stays_client_secret" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </div>
            <div class="mt-6 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Salvar</button>
                <a href="{{ route('admin.owners.index') }}" class="px-4 py-2 border border-gray-300 rounded-md">Cancelar</a>
            </div>
        </form>
    </div>
</x-admin-layout>
