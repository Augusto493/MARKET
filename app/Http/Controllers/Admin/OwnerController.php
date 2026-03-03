<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Services\Stays\StaysAdapterFactory;
use App\Services\Stays\StaysService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::with('user')->withCount('properties')->latest()->paginate(15);
        return view('admin.owners.index', compact('owners'));
    }

    public function create()
    {
        $users = \App\Models\User::orderBy('name')->get();
        return view('admin.owners.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telefone' => 'nullable|string|max:50',
            'status' => 'in:active,inactive',
            'stays_base_url' => 'nullable|url',
            'stays_client_id' => 'nullable|string',
            'stays_client_secret' => 'nullable|string',
            'stays_account_identifier' => 'nullable|string',
        ]);

        Owner::create($validated);
        return redirect()->route('admin.owners.index')->with('success', 'Owner cadastrado.');
    }

    public function show(Owner $owner)
    {
        $owner->load(['properties', 'syncLogs' => fn ($q) => $q->latest()->limit(10)]);
        return view('admin.owners.show', compact('owner'));
    }

    public function edit(Owner $owner)
    {
        $users = \App\Models\User::orderBy('name')->get();
        return view('admin.owners.edit', compact('owner', 'users'));
    }

    public function update(Request $request, Owner $owner)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telefone' => 'nullable|string|max:50',
            'status' => 'in:active,inactive',
            'stays_base_url' => 'nullable|url',
            'stays_client_id' => 'nullable|string',
            'stays_client_secret' => 'nullable|string',
            'stays_account_identifier' => 'nullable|string',
        ]);

        if (empty($validated['stays_client_secret'])) {
            unset($validated['stays_client_secret']);
        }

        $owner->update($validated);
        return redirect()->route('admin.owners.index')->with('success', 'Owner atualizado.');
    }

    public function destroy(Owner $owner)
    {
        $owner->delete();
        return redirect()->route('admin.owners.index')->with('success', 'Owner removido.');
    }

    public function testConnection(Owner $owner)
    {
        $adapter = StaysAdapterFactory::forOwner($owner);
        $result  = $adapter->testConnection();

        if ($result['success']) {
            $owner->update(['sync_status' => 'ok', 'last_sync_error' => null]);
            return back()->with('success', 'Conexão OK: ' . ($result['message'] ?? ''));
        }

        $owner->update(['sync_status' => 'erro', 'last_sync_error' => $result['message'] ?? 'Erro']);
        return back()->with('error', $result['message'] ?? 'Falha na conexão.');
    }

    public function sync(Owner $owner)
    {
        Artisan::call('stays:sync', ['ownerId' => $owner->id]);
        $output = Artisan::output();
        return back()->with('success', 'Sincronização executada. ' . trim($output));
    }
}
