<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarkupRule;
use App\Models\Owner;
use App\Models\Property;
use Illuminate\Http\Request;

class MarkupRuleController extends Controller
{
    public function index()
    {
        $rules = MarkupRule::with(['owner', 'property'])->latest()->paginate(15);
        return view('admin.markup-rules.index', compact('rules'));
    }

    public function create()
    {
        $owners = Owner::orderBy('nome')->get();
        $properties = Property::orderBy('nome')->get();
        return view('admin.markup-rules.create', compact('owners', 'properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:global,owner,property',
            'owner_id' => 'nullable|exists:owners,id',
            'property_id' => 'nullable|exists:properties,id',
            'markup_type' => 'required|in:percent,fixed',
            'markup_value' => 'required|numeric|min:0',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'min_noites' => 'nullable|integer|min:1',
            'max_noites' => 'nullable|integer|min:1',
            'ativo' => 'boolean',
            'prioridade' => 'integer',
        ]);

        $validated['ativo'] = $request->boolean('ativo', true);
        MarkupRule::create($validated);
        return redirect()->route('admin.markup-rules.index')->with('success', 'Regra criada.');
    }

    public function edit(MarkupRule $markupRule)
    {
        $owners = Owner::orderBy('nome')->get();
        $properties = Property::orderBy('nome')->get();
        return view('admin.markup-rules.edit', ['rule' => $markupRule, 'owners' => $owners, 'properties' => $properties]);
    }

    public function update(Request $request, MarkupRule $markupRule)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:global,owner,property',
            'owner_id' => 'nullable|exists:owners,id',
            'property_id' => 'nullable|exists:properties,id',
            'markup_type' => 'required|in:percent,fixed',
            'markup_value' => 'required|numeric|min:0',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
            'min_noites' => 'nullable|integer|min:1',
            'max_noites' => 'nullable|integer|min:1',
            'ativo' => 'boolean',
            'prioridade' => 'integer',
        ]);

        $markupRule->update($validated);
        return redirect()->route('admin.markup-rules.index')->with('success', 'Regra atualizada.');
    }

    public function destroy(MarkupRule $markupRule)
    {
        $markupRule->delete();
        return redirect()->route('admin.markup-rules.index')->with('success', 'Regra removida.');
    }
}
