<x-admin-layout>
    <x-slot name="header">Imóvel: {{ $property->nome }}</x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('admin.properties.index') }}" class="text-indigo-600 hover:underline mb-4 inline-block">← Voltar</a>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <p><strong>Owner:</strong> {{ $property->owner->nome }}</p>
            <p><strong>Capacidade:</strong> {{ $property->capacidade_hospedes }} hóspedes · {{ $property->quartos }} quartos · {{ $property->banheiros }} banheiros</p>
            <p><strong>Publicado:</strong> {{ $property->publicado_marketplace ? 'Sim' : 'Não' }}</p>
            <p><strong>Ativo:</strong> {{ $property->ativo ? 'Sim' : 'Não' }}</p>
            @if($property->mainPhoto)
                <img src="{{ $property->mainPhoto->url }}" alt="" class="mt-4 max-w-xs rounded-lg">
            @endif
            <div class="mt-4"><a href="{{ route('admin.properties.edit', $property) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Editar</a></div>
        </div>

        <h3 class="font-medium mb-2">Fotos ({{ $property->photos->count() }})</h3>
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($property->photos as $photo)
                <img src="{{ $photo->thumbnail_url ?? $photo->url }}" alt="" class="w-24 h-24 object-cover rounded">
            @endforeach
        </div>

        <h3 class="font-medium mb-2">Comodidades</h3>
        <p class="text-gray-600">{{ $property->amenities->pluck('nome')->join(', ') ?: 'Nenhuma' }}</p>
    </div>
</x-admin-layout>
