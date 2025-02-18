<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\client;
use App\Models\ContractAnnex;
use App\Models\User;
use App\Models\contracts;
use App\Models\payments;
use App\Models\PostponementRequest;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\NotificationDispatcher;


class SalesManagerController extends Controller
{
    use NotificationDispatcher;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get total and active sales agents
        $totalAgents = User::where('role', 'sales')->count();
        $activeAgents = User::where('role', 'sales')
            ->where('status', 'active')
            ->count();

        // Get total and active clients
        $totalClients = client::count();
        $activeClients = client::where('status', 'active')->count();

        // Get total and active contracts
        $totalContracts = contracts::count();
        $activeContracts = contracts::where('contract_status', 'approved')->count();

        // Get recent contracts
        $recentContracts = contracts::with(['customer', 'salesRepresentative'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $sales_agents = User::where('role', 'sales')->get();

        return view('managers.sales manager.dashboard', compact(
            'totalAgents',
            'activeAgents',
            'totalClients',
            'activeClients',
            'totalContracts',
            'activeContracts',
            'recentContracts',
            'sales_agents'
        ));
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
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }

    /**
     * Manage sales agents.
     */
    public function manageAgents()
    {
        $query = User::where('role', 'sales');

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $sales_agents = $query->get();
        return view('managers.sales manager.manage_agents', compact('sales_agents'));
    }

    /**
     * Store a newly created sales agent in storage.
     */
    public function storeAgent(Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:7',
                'phone' => 'required|numeric|unique:users',
                'status' => 'required|in:active,inactive',
            ]);

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->role = 'sales';
            $user->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating sales agent: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Sales agent added successfully');
    }

    /**
     * Update the specified sales agent in storage.
     */
    public function updateAgent(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->phone = $request->phone;
        $user->status = $request->status;
        $user->save();

        return redirect()->back()->with('success', 'Sales agent updated successfully');
    }

    public function removeAgent($id)
    {
        try {
            DB::beginTransaction();

            $agent = User::findOrFail($id);

            // Check if agent has any active contracts
            $hasActiveContracts = contracts::where('sales_id', $id)
                ->where('contract_status', 'approved')
                ->exists();

            if ($hasActiveContracts) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Cannot remove agent with active contracts. Please transfer their contracts first.');
            }

            // Check if agent has any active clients
            $hasActiveClients = client::where('sales_id', $id)
                ->where('status', 'active')
                ->exists();

            if ($hasActiveClients) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Cannot remove agent with active clients. Please reassign their clients first.');
            }

            // Permanently delete the agent
            $agent->forceDelete();

            DB::commit();
            return redirect()->back()->with('success', 'Agent has been permanently removed');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error removing agent: ' . $e->getMessage());
        }
    }

    public function contractsReport(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $contracts = contracts::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->with(['customer', 'salesRepresentative', 'type'])
            ->get();

        $total = $contracts->count();
        $approved = $contracts->where('contract_status', 'approved')->count();
        $pending = $contracts->where('contract_status', 'pending')->count();
        $rejected = $contracts->where('contract_status', 'rejected')->count();

        return view(
            'managers.sales manager.reports.contracts',
            compact('contracts', 'total', 'approved', 'pending', 'rejected')
        );
    }

    public function collectionsReport(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $collections = payments::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('paid_at', [$startDate, $endDate]);
        })
            ->whereNotNull('paid_at')
            ->with(['contract.customer', 'contract.salesRepresentative'])
            ->get();

        $total = $collections->sum('payment_amount');

        return view(
            'managers.sales manager.reports.collections',
            compact('collections', 'total')
        );
    }

    public function paymentsReport(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $remainingPayments = payments::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('due_date', [$startDate, $endDate]);
        })
            ->whereNull('paid_at')
            ->with(['contract.customer', 'contract.salesRepresentative'])
            ->get();

        $total = $remainingPayments->sum('payment_amount');

        return view(
            'managers.sales manager.reports.payments',
            compact('remainingPayments', 'total')
        );
    }

    public function invoicesReport(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $invoices = payments::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('due_date', [$startDate, $endDate]);
        })
            ->with(['contract.customer', 'contract.salesRepresentative'])
            ->get();

        $total = $invoices->sum('payment_amount');
        $collected = $invoices->whereNotNull('paid_at')->sum('payment_amount');
        $remaining = $total - $collected;

        return view(
            'managers.sales manager.reports.invoices',
            compact('invoices', 'total', 'collected', 'remaining')
        );
    }

    public function manageContracts()
    {
        $query = contracts::with(['customer', 'salesRepresentative']);

        // Apply filters
        if (request('contract_number')) {
            $query->where('contract_number', 'like', '%' . request('contract_number') . '%');
        }

        if (request('customer')) {
            $query->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . request('customer') . '%');
            });
        }

        if (request('sales_agent')) {
            $query->where('sales_id', request('sales_agent'));
        }

        if (request('status')) {
            $query->where('contract_status', request('status'));
        }

        if (request('date')) {
            $query->whereDate('created_at', request('date'));
        }

        $contracts = $query->orderBy('created_at', 'desc')->get();
        $salesAgents = User::where('role', 'sales')->get();

        return view('managers.sales manager.manage_contracts', compact('contracts', 'salesAgents'));
    }

    public function deleteContract($id)
    {
        try {
            $contract = contracts::findOrFail($id);

            // Check if contract has any payments paid
            if ($contract->payments->where('payment_status', 'paid')->count()) {
                return redirect()->back()->with('error', 'Cannot delete contract with existing payments');
            }
            $contract->contract_status = 'stopped';
            $contract->save();
            $contract->forceDelete();

            return redirect()->back()->with('success', 'Contract deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting contract: ' . $e->getMessage());
        }
    }

    public function transferContract(Request $request, $id)
    {
        try {
            $request->validate([
                'new_agent_id' => 'required|exists:users,id',
                'reason' => 'required|string'
            ]);

            DB::beginTransaction();

            $contract = contracts::findOrFail($id);
            $clientId = $contract->customer_id;
            $oldAgentId = $contract->sales_id;

            // Create transfer record
            DB::table('contract_transfers')->insert([
                'contract_id' => $id,
                'from_agent_id' => $oldAgentId,
                'to_agent_id' => $request->new_agent_id,
                'reason' => $request->reason,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update contract with new agent
            $contract->update([
                'sales_id' => $request->new_agent_id
            ]);

            // Update customer
            DB::table('clients')->where('id', $clientId)->update([
                'sales_id' => $request->new_agent_id
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Contract transferred successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error transferring contract: ' . $e->getMessage());
        }
    }

    public function agentPerformance(Request $request, $id = null)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $status = $request->input('status');

        // If no ID provided, get all sales agents
        if (!$id) {
            $agents = User::where('role', 'sales')->get();
            $performanceData = [];

            foreach ($agents as $agent) {
                $performanceData[] = $this->getAgentStats($agent->id, $month, $year, $status);
            }

            return view('managers.sales manager.agent_performance', compact('performanceData', 'month', 'year', 'status'));
        }

        // If ID provided, get specific agent's performance
        $agent = User::findOrFail($id);
        $performanceData = [$this->getAgentStats($id, $month, $year, $status)];

        return view('managers.sales manager.agent_performance', compact('performanceData', 'month', 'year', 'status'));
    }

    public function agentContracts($id)
    {
        $agent = User::findOrFail($id);
        $query = contracts::where('sales_id', $id);

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('contract_number', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $contracts = $query->orderBy('created_at', 'desc')->get();
        return view('managers.sales manager.agent_contracts', compact('agent', 'contracts'));
    }

    public function viewContract($id)
    {
        $contract = contracts::with(['customer'])->findOrFail($id);
        return view('managers.sales manager.view_contract', compact('contract'));
    }

    private function getAgentStats($agentId, $month, $year, $status = null)
    {
        $agent = User::findOrFail($agentId);

        // Get new contracts for selected month
        $newContracts = contracts::where('sales_id', $agentId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->when($status, function ($query) use ($status) {
                $query->where('contract_status', $status);
            })
            ->get();

        // Get collections for selected month
        $collections = payments::whereHas('contract', function ($query) use ($agentId) {
            $query->where('sales_id', $agentId);
        })
            ->whereYear('paid_at', $year)
            ->whereMonth('paid_at', $month)
            ->whereNotNull('paid_at')
            ->get();

        // Calculate totals
        $totalNewContracts = $newContracts->count();
        $totalContractValue = $newContracts->sum('contract_price');
        $totalCollections = $collections->sum('payment_amount');

        // Get all time stats
        $allTimeContracts = contracts::where('sales_id', $agentId)
            ->when($status, function ($query) use ($status) {
                $query->where('contract_status', $status);
            })
            ->count();
        $allTimeCollections = payments::whereHas('contract', function ($query) use ($agentId) {
            $query->where('sales_id', $agentId);
        })
            ->whereNotNull('paid_at')
            ->sum('payment_amount');

        return [
            'agent' => $agent,
            'current_month' => [
                'new_contracts' => $newContracts,
                'total_new_contracts' => $totalNewContracts,
                'total_contract_value' => $totalContractValue,
                'collections' => $collections,
                'total_collections' => $totalCollections
            ],
            'all_time' => [
                'total_contracts' => $allTimeContracts,
                'total_collections' => $allTimeCollections
            ]
        ];
    }

    public function agentPerformanceSingle($id)
    {
        $agent = User::findOrFail($id);

        // Get date range from request or use current month
        $startDate = request('start_date', date('Y-m-01'));
        $endDate = request('end_date', date('Y-m-d'));

        // Get all contracts for the agent within date range
        $contracts = contracts::where('sales_id', $id)
            ->whereBetween('contract_start_date', [$startDate, $endDate])
            ->with('customer')
            ->get();

        // Calculate statistics
        $stats = [
            'total_contracts' => $contracts->count(),
            'total_contract_value' => $contracts->sum('contract_price'),
            'avg_contract_value' => $contracts->count() > 0 ? $contracts->avg('contract_price') : 0,
            'total_collections' => $contracts->sum('collections_sum'),
            'paid_contracts' => $contracts->where('contract_status', 'completed')->count(),
            'pending_contracts' => $contracts->where('contract_status', 'active')->count(),
            'pending_collections' => $contracts->where('contract_status', 'active')->sum('contract_price')
        ];

        // Get monthly statistics for chart
        $monthlyStats = [
            'months' => [],
            'contract_values' => [],
            'collections' => []
        ];

        // Get last 6 months data
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd = date('Y-m-t', strtotime("-$i months"));

            $monthlyContracts = contracts::where('sales_id', $id)
                ->whereBetween('contract_start_date', [$monthStart, $monthEnd])
                ->get();

            $monthlyStats['months'][] = date('M Y', strtotime("-$i months"));
            $monthlyStats['contract_values'][] = $monthlyContracts->sum('contract_price');
            $monthlyStats['collections'][] = $monthlyContracts->sum('collections_sum');
        }

        // Get recent contracts
        $recentContracts = contracts::where('sales_id', $id)
            ->with('customer')
            ->orderBy('contract_start_date', 'desc')
            ->limit(10)
            ->get();

        return view('managers.sales manager.single_agent_performance', compact(
            'agent',
            'stats',
            'monthlyStats',
            'recentContracts'
        ));
    }

    public function postponementRequests()
    {
        $query = PostponementRequest::with(['payment.customer', 'payment.contract']);

        // Apply search filters
        if (request('client')) {
            $query->whereHas('payment.customer', function ($q) {
                $q->where('name', 'like', '%' . request('client') . '%');
            });
        }

        if (request('contract')) {
            $query->whereHas('payment.contract', function ($q) {
                $q->where('contract_number', 'like', '%' . request('contract') . '%');
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('start_date') && request('end_date')) {
            $query->whereBetween('requested_date', [request('start_date'), request('end_date')]);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();
        return view('managers.sales manager.postponement_requests', compact('requests'));
    }

    public function approvePostponement(Request $request)
    {
        $postponementRequest = PostponementRequest::findOrFail($request->request_id);
        $payment = $postponementRequest->payment;

        DB::beginTransaction();
        try {
            // Update payment date
            $payment->update([
                'due_date' => $postponementRequest->requested_date
            ]);

            // Update request status
            $postponementRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::user()->id,
                'approved_at' => now()
            ]);

            DB::commit();

            // notify the client, sales and finance
            $notificationData = [
                'title' => 'Payment Postponement Request Approved',
                'message' => 'Payment postponement request has been approved.',
                'type' => 'info',
                'url' => "#",
                'priority' => 'normal',
            ];
            $this->notifyRoles(['client', 'sales', 'finance'], $notificationData, $postponementRequest->payment->customer_id, $postponementRequest->payment->sales_id);

            return redirect()->back()->with('success', 'Payment postponement request has been approved.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to approve postponement request. Please try again.');
        }
    }

    public function rejectPostponement(Request $request)
    {
        $postponementRequest = PostponementRequest::findOrFail($request->request_id);

        try {
            $postponementRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'rejected_by' => Auth::user()->id,
                'rejected_at' => now()
            ]);

            // notify the client, sales and finance
            $notificationData = [
                'title' => 'Payment Postponement Request Rejected',
                'message' => 'Payment postponement request has been rejected.',
                'type' => 'info',
                'url' => "#",
                'priority' => 'normal',
            ];
            $this->notifyRoles(['client', 'sales', 'finance'], $notificationData, $postponementRequest->payment->customer_id, $postponementRequest->payment->sales_id);

            return redirect()->back()->with('success', 'Payment postponement request has been rejected.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject postponement request. Please try again.');
        }
    }

    public function manageClients(Request $request)
    {
        $query = client::with('sales')
            ->withCount('contracts')
            ->withSum('contracts', 'contract_price');

        // Apply search filters
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->sales_agent) {
            $query->where('sales_agent_id', $request->sales_agent);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $salesAgents = User::where('role', 'sales')->get();
        $clients = $query->paginate(10);

        return view('managers.sales manager.manage_clients', compact('clients', 'salesAgents'));
    }

    public function clientDetails($id)
    {
        $client = client::with(['sales', 'contracts'])
            ->withCount('contracts')
            ->withSum('contracts', 'contract_price')
            ->findOrFail($id);

        return view('managers.sales manager.client_details', compact('client'));
    }

    public function editClient($id)
    {
        $client = client::findOrFail($id);
        return view('managers.sales manager.edit_client', compact('client'));
    }

    public function updateClient(Request $request, $id)
    {
        $client = client::findOrFail($id);
        
        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email,' . $id,
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
            'password' => $request->has('change_password') ? 'required|min:6|confirmed' : 'nullable',
        ]);

        // Update client information
        $client->name = $request->name;
        $client->email = $request->email;
        $client->phone = $request->phone;
        $client->status = $request->status;

        // Update password if requested
        if ($request->has('change_password') && $request->filled('password')) {
            $client->password = bcrypt($request->password);
        }

        $client->save();

        return redirect()
            ->route('sales_manager.client.details', $client->id)
            ->with('success', 'Client information updated successfully');
    }

    public function dashboard()
    {
        return redirect()->route('sales-manager.index');
    }

    /**
     * Show ticket details
     */
    public function showTicket($id)
    {
        $ticket = Tiket::with(['customer', 'replies'])->findOrFail($id);
        return view('sales.ticket.show', compact('ticket'));
    }

    /**
     * Show contacts report
     */
    public function contactsReport(Request $request)
    {
        $query = client::with('sales')
            ->when($request->get('start_date') && $request->get('end_date'), function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->get('start_date'), $request->get('end_date')]);
            });

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by sales agent if provided
        if ($request->has('sales_agent')) {
            $query->where('sales_id', $request->sales_agent);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('managers.sales manager.reports.contacts', compact('customers'));
    }

    public function pendingAnnexes()
    {
        $annexes = ContractAnnex::with(['contract', 'creator'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('managers.sales manager.pending_annexes', compact('annexes'));
    }
}
