<x-admin-layout>
    <x-slot name="header">Reserva #{{ $reservation->id }}</x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('admin.reservations.index') }}" class="text-indigo-600 hover:underline mb-4 inline-block">← Voltar</a>

        <div class="bg-white shadow rounded-lg p-6">
            <p><strong>Status:</strong> <span class="px-2 py-1 rounded {{ $reservation->status === 'confirmed' ? 'bg-green-100' : ($reservation->status === 'failed' ? 'bg-red-100' : 'bg-gray-100') }}">{{ $reservation->status }}</span></p>
            <p><strong>Hóspede:</strong> {{ $reservation->guest_name }} · {{ $reservation->guest_email }} · {{ $reservation->guest_phone }}</p>
            <p><strong>Imóvel:</strong> {{ $reservation->property->nome }}</p>
            <p><strong>Check-in:</strong> {{ $reservation->checkin_date->format('d/m/Y') }} · <strong>Check-out:</strong> {{ $reservation->checkout_date->format('d/m/Y') }} · {{ $reservation->nights }} noites</p>
            <p><strong>Total base:</strong> R$ {{ number_format($reservation->base_total, 2, ',', '.') }}</p>
            <p><strong>Markup:</strong> R$ {{ number_format($reservation->markup_total, 2, ',', '.') }}</p>
            <p><strong>Total final:</strong> R$ {{ number_format($reservation->final_total, 2, ',', '.') }}</p>
            @if($reservation->stays_reservation_id)<p><strong>ID Stays:</strong> {{ $reservation->stays_reservation_id }}</p>@endif
            @if($reservation->error_message)<p class="text-red-600">{{ $reservation->error_message }}</p>@endif
        </div>
    </div>
</x-admin-layout>
