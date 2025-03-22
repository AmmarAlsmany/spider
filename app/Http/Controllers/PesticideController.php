<?php

namespace App\Http\Controllers;

use App\Models\Pesticide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
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
            'category' => 'nullable|string',
            'current_stock' => 'required|integer|min:0',
            'min_stock_threshold' => 'required|integer|min:0',
            'active' => 'boolean'
        ]);

        $pesticide = new Pesticide();
        $pesticide->fill([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'category' => $request->category,
            'current_stock' => $request->current_stock,
            'min_stock_threshold' => $request->min_stock_threshold,
            'active' => $request->has('active')
        ]);
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
            'category' => 'nullable|string',
            'current_stock' => 'required|integer|min:0',
            'min_stock_threshold' => 'required|integer|min:0',
            'active' => 'boolean'
        ]);

        $pesticide->fill([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'category' => $request->category,
            'current_stock' => $request->current_stock,
            'min_stock_threshold' => $request->min_stock_threshold,
            'active' => $request->has('active')
        ]);
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

    /**
     * Export pesticides to CSV
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        $pesticides = Pesticide::all();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pesticides-' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($pesticides) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Name', 'Category', 'Description', 'Current Stock', 'Min Stock Threshold', 'Status']);
            
            // Add rows
            foreach ($pesticides as $pesticide) {
                fputcsv($file, [
                    $pesticide->name,
                    $pesticide->category ?? 'N/A',
                    $pesticide->description ?? 'N/A',
                    $pesticide->current_stock,
                    $pesticide->min_stock_threshold,
                    $pesticide->active ? 'Active' : 'Inactive'
                ]);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
}
