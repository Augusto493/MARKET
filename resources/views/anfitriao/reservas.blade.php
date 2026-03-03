<x-anfitriao-layout>
    <x-slot name="header">Minhas reservas</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        @if($reservations->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Hóspede</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Imóvel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check-in / Check-out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($reservations as $r)
                            <tr>
                                <td class="px-6 py-4"><span class="font-medium">{{ $r->guest_name }}</span><br><span class="text-sm text-slate-500">{{ $r->guest_email }}</span></td>
                                <td class="px-6 py-4 text-slate-600">{{ $r->property->nome ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm">{{ $r->checkin_date->format('d/m/Y') }} → {{ $r->checkout_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-medium">R$ {{ number_format($r->final_total, 2, ',', '.') }}</td>
                                <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full {{ $r->status === 'confirmed' ? 'bg-emerald-100' : ($r->status === 'failed' ? 'bg-red-100' : 'bg-slate-100') }}">{{ $r->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-slate-200">{{ $reservations->links() }}</div>
        @else
            <div class="px-6 py-12 text-center text-slate-500">Nenhuma reserva ainda.</div>
        @endif
    </div>
</x-anfitriao-layout>
