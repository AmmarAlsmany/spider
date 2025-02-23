<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    public function checkDuplicate(Request $request)
    {
        $field = $request->query('field');
        $value = $request->query('value');

        if (!in_array($field, ['email', 'phone', 'mobile'])) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        // Check if a client_id is provided to exclude current client in edit mode
        $clientId = $request->query('client_id');
        
        $query = Client::where($field, $value);
        
        // If client_id is provided, exclude that client from the check
        if ($clientId) {
            $query->where('id', '!=', $clientId);
        }

        $exists = $query->exists();

        return response()->json(['exists' => $exists]);
    }
}
