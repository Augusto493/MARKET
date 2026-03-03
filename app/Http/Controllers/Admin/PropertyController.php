<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function create()
    {
        return redirect()->route('admin.properties.index')->with('info', 'Imóveis são importados via sincronização Stays.');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.properties.index');
    }

    public function index(Request $request)
    {
        $query = Property::with('owner');
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }
        if ($request->filled('publicado')) {
            $query->where('publicado_marketplace', $request->publicado);
        }
        $properties = $query->latest()->paginate(15);
        return view('admin.properties.index', compact('properties'));
    }

    public function show(Property $property)
    {
        $property->load(['owner', 'photos', 'amenities']);
        return view('admin.properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        $property->load('owner');
        return view('admin.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'titulo_marketing' => 'nullable|string|max:255',
            'publicado_marketplace' => 'boolean',
            'ativo' => 'boolean',
            'prioridade' => 'integer',
            'destaque' => 'boolean',
        ]);

        $property->update($validated);
        return redirect()->route('admin.properties.index')->with('success', 'Imóvel atualizado.');
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return redirect()->route('admin.properties.index')->with('success', 'Imóvel removido.');
    }

    public function toggleStatus(Property $property)
    {
        $property->update(['publicado_marketplace' => !$property->publicado_marketplace]);
        return back()->with('success', $property->publicado_marketplace ? 'Imóvel publicado.' : 'Imóvel despublicado.');
    }
}
