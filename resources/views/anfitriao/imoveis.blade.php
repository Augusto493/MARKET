<x-anfitriao-layout>
    <x-slot name="header">Meus imóveis</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        @if($properties->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Imóvel</th><th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Publicado</th><th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Ações</th></tr></thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($properties as $p)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($p->photos->first())<img src="{{ $p->photos->first()->thumbnail_url ?? $p->photos->first()->url }}" alt="" class="w-14 h-14 rounded-xl object-cover flex-shrink-0">@else<div class="w-14 h-14 rounded-xl bg-slate-100 flex-shrink-0"></div>@endif
                                        <span class="font-medium text-slate-800">{{ $p->nome }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-medium rounded-full {{ $p->publicado_marketplace ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $p->publicado_marketplace ? 'Publicado' : 'Não publicado' }}</span></td>
                                <td class="px-6 py-4"><a href="{{ route('marketplace.property.show', $p) }}" target="_blank" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">Ver no marketplace →</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-slate-200">{{ $properties->links() }}</div>
        @else
            <div class="px-6 py-12 text-center text-slate-500">
                <p class="text-slate-600">Você ainda não tem imóveis importados.</p>
                <p class="text-sm mt-2">Vá em "Conectar Stays" e depois clique em "Importar imóveis agora" no painel.</p>
                <a href="{{ route('anfitriao.conectar-stays') }}" class="inline-block mt-4 px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">Conectar Stays</a>
            </div>
        @endif
    </div>
</x-anfitriao-layout>
