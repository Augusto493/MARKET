<x-anfitriao-layout>
    <x-slot name="header">Conectar Stays.net</x-slot>

    <div class="max-w-2xl">
        <p class="text-slate-600 mb-6">Informe as credenciais da sua conta Stays.net para importar seus imóveis. Deixe em branco para usar o modo demonstração (mock).</p>

        <form action="{{ route('anfitriao.conectar-stays.salvar') }}" method="post" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">URL base da API Stays</label>
                <input type="url" name="stays_base_url" value="{{ old('stays_base_url', $owner->stays_base_url) }}" placeholder="https://api.stays.net" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('stays_base_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Client ID</label>
                <input type="text" name="stays_client_id" value="{{ old('stays_client_id', $owner->stays_client_id) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Client Secret (deixe em branco para não alterar)</label>
                <input type="password" name="stays_client_secret" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Identificador da conta</label>
                <input type="text" name="stays_account_identifier" value="{{ old('stays_account_identifier', $owner->stays_account_identifier) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">Salvar</button>
                <a href="{{ route('anfitriao.dashboard') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Voltar</a>
            </div>
        </form>

        <div class="mt-6 flex gap-2">
            <form action="{{ route('anfitriao.sincronizar') }}" method="post" class="inline">@csrf<button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-800">Testar conexão e importar imóveis</button></form>
        </div>
    </div>
</x-anfitriao-layout>
