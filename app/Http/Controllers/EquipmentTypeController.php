<?php

namespace App\Http\Controllers;

use App\Models\EquipmentType;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{
    public function index()
    {
        $equipment_types = EquipmentType::withTrashed()->get();
        return view('managers.equipment.index', compact('equipment_types'));
    }

    public function create()
    {
        return view('managers.equipment.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        EquipmentType::create([ 
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('equipment-types.index')
            ->with('success', 'Equipment type created successfully.');
    }

    public function edit(EquipmentType $equipmentType)
    {
        return view('managers.equipment.edit', compact('equipmentType'));
    }

    public function update(Request $request, EquipmentType $equipmentType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $equipmentType->update([
            'name' => $request->name,
            'description' => $request->description,
            'default_price' => $request->default_price,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('equipment-types.index')
            ->with('success', 'Equipment type updated successfully.');
    }

    public function destroy(EquipmentType $equipmentType)
    {
        $equipmentType->delete();
        return redirect()->route('equipment-types.index')
            ->with('success', 'Equipment type deleted successfully.');
    }

    public function restore($id)
    {
        EquipmentType::withTrashed()->find($id)->restore();
        return redirect()->route('equipment-types.index')
            ->with('success', 'Equipment type restored successfully.');
    }
}
