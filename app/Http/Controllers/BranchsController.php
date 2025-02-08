<?php

namespace App\Http\Controllers;

use App\Models\branchs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $branch = branchs::findOrFail($id);
            return response()->json([
                'success' => true,
                'branch' => $branch
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found'
            ], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(branchs $branchs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'branch_name' => 'required',
            'branch_manager_name' => 'required',
            'branch_manager_phone' => 'required',
            'branch_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $branch = branchs::findOrFail($id);
            
            $branch->update([
                'branch_name' => $request->branch_name,
                'branch_manager_name' => $request->branch_manager_name,
                'branch_manager_phone' => $request->branch_manager_phone,
                'branch_address' => $request->branch_address,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Branch information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update branch information'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(branchs $branchs)
    {
        //
    }
}
