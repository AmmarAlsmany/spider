<?php

namespace App\Http\Controllers;

use App\Models\ContractUpdateRequest;
use App\Models\contracts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractRequestController extends Controller
{
    public function handleRequest($id, $action, Request $request)
    {
        try {
            $updateRequest = ContractUpdateRequest::findOrFail($id);
            
            // Check if the authenticated user is the sales representative for this contract
            if (Auth::user()->id != $updateRequest->contract->sales_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Update the request status and response
            $updateRequest->update([
                'status' => $action === 'approve' ? 'approved' : 'rejected',
                'response' => $request->response
            ]);

            return response()->json(['message' => 'Request ' . ucfirst($action) . 'd successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process request'], 500);
        }
    }

    public function clientHandleContract($id, $action, Request $request)
    {
        $contract = contracts::findOrFail($id);
        
        // Check if the authenticated user is the client for this contract
        if (Auth::user()->id != $contract->customer_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Update the contract status based on client's action
        $contract->update([
            'contract_status' => $action === 'approve' ? 'active' : 'rejected',
        ]);

        return response()->json(['message' => 'Contract ' . ucfirst($action) . 'd successfully']);
    }
}
