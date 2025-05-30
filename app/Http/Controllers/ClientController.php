<?php

namespace App\Http\Controllers;

use App\Models\client;
use App\Models\contracts;
use App\Models\ContractAnnex;
use App\Models\payments;
use App\Models\Team;
use App\Models\VisitSchedule;
use App\Models\tikets;
use App\Models\ContractUpdateRequest; 
use App\Models\VisitChangeRequest;
use App\Services\VisitScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Traits\NotificationDispatcher;

class ClientController extends Controller
{
    use NotificationDispatcher;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $client = Auth::guard('client')->user();
        // Get total contracts and active contracts
        $totalContracts = contracts::where('customer_id', $client->id)->count();
        $activeContracts = contracts::where('customer_id', $client->id)
            ->where('contract_status', 'approved')
            ->count();

        // Get payments statistics
        $totalPayments = payments::where('customer_id', $client->id)->count();
        $totalRevenue = payments::where('customer_id', $client->id)
            ->where('payment_status', 'paid')
            ->sum('payment_amount');

        // Get pending payments (with postponement requests)
        $pendingPayments = payments::where('customer_id', $client->id)
            ->whereHas('postponementRequests', function ($query) {
                $query->where('status', 'pending');
            })
            ->count();

        // Get overdue payments (excluding those with pending postponement requests)
        $overduePayments = payments::where('customer_id', $client->id)
            ->where('payment_status', 'overdue')
            ->where('due_date', '<', now())
            ->whereDoesntHave('postponementRequests', function ($query) {
                $query->where('status', 'pending');
            })
            ->count();

        // Get recent payments with postponement info
        $recentPayments = payments::where('customer_id', $client->id)
            ->with(['contract', 'postponementRequests' => function ($query) {
                $query->where('status', 'pending')->latest();
            }])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent contracts
        $recentContracts = contracts::where('customer_id', $client->id)
            ->with(['type', 'salesRepresentative'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get open tickets count
        $openTickets = tikets::where('customer_id', $client->id)
            ->where('status', 'open')
            ->count();

        // Get scheduled visits
        $scheduledVisits = VisitSchedule::whereHas('contract', function ($query) use ($client) {
            $query->where('customer_id', $client->id)
                ->where('contract_status', 'approved');
        })
            ->with(['contract', 'team'])
            ->orderBy('visit_date', 'desc')
            ->orderBy('visit_time', 'desc')
            ->get();

        // Fetch client's contracts
        $contracts = contracts::where('customer_id', $client->id)->get();

        // Get pending annexes
        $pendingAnnexes = ContractAnnex::where('status', 'pending')
            ->whereHas('contract', function ($query) use ($client) {
                $query->where('customer_id', $client->id);
            })
            ->count();

        return view('clients.dashboard', compact(
            'totalContracts',
            'activeContracts',
            'totalPayments',
            'totalRevenue',
            'pendingPayments',
            'overduePayments',
            'recentPayments',
            'recentContracts',
            'openTickets',
            'scheduledVisits',
            'contracts',
            'pendingAnnexes' // Add contracts to the view
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
    public function store(Request $request) {}

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $client = Auth::guard('client')->user();
        $my_contracts = contracts::where('customer_id', $client->id)
            ->with(['type', 'history'])
            ->get();

        return view('clients.contracts', compact('my_contracts'));
    }

    public function contractDetails($id)
    {
        $client = Auth::guard('client')->user();
        $contract = contracts::where('customer_id', $client->id)
            ->where('id', $id)
            ->with([
                'type',
                'history',
                'branchs',
                'payments',
                'salesRepresentative',
                'updateRequests'
            ])
            ->firstOrFail();

        return view('clients.contract-details', compact('contract'));
    }

    public function submitUpdateRequest(Request $request, $id)
    {
        $client = Auth::guard('client')->user();
        $contract = contracts::where('customer_id', $client->id)
            ->where('id', $id)
            ->firstOrFail();

        $updateRequest = new ContractUpdateRequest();
        $updateRequest->contract_id = $contract->id;
        $updateRequest->client_id = $client->id;
        $updateRequest->request_details = $request->request_details;
        $updateRequest->status = 'pending';
        $updateRequest->save();

        // Notify sales manager,sales representative about contract change request
        $data = [
            'title' => "Update Request for Contract " . $contract->contract_number,
            'message' =>$contract->customer->name ."Ask for update on contract". $request->request_details,
        ];
        
        // Different URLs for different roles
        $roleUrls = [
            'sales' => route('contract.show.details', $contract->id),
            'sales_manager' => route('sales_manager.contract.view', $contract->id)
        ];

        $this->notifyRoles(['sales', 'sales_manager'], $data, $client->id, $contract->sales_id, $roleUrls);

        return redirect()->back()->with('success', 'Update request submitted successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(client $client)
    {
        //
    }

    public function pendingAnnexes()
    {
        $annexes = ContractAnnex::with(['contract', 'creator'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('clients.pending_annexes', compact('annexes'));
    }

    public function approveContract($id)
    {
        try {
            DB::beginTransaction();
            
            $contract = contracts::findOrFail($id);
            $client = Auth::guard('client')->user();

            // Verify if the contract belongs to the authenticated client
            if ($contract->customer_id !== $client->id) {
                return redirect()->back()->with('error', 'Unauthorized action.');
            }

            // Check if there are active teams available
            $activeTeams = Team::where('status', 'active')->count();
            
            if ($activeTeams > 0) {
                // Teams are available, set to approved and schedule visits
                $contract->contract_status = 'approved';
                $contract->save();
                
                // Create visit schedule
                try {
                    $visitScheduleService = new VisitScheduleService();
                    $visitScheduleService->createVisitSchedule($contract);
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error('Failed to schedule visits for contract: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Failed to schedule visits for the contract: ' . $e->getMessage());
                }
            } else {
                // No active teams available, set status to under_processing
                $contract->contract_status = 'under_processing';
                $contract->save();
                
                // Log this situation for admin follow-up
                Log::info('Contract '.$contract->id.' set to under_processing due to no active teams being available');
            }

            // Notify sales manager,sales representative about contract approval
            $statusText = $contract->contract_status === 'approved' ? 'Approved' : 'Under Processing';
            $messageText = $contract->contract_status === 'approved' 
                ? 'Your contract has been approved and visits have been scheduled.' 
                : 'Your contract has been approved by the client but is currently under processing due to team availability. Visits will be scheduled once teams become available.';
            
            $data = [
                'title' => "Contract {$statusText}: " . $contract->contract_number,
                'message' => $messageText,
            ];
            
            // Different URLs for different roles
            $roleUrls = [
                'sales' => route('contract.show.details', $contract->id),
                'sales_manager' => route('sales_manager.contract.view', $contract->id)
            ];

            // Notify sales and managers
            $this->notifyRoles(['sales', 'sales_manager'], $data, null, null, $roleUrls);
            
            // Also notify technical team if contract is under processing so they can assign teams
            if ($contract->contract_status === 'under_processing') {
                $technicalData = [
                    'title' => "Action Required: Contract Needs Team Assignment - " . $contract->contract_number,
                    'message' => 'A contract has been approved by the client but is waiting for team assignment. Please assign an active team.',
                    'type' => 'warning',
                    'priority' => 'high'
                ];
                
                $this->notifyRoles(['technical'], $technicalData, null, null, [
                    'technical' => route('technical.contract.show', $contract->id)
                ]);
            }

            DB::commit();
            
            // Return the appropriate success message based on contract status
            $successMessage = $contract->contract_status === 'approved'
                ? 'Contract has been approved successfully and visits have been scheduled.'
                : 'Contract has been approved and marked as under processing. Visits will be scheduled once teams become available.';
                
            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Contract approval error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve contract: ' . $e->getMessage());
        }
    }

    public function rejectContract($id)
    {
        try {
            $contract = contracts::findOrFail($id);
            $client = Auth::guard('client')->user();
            // Verify if the contract belongs to the authenticated client
            if ($contract->customer_id !== $client->id) {
                return redirect()->back()->with('error', 'Unauthorized action.');
            }

            $contract->contract_status = 'Not approved';
            $contract->rejection_reason = request('reject_reason');
            $contract->save();

            // Notify sales manager,sales representative about contract rejection
            $data = [
                'title' => "Contract Rejection: " . $contract->contract_number,
                'message' => 'Your contract has been rejected',
            ];
            
            // Different URLs for different roles
            $roleUrls = [
                'sales' => route('contract.show.details', $contract->id),
                'sales_manager' => route('sales_manager.contract.view', $contract->id)
            ];

            $this->notifyRoles(['sales', 'sales_manager'], $data, null, null, $roleUrls);

            return redirect()->back()->with('success', 'Contract has been rejected.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject contract.');
        }
    }

    public function requestContractUpdate($id)
    {
        try {
            $contract = contracts::findOrFail($id);
            $client = Auth::guard('client')->user();

            // Verify if the contract belongs to the authenticated client
            if ($contract->customer_id !== $client->id) {
                return redirect()->back()->with('error', 'Unauthorized action.');
            }

            // Create update request record
            $updateRequest = new ContractUpdateRequest();
            $updateRequest->contract_id = $contract->id;
            $updateRequest->client_id = $client->id;
            $updateRequest->request_details = request('update_request');
            $updateRequest->status = 'pending';
            $updateRequest->save();

            // Notify sales manager and sales representative about contract change request
            $data = [
                'title' => "Update Request for Contract " . $contract->contract_number,
                'message' => $updateRequest->request_details,
            ];
            
            // Different URLs for different roles
            $roleUrls = [
                'sales' => route('contract.show.details', $contract->id),
                'sales_manager' => route('sales_manager.contract.view', $contract->id)
            ];

            $this->notifyRoles(['sales', 'sales_manager'], $data, null, null, $roleUrls);

            return redirect()->back()->with('success', 'Update request has been submitted successfully.');
            // use 
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit update request.');
        }
    }

    public function downloadContract($id)
    {
        try {
            $contract = contracts::findOrFail($id);
            $client = Auth::guard('client')->user();

            // Verify if the contract belongs to the authenticated client
            if ($contract->customer_id !== $client->id) {
                return redirect()->back()->with('error', 'Unauthorized action.');
            }

            // Generate contract content
            $content = "Contract Agreement\n";
            $content .= "Contract #" . $contract->contract_number . "\n\n";
            $content .= "Contract Details:\n";
            $content .= "Start Date: " . $contract->contract_start_date . "\n";
            $content .= "Property Type: " . $contract->Property_type . "\n";
            $content .= "Contract Type: " . ($contract->type ? $contract->type->name : 'N/A') . "\n";
            $content .= "Contract Value: " . number_format($contract->contract_price, 2) . " SAR\n\n";
            $content .= "Payment Information:\n";
            $content .= "Payment Type: " . $contract->Payment_type . "\n";
            $content .= "Number of Payments: " . $contract->number_Payments . "\n\n";
            $content .= "Description:\n";
            $content .= $contract->contract_description . "\n\n";
            $content .= "Generated on: " . now()->format('F d, Y H:i:s');

            // Generate filename
            $filename = 'contract_' . $contract->contract_number . '.txt';

            // Return the file for download
            return response($content)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to download contract.');
        }
    }

    public function showContractDetails(contracts $contract)
    {
        return view('clients.contract-details', compact('contract'));
    }

    public function showContractVisitDetails(contracts $contract)
    {
        return view('clients.contract_visit_details', compact('contract'));
    }

    /**
     * Payment Related Functions
     */
    public function showPaymentDetails($id)
    {
        $client = Auth::guard('client')->user();
        $contract = contracts::where('customer_id', $client->id)
            ->where('id', $id)
            ->with(['payments' => function ($query) {
                $query->orderBy('due_date', 'asc');
            }])
            ->firstOrFail();

        return view('clients.payment-details', compact('contract'));
    }

    public function getPaymentDetails($id)
    {
        $client = Auth::guard('client')->user();
        $payment = payments::whereHas('contract', function ($query) use ($client) {
            $query->where('customer_id', $client->id);
        })
            ->findOrFail($id);

        return view('clients.partials.payment-details', compact('payment'));
    }

    public function postponePayment(Request $request, $contractId)
    {
        $client = Auth::guard('client')->user();
        $contract = contracts::where('customer_id', $client->id)
            ->where('id', $contractId)
            ->firstOrFail();

        $payment = payments::where('contract_id', $contract->id)
            ->findOrFail($request->payment_id);

        // Validate request
        $request->validate([
            'requested_date' => 'required|date|after:today',
            'reason' => 'required|string|max:500'
        ]);

        // Create postponement request
        $payment->postponementRequests()->create([
            'current_date' => $payment->due_date,
            'requested_date' => $request->requested_date,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        // Record in contract history
        $contract->history()->create([
            'action' => 'Payment Postponement Requested',
            'notes' => "Payment of {$payment->payment_amount} SAR requested to be postponed from " .
                Carbon::parse($payment->due_date)->format('M d, Y') .
                " to " . Carbon::parse($request->requested_date)->format('M d, Y')
        ]);

        // Notify sales manager , sales representative, finance about payment postponement
        $data = [
            'title' => "Payment Postponement Request for Contract " . $contract->contract_number,
            'message' => "Payment of {$payment->payment_amount} SAR requested to be postponed from " .
                Carbon::parse($payment->due_date)->format('M d, Y') .
                " to " . Carbon::parse($request->requested_date)->format('M d, Y'),
        ];
        
        // Different URLs for different roles
        $roleUrls = [
            'sales' => route('payment.show', $payment->id),
            'sales_manager' => route('sales_manager.postponement_requests'),
            'finance' => route('finance.payments.show', $payment->id)
        ];

        $this->notifyRoles(['sales', 'sales_manager', 'finance'], $data, null, null, $roleUrls);

        return redirect()->back()->with('success', 'Payment postponement request has been submitted successfully.');
    }

    /**
     * End of Payment Related Functions
     */

    public function send_updateVisit(Request $request)
    {
        $request->validate([
            'visit_id' => 'required|exists:visit_schedules,id',
            'visit_date' => 'required|date',
            'visit_time' => 'required',
        ]);

        $visit = VisitSchedule::find($request->visit_id);
        if (!$visit) {
            return redirect()->back()->with('error', 'Visit not found.');
        }

        // Only change the status to pending, don't update the schedule yet
        $visit->status = 'pending';
        $visit->change_requested_at = now();
        $visit->update();


        // save visit change request
        $visitChangeRequest = new VisitChangeRequest();
        $visitChangeRequest->visit_id = $visit->id;
        $visitChangeRequest->client_id = Auth::guard('client')->user()->id;
        $visitChangeRequest->visit_date = $request->visit_date;
        $visitChangeRequest->visit_time = $request->visit_time;
        $visitChangeRequest->save();

        // notify technical managers
        $data = [
            'title' => "Visit Change Request" . ($visit->team ? " for " . $visit->team->name : "") . " - Contract #" . $visit->contract->contract_number,
            'message' => "Visit on " . Carbon::parse($visit->visit_date)->format('M d, Y') . " at " . $visit->visit_time . " the client has requested a change.",
        ];

        $client = Auth::guard('client')->user();
        $roleUrls = [
            'technical' => route('technical.visit.requests', $visit->id),
        ];

        $this->notifyRoles(['technical'], $data, $client->id, null, $roleUrls);

        return redirect()->back()->with('success', 'Visit change request sent successfully. Awaiting technical manager approval.');
    }

    public function updateVisit(Request $request)
    {
        $request->validate([
            'visit_id' => 'required|exists:visit_schedules,id',
            'visit_date' => 'required|date',
            'visit_time' => 'required',
            'status' => 'required|in:scheduled,completed,cancelled,pending,approved,rejected',
        ]);

        $visit = VisitSchedule::find($request->visit_id);
        // data from visit change request
        $visitChangeRequest = VisitChangeRequest::where('visit_id', $request->visit_id)->first();
        if (!$visitChangeRequest) {
            return redirect()->back()->with('error', 'Visit change request not found.');
        }

        // Only update the schedule if the request is approved
        if ($request->status === 'approved') {
            $visit->visit_date = $visitChangeRequest->visit_date;
            $visit->visit_time = $visitChangeRequest->visit_time;
            $visit->status = 'scheduled'; // Set back to scheduled after approval
        } else if ($request->status === 'rejected') {
            $visit->status = 'scheduled'; // Set back to scheduled if rejected
        }
        $visit->update();

        // delete visit change request
        $visitChangeRequest->delete();

        // Prepare notification message based on status
        $message = $request->status === 'approved'
            ? 'Your visit change request has been approved. The visit has been rescheduled.'
            : 'Your visit change request has been rejected. The original schedule remains unchanged.';

        // Get the sales agent ID from the contract
        $salesId = $visit->contract->sales_id;

        // Notify the client and relevant team members
        $data = [
            'title' => "Visit Change Request for " . $visit->contract->customer->name,
            'message' => $message,
        ];

        $urls = [
            'client' => route('client.visit.details', $visit->id),
            'team_leader' => route('team-leader.contract.show', $visit->contract->id),
            'sales' => route('view.contract.visit', $visit->contract->id),
        ];

        $this->notifyRoles(['client', 'team_leader', 'sales'], $data, $visit->contract->customer_id, $salesId, $urls);

        $statusMessage = $request->status === 'approved'
            ? 'Visit rescheduled and client notified successfully.'
            : 'Visit change request rejected and client notified.';

        return redirect()->back()->with('success', $statusMessage);
    }

    public function visitDetails($visitId)
    {
        $client = Auth::guard('client')->user();

        // Get the visit with its relationships
        $visit = VisitSchedule::with(['contract', 'team'])
            ->whereHas('contract', function ($query) use ($client) {
                $query->where('customer_id', $client->id);
            })
            ->findOrFail($visitId);

        return view('clients.reports_details', compact('visit'));
    }

    public function contractVisits($contractId)
    {
        $client = Auth::guard('client')->user();

        // Get the contract with its visits
        $contract = contracts::with(['visitSchedules' => function ($query) {
            $query->orderBy('visit_date', 'desc')
                ->orderBy('visit_time', 'desc');
        }, 'visitSchedules.team'])
            ->where('customer_id', $client->id)
            ->findOrFail($contractId);

        return view('clients.contract_visits', compact('contract'));
    }

    /**
     * Send a test notification to the authenticated client
     */
    public function testNotification()
    {
        $client = Auth::guard('client')->user();
        
        if (!$client) {
            return redirect()->back()->with('error', 'You must be logged in as a client to receive notifications.');
        }
        
        // Create notification data
        $data = [
            'title' => 'Test Notification',
            'message' => 'This is a test notification to verify that client notifications are working correctly.',
            'type' => 'info',
            'priority' => 'normal',
        ];
        
        // Send notification directly to this client
        $this->notifyRoles(['client'], $data, $client->id);
        
        return redirect()->back()->with('success', 'Test notification sent successfully. Please check your notifications.');
    }
}
