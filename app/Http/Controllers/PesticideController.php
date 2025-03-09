<?php

namespace App\Http\Controllers;

use App\Models\Pesticide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PesticideController extends Controller
{
    /**
     * Display a listing of the pesticides.
     */
    public function index()
    {
        $pesticides = Pesticide::all();
        return view('managers.technical.pesticides.index', compact('pesticides'));
    }

    /**
     * Show the form for creating a new pesticide.
     */
    public function create()
    {
        return view('managers.technical.pesticides.create');
    }

    /**
     * Store a newly created pesticide in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:pesticides,name',
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $pesticide = new Pesticide();
        $pesticide->name = $request->name;
        $pesticide->slug = Str::slug($request->name);
        $pesticide->description = $request->description;
        $pesticide->active = $request->has('active');
        $pesticide->save();

        return redirect()->route('technical.pesticides.index')->with('success', __('messages.pesticide_added_successfully'));
    }

    /**
     * Show the form for editing the specified pesticide.
     */
    public function edit($id)
    {
        $pesticide = Pesticide::findOrFail($id);
        return view('managers.technical.pesticides.edit', compact('pesticide'));
    }

    /**
     * Update the specified pesticide in storage.
     */
    public function update(Request $request, $id)
    {
        $pesticide = Pesticide::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:pesticides,name,' . $pesticide->id,
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $pesticide->name = $request->name;
        $pesticide->slug = Str::slug($request->name);
        $pesticide->description = $request->description;
        $pesticide->active = $request->has('active');
        $pesticide->save();

        return redirect()->route('technical.pesticides.index')->with('success', __('messages.pesticide_updated_successfully'));
    }

    /**
     * Remove the specified pesticide from storage.
     */
    public function destroy($id)
    {
        $pesticide = Pesticide::findOrFail($id);
        $pesticide->delete();

        return redirect()->route('technical.pesticides.index')->with('success', __('messages.pesticide_deleted_successfully'));
    }
}
