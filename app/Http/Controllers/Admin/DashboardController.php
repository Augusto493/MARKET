<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\SyncLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $reservationsByStatus = Reservation::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $receitaBruta = Reservation::whereIn('status', ['confirmed', 'pending'])->sum('final_total');
        $markupGanho = Reservation::whereIn('status', ['confirmed', 'pending'])->sum('markup_total');
        $leads = $reservationsByStatus->get('lead', 0) + $reservationsByStatus->get('pending', 0);
        $confirmadas = $reservationsByStatus->get('confirmed', 0);
        $taxaConversao = $leads > 0 ? round(($confirmadas / $leads) * 100, 1) : 0;

        $errosSync = SyncLog::where('status', 'error')
            ->where('started_at', '>=', now()->subDays(7))
            ->count();

        $ownersWithCounts = Owner::withCount('properties')
            ->orderBy('nome')
            ->get();

        return view('admin.dashboard', [
            'ownersCount' => Owner::count(),
            'propertiesCount' => Property::published()->count(),
            'reservationsCount' => Reservation::count(),
            'reservationsByStatus' => $reservationsByStatus,
            'receitaBruta' => $receitaBruta,
            'markupGanho' => $markupGanho,
            'taxaConversao' => $taxaConversao,
            'errosSync' => $errosSync,
            'ownersWithCounts' => $ownersWithCounts,
        ]);
    }
}
