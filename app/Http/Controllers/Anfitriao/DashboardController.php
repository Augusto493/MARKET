<?php

namespace App\Http\Controllers\Anfitriao;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    protected function getOwner(): ?Owner
    {
        return Owner::where('user_id', auth()->id())->first();
    }

    protected function redirectSeNaoAnfitriao()
    {
        if (!$this->getOwner()) {
            return redirect()->route('marketplace.index')
                ->with('info', 'Você não está vinculado a um perfil de anfitrião. Entre em contato com o administrador.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->redirectSeNaoAnfitriao()) {
            return $redirect;
        }
        $owner = $this->getOwner();
        $owner->loadCount(['properties', 'reservations']);
        $owner->load(['syncLogs' => fn ($q) => $q->latest()->limit(5)]);

        return view('anfitriao.dashboard', compact('owner'));
    }

    public function conectarStays()
    {
        if ($redirect = $this->redirectSeNaoAnfitriao()) {
            return $redirect;
        }
        $owner = $this->getOwner();
        return view('anfitriao.conectar-stays', compact('owner'));
    }

    public function salvarStays(Request $request)
    {
        if ($redirect = $this->redirectSeNaoAnfitriao()) {
            return $redirect;
        }
        $owner = $this->getOwner();
        $validated = $request->validate([
            'stays_base_url' => 'nullable|url',
            'stays_client_id' => 'nullable|string',
            'stays_client_secret' => 'nullable|string',
            'stays_account_identifier' => 'nullable|string',
        ]);
        if (empty($validated['stays_client_secret'])) {
            unset($validated['stays_client_secret']);
        }
        $owner->update($validated);
        return redirect()->route('anfitriao.conectar-stays')->with('success', 'Credenciais salvas. Use "Testar conexão" e depois "Importar imóveis".');
    }

    public function sincronizar(Request $request)
    {
        if ($redirect = $this->redirectSeNaoAnfitriao()) {
            return $redirect;
        }
        $owner = $this->getOwner();
        Artisan::call('stays:sync', ['ownerId' => $owner->id]);
        $output = Artisan::output();
        return back()->with('success', 'Sincronização executada. ' . trim($output));
    }

    public function imoveis()
    {
        if ($redirect = $this->redirectSeNaoAnfitriao()) {
            return $redirect;
        }
        $owner = $this->getOwner();
        $properties = $owner->properties()->with('photos')->latest()->paginate(12);
        return view('anfitriao.imoveis', compact('owner', 'properties'));
    }

    public function reservas()
    {
        if ($redirect = $this->redirectSeNaoAnfitriao()) {
            return $redirect;
        }
        $owner = $this->getOwner();
        $reservations = $owner->reservations()->with('property')->latest()->paginate(15);
        return view('anfitriao.reservas', compact('owner', 'reservations'));
    }
}
