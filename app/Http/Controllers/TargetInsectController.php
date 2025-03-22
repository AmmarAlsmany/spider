<?php

namespace App\Http\Controllers;

use App\Models\TargetInsect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TargetInsectController extends Controller
{
    /**
     * Display a listing of the target insects.
     */
    public function index()
    {
        $targetInsects = TargetInsect::all();
        return view('managers.technical.target-insects.index', compact('targetInsects'));
    }

    /**
     * Show the form for creating a new target insect.
     */
    public function create()
    {
        return view('managers.technical.target-insects.create');
    }

    /**
     * Store a newly created target insect in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:target_insects,name',
            'value' => 'required|string|max:255|unique:target_insects,value',
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $targetInsect = new TargetInsect();
        $targetInsect->fill([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'value' => $request->value,
            'description' => $request->description,
            'active' => $request->has('active')
        ]);
        $targetInsect->save();

        return redirect()->route('technical.target-insects.index')->with('success', 'Target insect added successfully');
    }

    /**
     * Show the form for editing the specified target insect.
     */
    public function edit($id)
    {
        $targetInsect = TargetInsect::findOrFail($id);
        return view('managers.technical.target-insects.edit', compact('targetInsect'));
    }

    /**
     * Update the specified target insect in storage.
     */
    public function update(Request $request, $id)
    {
        $targetInsect = TargetInsect::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:target_insects,name,' . $targetInsect->id,
            'value' => 'required|string|max:255|unique:target_insects,value,' . $targetInsect->id,
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $targetInsect->fill([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'value' => $request->value,
            'description' => $request->description,
            'active' => $request->has('active')
        ]);
        $targetInsect->save();

        return redirect()->route('technical.target-insects.index')->with('success', 'Target insect updated successfully');
    }

    /**
     * Remove the specified target insect from storage.
     */
    public function destroy($id)
    {
        $targetInsect = TargetInsect::findOrFail($id);
        
        try {
            $targetInsect->delete();
            return redirect()->route('technical.target-insects.index')->with('success', 'Target insect deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('technical.target-insects.index')->with('error', 'Cannot delete this target insect as it is in use');
        }
    }
}
