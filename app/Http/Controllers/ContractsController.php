<?php

namespace App\Http\Controllers;

use App\Models\contracts;
use App\Http\Controllers\Controller;
use App\Models\branchs;
use App\Models\client;
use App\Models\ContractAnnex;
use App\Models\contracts_types;
use App\Models\payments;
use App\Models\PostponementRequest;
use App\Models\VisitSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\VisitScheduleService;
use App\Traits\NotificationDispatcher;

class ContractsController extends Controller
{
    use NotificationDispatcher;

    protected $visitScheduleService;

    public function __construct(VisitScheduleService $visitScheduleService)
    {
        $this->visitScheduleService = $visitScheduleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        isset($request->client_id) ? $client_info = client::find($request->client_id) : $client_info = null;
        $branches = $request->branches;
        $contract_type_id = contracts_types::find($request->contract_type_id);
        $contract_number = $this->generator_contract_number();
        return view('contracts.index', compact('client_info', 'branches', 'contract_type_id', 'contract_number'));
    }

    public function generator_contract_number()
    {
        $latest_contract = contracts::orderBy('id', 'desc')->withTrashed()->first();
        $current_year = date('Y');

        if ($latest_contract) {
            $last_number = intval(substr($latest_contract->contract_number, 6));
            $last_year = substr($latest_contract->contract_number, 2, 4);

            if ($last_year == $current_year) {
                $new_number = $last_number + 1;
            } else {
                $new_number = 1;
            }
        } else {
            $new_number = 1;
        }

        $contract_number = "SP" . $current_year . str_pad($new_number, 4, '0', STR_PAD_LEFT);

        while (contracts::where('contract_number', $contract_number)->exists()) {
            $new_number++;
            $contract_number = "SP" . $current_year . str_pad($new_number, 4, '0', STR_PAD_LEFT);
        }

        return $contract_number;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate required fields
            $rules = [
                'clientName' => 'required|string',
                'clientEmail' => 'required|email',
                'clientPhone' => 'required|string|size:10',
                'clientMobile' => 'required|string',
                'clientAddress' => 'required|string',
                'client_zipcode' => 'nullable|numeric|min:0',
                'client_tax_number' => 'nullable|numeric|min:0',
                'client_city' => 'required|string',
                'contractnumber' => 'required|string|unique:contracts,contract_number',
                'contractstartdate' => 'required|date|after_or_equal:today',
                'contractenddate' => 'required|date|after:contractstartdate',
                'Property_type' => 'required|string',
                'contract_type_id' => 'required|exists:contracts_types,id',
                'payment_type' => 'required|in:prepaid,postpaid',
                'contractamount' => 'required|numeric|min:0',
                'warrantyperiod' => 'required|numeric|min:0',
                'number_of_visits' => 'required|numeric|min:0',
            ];

            if ($request->payment_type === 'postpaid') {
                $rules['number_of_payments'] = 'required|integer|min:1';
                $rules['first_payment_date'] = 'required|date|after_or_equal:today';
                $rules['payment_schedule'] = 'required|in:monthly,custom';

                if ($request->payment_schedule === 'custom') {
                    // Skip payment_date_1 since it uses first_payment_date
                    for ($i = 2; $i <= $request->number_of_payments; $i++) {
                        $rules["payment_date_$i"] = 'required|date|after_or_equal:first_payment_date';
                    }
                }
            }

            $request->validate($rules);

            #check the user already registered or not
            $client_info = client::where('email', $request->clientEmail)->first();

            if ($client_info) {
                #create a new contract
                $this->create_new_contract($request, $client_info);
            } else {
                #create a new client info
                $client_info = new client();
                $client_info->name = $request->clientName;
                $client_info->email = $request->clientEmail;
                $client_info->phone = $request->clientPhone;
                $client_info->mobile = $request->clientMobile;
                $client_info->password = Hash::make($request->clientPhone);
                $client_info->address = $request->clientAddress;
                $client_info->city = $request->client_city;
                $client_info->zip_code = $request->client_zipcode;
                $client_info->tax_number = $request->client_tax_number;
                $client_info->sales_id = Auth::user()->id;
                $client_info->save();

                #create a new contract
                $this->create_new_contract($request, $client_info);
            }

            DB::commit();

            // Send notification to technical and sales managers
            $contract = contracts::latest()->first();
            $notificationData = [
                'title' => 'New Contract Created',
                'message' => 'Contract ' . $contract->contract_number . ' has been created.',
                'type' => 'info',
                'url' => "#",
                'priority' => 'normal'
            ];

            $this->notifyRoles(['technical', 'sales_manager'], $notificationData);

            return redirect()->route('sales.dashboard')
                ->with('success', 'New Contract Created Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Contract creation error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request' => $request->except(['password']),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return back()
                ->with('error', 'Error creating contract: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function create_new_contract(Request $request, $client_info)
    {
        // Calculate VAT and total amount
        $amount = floatval($request->contractamount);
        $vat = $amount * 0.15;
        $total_amount = $amount + $vat;
        $contract = new contracts();
        $contract->customer_id = $client_info->id;
        $contract->sales_id = Auth::user()->id;
        $contract->contract_number = $request->contractnumber;
        $contract->contract_start_date = $request->contractstartdate;
        $contract->contract_end_date = $request->contractenddate;
        $contract->Property_type = $request->Property_type;
        $contract->contract_type = $request->contract_type_id;
        $contract->contract_description = $request->contract_description;
        $contract->contract_price = $total_amount;
        $contract->warranty = $request->warrantyperiod;
        $contract->number_of_visits = $request->number_of_visits;
        $contract->payment_type = $request->payment_type;

        if ($request->number_of_payments) {
            $contract->number_Payments = $request->number_of_payments;
        }

        if ($request->is_multi_branch == "yes") {
            $contract->is_multi_branch = "yes";
        }

        $contract->save();

        // Handle branch information
        if ($request->is_multi_branch == "yes") {
            $branch_names = [];
            for ($i = 0; $i < $request->branchs_number; $i++) {
                $branch_name = $request->input("branchName{$i}");

                // Check for duplicate branch names
                if (in_array($branch_name, $branch_names)) {
                    throw new \Exception("Duplicate branch name: {$branch_name}");
                }
                $branch_names[] = $branch_name;

                // Validate phone number format
                $phone = $request->input("branchphone{$i}");
                if (!preg_match('/^[0-9]{10}$/', $phone)) {
                    throw new \Exception("Invalid phone number format for branch: {$branch_name}");
                }

                // Validate city
                $city = $request->input("branchcity{$i}");
                if (!in_array($city, $this->getSaudiCities())) {
                    throw new \Exception("Invalid city selected for branch: {$branch_name}");
                }

                $branch = new branchs();
                $branch->branch_name = $branch_name;
                $branch->branch_manager_name = $request->input("branchmanager{$i}");
                $branch->branch_manager_phone = $phone;
                $branch->branch_address = $request->input("branchadress{$i}");
                $branch->branch_city = $city;
                $branch->contracts_id = $contract->id;
                $branch->save();
            }
        }

        // Calculate payment amounts
        $amount = floatval($request->contractamount);
        $vat = $amount * 0.15;
        $total_amount = $amount + $vat;

        if ($request->payment_type === 'prepaid') {
            $this->create_payment($contract->id, $client_info->id, $total_amount, now());
        } else {
            $payment_amount = $total_amount / intval($request->number_of_payments);

            // Create first payment using first_payment_date
            try {
                $this->create_payment($contract->id, $client_info->id, $payment_amount, $request->first_payment_date);
            } catch (\Exception $e) {
                Log::error('Error creating first payment: ' . $e->getMessage());
                return back()->with('error', 'Error creating first payment: ' . $e->getMessage());
            }

            if ($request->payment_schedule === 'monthly') {
                // Create remaining monthly payments
                $payment_date = Carbon::parse($request->first_payment_date);
                for ($i = 2; $i <= $request->number_of_payments; $i++) {
                    $payment_date->addMonth();
                    $this->create_payment($contract->id, $client_info->id, $payment_amount, $payment_date->copy());
                }
            } else {
                // Create remaining payments with custom dates
                for ($i = 2; $i <= $request->number_of_payments; $i++) {
                    $this->create_payment($contract->id, $client_info->id, $payment_amount, $request->input("payment_date_$i"));
                }
            }
        }
    }

    private function generateInvoiceNumber()
    {
        // Generate a unique invoice number based on the current timestamp and a random string
        return 'INV-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }

    public function create_payment($contract_id, $client_id, $payment_amount, $payment_date, $annex = null)
    {
        $payment = new payments();
        $payment->invoice_number = $this->generateInvoiceNumber();
        $payment->customer_id = $client_id;
        $payment->contract_id = $contract_id;
        $payment->payment_amount = $payment_amount;
        $payment->due_date = $payment_date;
        $payment->payment_status = 'unpaid';
        $payment->annex_id = $annex->id ?? null;
        $payment->save();
    }

    public function showcontractDetails($id)
    {
        $contract = contracts::find($id);
        $saudiCities = $this->getSaudiCities();
        return view('contracts.contract_details', compact('contract', 'saudiCities'));
    }

    /**
     * Display the specified resource.
     */
    public function show(contracts $contracts)
    {
        $query = contracts::where('sales_id', Auth::user()->id)
            ->whereNotIn('contract_status', ['completed', 'Not approved', 'Stopped'])
            ->whereNull('deleted_at')
            ->with('customer'); // Eager load customer relationship

        // Apply search filters
        if (request('client_name')) {
            $query->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . request('client_name') . '%');
            });
        }

        if (request('client_email')) {
            $query->whereHas('customer', function ($q) {
                $q->where('email', 'like', '%' . request('client_email') . '%');
            });
        }

        if (request('client_phone')) {
            $query->whereHas('customer', function ($q) {
                $q->where('phone', 'like', '%' . request('client_phone') . '%');
            });
        }

        if (request('contract_number')) {
            $query->where('contract_number', 'like', '%' . request('contract_number') . '%');
        }

        $contracts = $query->orderBy('id', 'desc')->get();

        return view('contracts.show_active_contract', compact('contracts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $contract = contracts::find($id);
        $contract_type = contracts_types::all();
        $saudiCities = [
            'Riyadh',
            'Jeddah',
            'Mecca',
            'Medina',
            'Dammam',
            'Khobar',
            'Dhahran',
            'Tabuk',
            'Abha',
            'Taif',
            'Khamis Mushait',
            'Buraidah',
            'Jubail',
            'Yanbu',
            'Najran',
            'Jizan',
            'Hail',
            'Al-Ahsa',
            'Al-Qatif',
            'Al-Kharj'
        ];

        return view('contracts.edit_contract', compact('contract', 'contract_type', 'saudiCities'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $contract = contracts::findOrFail($id);
            $contract->update($request->all());

            // dd($contract);

            DB::commit();

            // Send notifications to relevant roles
            if (Auth::user()->role === 'technical') {
                $url = route('technical.contract.show', $contract->id);
            }elseif(Auth::user()->role === 'sales'){
                $url = route('contract.show.details', $contract->id);
            }elseif(Auth::user()->role === 'sales_manager'){
                $url = route('sales_manager.contract.view', $contract->id);
            }elseif(Auth::user()->role === 'client'){
                $url = route('client.payment.details', $contract->id);
            }

            $notificationData = [
                'title' => 'Contract Updated',
                'message' => 'Contract ' . $contract->contract_number . ' has been updated.',
                'type' => 'info',
                'url' => "#",
                'priority' => 'normal',
            ];

            $this->notifyRoles(['client', 'sales', 'sales_manager', 'technical'], $notificationData);

            return redirect('/sales/Show contracts Details/' . $contract->id)
                ->with('success', 'Contract updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Contract update error: ' . $e->getMessage());
            return back()->with('error', 'Error updating contract: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $contract = contracts::findOrFail($id);
            
            // Send notifications before deletion
            $notificationData = [
                'title' => 'Contract Deleted',
                'message' => 'Contract ' . $contract->contract_number . ' has been deleted.',
                'type' => 'info',
                'url' => "#",
                'priority' => 'normal',
                'client' => $contract->customer
            ];

            $this->notifyRoles(['sales_manager', 'technical'], $notificationData);
            
            $contract->delete();
            DB::commit();

            return redirect()->route('sales.dashboard')
                ->with('success', 'Contract deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Contract deletion error: ' . $e->getMessage());
            return back()->with('error', 'Error deleting contract: ' . $e->getMessage());
        }
    }

    public function postponement_requests()
    {
        $requests = PostponementRequest::with(['payment.customer', 'payment.contract'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('managers.sales.postponement_requests', compact('requests'));
    }

    public function approve_postponement(Request $request)
    {
        $postponement = PostponementRequest::findOrFail($request->request_id);
        $payment = $postponement->payment;

        $postponement->status = 'approved';
        $postponement->approved_at = now();
        $postponement->approved_by = Auth::user()->id;
        $postponement->save();

        // Update payment date
        $payment->due_date = $postponement->requested_date;
        $payment->payment_status = 'pending';
        $payment->save();

        // Notify the client, sales , financial

        $notificationData = [
            'title' => 'Payment Postponement Approved',
            'message' => 'Payment of ' . $payment->payment_amount . ' SAR has been postponed to ' . Carbon::parse($postponement->requested_date)->format('M d, Y'),
            'type' => 'info',
            'url' => "#",
            'priority' => 'normal',
        ];

        $this->notifyRoles(['sales', 'financial', 'client'], $notificationData);

        return back()->with('status', 'Payment postponement request approved successfully');
    }

    public function reject_postponement(Request $request)
    {
        $postponement = PostponementRequest::findOrFail($request->request_id);

        $postponement->status = 'rejected';
        $postponement->reason = $request->reason;
        $postponement->save();

        $notificationData = [
            'title' => 'Payment Postponement Rejected',
            'message' => 'Payment of ' . $postponement->payment->payment_amount . ' SAR has been rejected',
            'type' => 'info',
            'url' => "#",
            'priority' => 'normal',
        ];

        $this->notifyRoles(['sales', 'financial', 'client'], $notificationData);

        return back()->with('status', 'Payment postponement request rejected successfully');
    }

    public function showContractVisitDetails($id)
    {
        $contract = contracts::findOrFail($id);
        $contract->load('customer'); // Eager load customer relationship
        $visits = $contract->visitSchedules;
        return view('contracts.view_contract_visit', compact('contract', 'visits'));
    }

    /**
     * Display the visit report for a specific visit.
     */
    public function viewVisitReport($visit)
    {
        $visit = VisitSchedule::with(['report', 'contract.customer', 'contract.type', 'team'])->findOrFail($visit);

        // Check if visit is completed
        if ($visit->status !== 'completed') {
            return redirect()->back()->with('error', 'Report is only available for completed visits.');
        }

        if (!$visit->report) {
            return redirect()->back()->with('error', 'No report found for this visit.');
        }

        return view('contracts.visit_report', compact('visit'));
    }

    public function getSaudiCities()
    {
        return [
            'Riyadh',
            'Jeddah',
            'Mecca',
            'Medina',
            'Dammam',
            'Taif',
            'Tabuk',
            'Buraidah',
            'Khamis Mushait',
            'Abha',
            'Al-Khobar',
            'Al-Ahsa',
            'Najran',
            'Yanbu',
            'Al-Qatif',
            'Al-Jubail',
            "Ha'il",
            'Al-Hofuf',
            'Al-Mubarraz',
            'Kharj',
            'Qurayyat',
            'Hafr Al-Batin',
            'Al-Kharj',
            'Arar',
            'Sakaka',
            'Jizan',
            'Al-Qunfudhah',
            'Bisha',
            'Al-Bahah',
            'Unaizah',
            'Rafha',
            'Dawadmi',
            'Ar Rass',
            "Al Majma'ah",
            'Tarut',
            'Baljurashi',
            'Shaqra',
            'Al-Zilfi',
            'Ar Rayn',
            'Wadi ad-Dawasir',
            'Badr',
            'Al Ula',
            'Tharmada',
            'Turabah',
            'Tayma'
        ];
    }

    public function createAnnex($contract_id)
    {
        $contract = contracts::findOrFail($contract_id);
        $saudiCities = $this->getSaudiCities();

        // Check if contract is approved
        if ($contract->contract_status !== 'approved') {
            return back()->with('error', 'Only approved contracts can have annexes');
        }

        return view('contracts.create_annex', compact('contract', 'saudiCities'));
    }

    public function storeAnnex(Request $request, $contract_id)
    {
        try {
            DB::beginTransaction();

            $contract = contracts::findOrFail($contract_id);

            // Validate request
            $request->validate([
                'additional_amount' => 'required|numeric|min:0',
                'due_date' => 'required|date|after_or_equal:today',
                'description' => 'nullable|string',
                'branches' => 'required|array|min:1',
                'branches.*.branch_name' => 'required|string',
                'branches.*.branch_manager_name' => 'required|string',
                'branches.*.branch_manager_phone' => 'required|string|size:9',
                'branches.*.branch_address' => 'required|string',
                'branches.*.branch_city' => 'required|string'
            ]);

            // Generate annex number
            $annex_number = $contract->contract_number . '-A' . ($contract->annexes()->count() + 1);

            // Create annex
            $annex = new ContractAnnex([
                'contract_id' => $contract_id,
                'annex_number' => $annex_number,
                'annex_date' => now(),
                'additional_amount' => $request->additional_amount,
                'description' => $request->description,
                'status' => 'pending',
                'created_by' => Auth::id()
            ]);
            $annex->save();

            // Create branches and link them to the annex
            foreach ($request->branches as $branchData) {
                $branch = new branchs([
                    'branch_name' => $branchData['branch_name'],
                    'branch_manager_name' => $branchData['branch_manager_name'],
                    'branch_manager_phone' => $branchData['branch_manager_phone'],
                    'branch_address' => $branchData['branch_address'],
                    'branch_city' => $branchData['branch_city'],
                    'contracts_id' => $contract_id,
                    'annex_id' => $annex->id // Link branch to annex
                ]);
                $branch->save();
            }

            // create payment for the annex
            $this->create_payment($contract_id, $contract->customer_id, $request->additional_amount, $request->due_date, $annex);

            DB::commit();

            // Notify the team leader,client,sales manager,technical
            $notificationData = [
                'title' => 'Annex Created',
                'message' => "Annex {$annex->annex_number} has been created",
                'type' => 'info',
                'url' => "#",
                'priority' => 'high',
            ];

            $this->notifyRoles(['team_leader', 'client', 'sales_manager', 'technical'], $notificationData);

            DB::commit();

            return redirect()->route('contract.show.details', $contract_id)
                ->with('success', 'Contract annex with ' . count($request->branches) . ' branches created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->with('error', 'Error creating contract annex: ' . $e->getMessage());
        }
    }

    public function getAnnexDetails($id)
    {
        try {
            $annex = ContractAnnex::with(['payment', 'contract'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'annex_number' => $annex->annex_number,
                'annex_date' => $annex->annex_date,
                'additional_amount' => $annex->additional_amount,
                'description' => $annex->description,
                'status' => $annex->status,
                'payment' => [
                    'amount' => $annex->payment ? $annex->payment->payment_amount : null,
                    'due_date' => $annex->payment ? $annex->payment->due_date : null
                ],
                'branches' => $annex->contract->branchs()
                    ->where('contracts_id', $annex->contract_id)
                    ->get()
                    ->map(function ($branch) {
                        return [
                            'id' => $branch->id,
                            'name' => $branch->branch_name,
                            'manager_name' => $branch->branch_manager_name,
                            'phone' => substr($branch->branch_manager_phone, 3), // Remove 966 prefix
                            'address' => $branch->branch_address,
                            'city' => $branch->branch_city
                        ];
                    })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load annex details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approveAnnex($id)
    {
        try {
            // Check if user is a sales manager
            if (Auth::user()->role !== 'sales_manager') {
                return back()->with('error', 'Only sales managers can approve annexes');
            }

            $annex = ContractAnnex::findOrFail($id);

            // Additional authorization check
            if ($annex->status !== 'pending') {
                return back()->with('error', 'This annex cannot be approved');
            }

            DB::beginTransaction();

            $annex->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Update contract and add the additional amount to the contract price
            $contract = $annex->contract;
            $contract->is_multi_branch = 'yes';
            $contract->contract_price += $annex->additional_amount;
            $contract->save();

            // Get branches added by this annex
            $newBranches = branchs::where('annex_id', $annex->id)->get();

            // Create a temporary contract object for scheduling only the new branches
            $tempContract = clone $contract;
            $tempContract->setRelation('branchs', $newBranches);

            // Schedule visits for the new branches only
            try {
                $this->visitScheduleService->createVisitSchedule($tempContract);
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Failed to schedule visits for annex branches: ' . $e->getMessage());
                return back()->with('error', 'Failed to schedule visits for the new branches');
            }

            // Notify relevant parties
            $notificationData = [
                'title' => 'Annex Approved',
                'message' => "Annex {$annex->annex_number} has been approved and visits scheduled for " . $newBranches->count() . " new branches",
                'type' => 'info',
                'url' => "#",
                'client' => $contract->customer,
                'priority' => 'normal'
            ];

            $this->notifyRoles(['client', 'sales', 'sales_manager', 'technical'], $notificationData);

            DB::commit();

            return back()->with('success', 'Annex approved successfully and visits scheduled for new branches');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Annex approval error: ' . $e->getMessage());
            return back()->with('error', 'Error approving annex');
        }
    }

    public function rejectAnnex($id)
    {
        try {
            // Check if user is a sales manager
            if (Auth::user()->role !== 'sales_manager') {
                return back()->with('error', 'Only sales managers can reject annexes');
            }

            $annex = ContractAnnex::findOrFail($id);

            // Additional authorization check
            if ($annex->status !== 'pending') {
                return back()->with('error', 'This annex cannot be rejected');
            }

            DB::beginTransaction();

            $annex->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Notify relevant parties
            $notificationData = [
                'title' => 'Annex Rejected',
                'message' => "Annex {$annex->annex_number} has been rejected",
                'type' => 'info',
                'url' => "#",
                'client' => $annex->contract->customer,
                'priority' => 'normal'
            ];

            $this->notifyRoles(['client', 'sales', 'sales_manager', 'technical'], $notificationData);

            DB::commit();

            // Notify the team leader,client,sales manager,technical
            $notificationData = [
                'title' => 'Annex Rejected',
                'message' => "Annex {$annex->annex_number} has been rejected",
                'type' => 'info',
                'url' => "#",
                'priority' => 'high',
            ];

            $this->notifyRoles(['team_leader', 'client', 'sales_manager', 'technical'], $notificationData);

            return back()->with('success', 'Annex rejected successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Annex rejection error: ' . $e->getMessage());
            return back()->with('error', 'Error rejecting annex');
        }
    }

    public function editAnnex($id)
    {
        $annex = ContractAnnex::findOrFail($id);
        $contract = $annex->contract;
        $branches = branchs::all();
        $saudiCities = $this->getSaudiCities();

        return view('contracts.edit_annex', compact('annex', 'contract', 'branches', 'saudiCities'));
    }

    public function updateAnnex(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $annex = ContractAnnex::findOrFail($id);
            $contract = $annex->contract;

            // Validate request
            $request->validate([
                'additional_amount' => 'required|numeric|min:0',
                'due_date' => 'required|date|after_or_equal:today',
                'description' => 'nullable|string',
                'branches' => 'required|array|min:1',
                'branches.*.branch_name' => 'required|string',
                'branches.*.branch_manager_name' => 'required|string',
                'branches.*.branch_manager_phone' => 'required|string|size:9',
                'branches.*.branch_address' => 'required|string',
                'branches.*.branch_city' => 'required|string'
            ]);

            // Update annex
            $annex->update([
                'additional_amount' => $request->additional_amount,
                'description' => $request->description
            ]);

            // Update payment
            if ($annex->payment) {
                $annex->payment->update([
                    'payment_amount' => $request->additional_amount,
                    'due_date' => $request->due_date
                ]);
            }

            // Update existing branches and create new ones
            $existingBranchIds = [];
            foreach ($request->branches as $branchData) {
                if (isset($branchData['id'])) {
                    // Update existing branch
                    $branch = branchs::findOrFail($branchData['id']);
                    $branch->update([
                        'branch_name' => $branchData['branch_name'],
                        'branch_manager_name' => $branchData['branch_manager_name'],
                        'branch_manager_phone' => $branchData['branch_manager_phone'],
                        'branch_address' => $branchData['branch_address'],
                        'branch_city' => $branchData['branch_city']
                    ]);
                    $existingBranchIds[] = $branch->id;
                } else {
                    // Create new branch
                    $branch = new branchs([
                        'branch_name' => $branchData['branch_name'],
                        'branch_manager_name' => $branchData['branch_manager_name'],
                        'branch_manager_phone' => $branchData['branch_manager_phone'],
                        'branch_address' => $branchData['branch_address'],
                        'branch_city' => $branchData['branch_city'],
                        'contracts_id' => $contract->id
                    ]);
                    $branch->save();
                    $existingBranchIds[] = $branch->id;
                }
            }

            // Delete branches that were removed
            branchs::where('contracts_id', $contract->id)
                ->whereNotIn('id', $existingBranchIds)
                ->delete();

            DB::commit();

            // Notify the team leader,client,sales manager,technical
            $notificationData = [
                'title' => 'Annex Updated',
                'message' => "Annex {$annex->annex_number} has been updated",
                'type' => 'info',
                'url' => "#",
                'priority' => 'high',
            ];

            $this->notifyRoles(['team_leader', 'client', 'sales_manager', 'technical'], $notificationData);

            return redirect()->route('contract.show.details', $contract->id)
                ->with('success', 'Contract annex updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Annex update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating contract annex: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroyAnnex($contract, $annex)
    {
        try {
            DB::beginTransaction();

            $annex = ContractAnnex::findOrFail($annex);
            $branches = $annex->contract->branchs()->where('annex_id', $annex->id)->first();
            $payment = $annex->contract->payments()->where('annex_id', $annex->id)->first();
            if ($branches) {
                $branches->delete();
            }
            if ($payment) {
                $payment->delete();
            }
            // remove the annex price from the contract price if annex is approved
            $contract = contracts::findOrFail($contract);
            if ($annex->annex_status == 'approved') {
                $contract->contract_price -= $annex->additional_amount;
                $contract->save();
            }

            $annex->delete();

            DB::commit();

            // Notify the team leader,client,sales manager,technical
            $notificationData = [
                'title' => 'Annex Deleted',
                'message' => "Annex {$annex->annex_number} has been deleted",
                'type' => 'info',
                'url' => "#",
                'priority' => 'high',
            ];

            $this->notifyRoles(['sales_manager', 'technical', 'client','team_leader',], $notificationData);

            return redirect()->route('contract.show.details', $contract->id)
                ->with('success', 'Annex deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Annex delete error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting annex: ' . $e->getMessage())
                ->withInput();
        }
    }
}
