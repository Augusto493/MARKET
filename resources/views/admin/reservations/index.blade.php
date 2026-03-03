<x-admin-layout>
    <x-slot name="header">Reservas</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hóspede</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Imóvel</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Check-in / Check-out</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($reservations as $r)
                        <tr>
                            <td class="px-4 py-2">{{ $r->id }}</td>
                            <td class="px-4 py-2">{{ $r->guest_name }}<br><small class="text-gray-500">{{ $r->guest_email }}</small></td>
                            <td class="px-4 py-2">{{ $r->property->nome ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $r->checkin_date->format('d/m/Y') }} → {{ $r->checkout_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">R$ {{ number_format($r->final_total, 2, ',', '.') }}</td>
                            <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded {{ $r->status === 'confirmed' ? 'bg-green-100' : ($r->status === 'failed' ? 'bg-red-100' : 'bg-gray-100') }}">{{ $r->status }}</span></td>
                            <td class="px-4 py-2"><a href="{{ route('admin.reservations.show', $r) }}" class="text-indigo-600 hover:underline">Ver</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Nenhuma reserva.</td></tr>
                    @endforelse
                </tbody>
            </table>
        <div class="px-4 py-2">{{ $reservations->links() }}</div>
    </div>
</x-admin-layout>
