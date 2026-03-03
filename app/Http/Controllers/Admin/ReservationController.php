<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['owner', 'property']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }
        $reservations = $query->latest()->paginate(15);
        return view('admin.reservations.index', compact('reservations'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['owner', 'property', 'integrationLogs']);
        return view('admin.reservations.show', compact('reservation'));
    }
}
