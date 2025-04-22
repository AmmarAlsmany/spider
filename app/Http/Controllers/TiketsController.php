<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Http\Controllers\Controller;
use App\Models\client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Traits\NotificationDispatcher;

class TiketsController extends Controller
{
    use NotificationDispatcher;

    public function index(Request $request)
    {
        // Get the authenticated sales person's ID
        $salesId = Auth::id();

        // Get all clients assigned to this sales person
        $clientIds = client::where('sales_id', $salesId)->pluck('id');

        // Get all tickets for these clients with search
        $query = Tiket::whereIn('customer_id', $clientIds)
            ->with(['customer', 'solver']);  // Eager load relationships

        // Apply search if search parameter exists
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('tiket_number', 'like', "%{$search}%")
                    ->orWhere('tiket_title', 'like', "%{$search}%")
                    ->orWhere('tiket_description', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->latest()->get();

        return view('tickets.index', compact('tickets'));
    }

    public function client_create()
    {
        return view('clients.tickets.create');
    }

    public function client_store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:clients,id',
            'tiket_title' => 'required|string|max:255',
            'tiket_description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $ticket = new Tiket();
        $ticket->customer_id = $request->customer_id;
        $ticket->tiket_number = 'TKT-' . Str::random(8);
        $ticket->tiket_title = $request->tiket_title;
        $ticket->tiket_description = $request->tiket_description;
        $ticket->priority = $request->priority;
        $ticket->status = 'open';
        $ticket->created_by = Auth::guard('client')->user()->id;
        $ticket->save();

        // Notify technical,sales and sales managers about the new complaint
        $data = [
            'title' => "New Ticket Created: " . $ticket->tiket_number . "from " . Auth::guard('client')->user()->name,
            'message' => $ticket->tiket_title,
            'priority' => 'high',
        ];
        
        // Different URLs for different roles
        $roleUrls = [
            'technical' => route('technical.client_tickets'),
            'sales' => route('sales.show.ticket'),
        ];

        $this->notifyRoles(['technical', 'sales'], $data, $ticket->customer_id, $ticket->created_by, $roleUrls);

        return redirect()->route('client.tikets')->with('success', 'Ticket created successfully');
    }

    public function show($id)
    {
        $ticket = Tiket::with(['customer', 'solver', 'replies'])->findOrFail($id);
        return view('tickets.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string'
        ]);

        $ticket = Tiket::findOrFail($id);
        $ticket->replies()->create([
            'reply' => $request->reply,
            'user_id' => Auth::guard('client')->user()->id
        ]);

        // Notify technical,sales and sales managers about the new reply
        $data = [
            'title' => "New Reply: " . $ticket->tiket_number . "from " . Auth::guard('client')->user()->name,
            'message' => $request->reply,
            'priority' => 'high',
        ];
        
        // Different URLs for different roles
        $roleUrls = [
            'technical' => route('technical.client_tickets'),
            'sales' => route('sales.show.ticket'),
        ];

        $this->notifyRoles(['technical', 'sales'], $data, $ticket->customer_id, $ticket->created_by, $roleUrls);

        return redirect()->back()->with('success', 'Reply added successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        $ticket = Tiket::findOrFail($id);
        $ticket->status = $request->status;
        if ($request->status === 'resolved' || $request->status === 'closed') {
            $ticket->solved_at = now();
            $ticket->solver_id = Auth::id();
        }
        $ticket->save();

        // Notify technical,sales and sales managers about the status update
        $data = [
            'title' => "Ticket Status Updated: " . $ticket->tiket_number . "from " . Auth::guard('client')->user()->name,
            'message' => 'Status changed to ' . $request->status,
            'priority' => 'high',
        ];
        
        // Different URLs for different roles
        $roleUrls = [
            'technical' => route('technical.client_tickets'),
            'sales' => route('sales.show.ticket'),
            'sales_manager' => route('sales.show.ticket')
        ];

        $this->notifyRoles(['technical', 'sales', 'sales_manager'], $data, $ticket->customer_id, $ticket->created_by, $roleUrls);

        return redirect()->back()->with('success', 'Ticket status updated successfully');
    }

    public function my_tikets()
    {
        // Get the authenticated client's ID
        $clientId = Auth::guard('client')->user()->id;

        // Get all tickets for the authenticated client
        $tickets = Tiket::where('customer_id', $clientId)
            ->with(['solver']) // Eager load solver relationship
            ->latest()
            ->paginate(10);

        // Add status colors for the view
        $tickets->each(function ($ticket) {
            $ticket->status_color = match ($ticket->status) {
                'open' => 'info',
                'in_progress' => 'warning',
                'resolved' => 'success',
                'closed' => 'secondary',
                default => 'primary'
            };
        });

        return view('clients.tikets', compact('tickets'));
    }
}
