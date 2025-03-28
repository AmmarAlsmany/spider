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
use App\Models\EquipmentContract; // Add the EquipmentContract model import
use App\Models\EquipmentType;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\VisitScheduleService;
use App\Traits\NotificationDispatcher;
use Exception;
use Mpdf\Mpdf;

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
        // Cache contract types to avoid repeated database queries
        $contract_types = cache()->remember('contract_types', 60*60, function() {
            return contracts_types::all();
        });
        
        $property_types = ['Residential', 'Commercial', 'Industrial', 'Government'];
        
        // Only query client info if client_id is provided
        $client_info = null;
        if (isset($request->client_id)) {
            $client_info = client::find($request->client_id);
        }
        
        $branches = $request->branches ?? 0;
        
        // Only query contract type if contract_type_id is provided
        $contract_type_id = null;
        if (isset($request->contract_type_id)) {
            // Now we can use eager loading since the relationship is defined
            $contract_type_id = contracts_types::with('contracts')->find($request->contract_type_id);
        }
        
        // Generate contract number - use caching to avoid repeated queries
        $contract_number = cache()->remember('latest_contract_number_' . date('Y-m-d'), 60, function() {
            return $this->generator_contract_number();
        });
        
        return view('contracts.index', compact('client_info', 'branches', 'contract_type_id', 'contract_number', 'contract_types', 'property_types'));
    }

    public function generator_contract_number()
    {
        $current_year = date('Y');
        $prefix = "SP" . $current_year;

        // Use a more efficient query with a specific filter for current year's prefix
        $latest_contract = contracts::select('contract_number')
            ->where('contract_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->limit(1)
            ->withTrashed()
            ->first();

        if ($latest_contract) {
            // Extract the numeric part and increment
            $last_number = intval(substr($latest_contract->contract_number, 6));
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }

        $contract_number = $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);

        // Check if the generated number already exists (use a direct query)
        $exists = contracts::where('contract_number', $contract_number)->exists();
        
        if ($exists) {
            // If it exists, find the max number and increment by 1 (more efficient query)
            $max_number = contracts::where('contract_number', 'like', $prefix . '%')
                ->max(DB::raw('CAST(SUBSTRING(contract_number, 7) AS UNSIGNED)'));
            $new_number = $max_number + 1;
            $contract_number = $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
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
                'clientPhone' => 'required|string',
                'clientMobile' => 'required|string',
                'clientAddress' => 'required|string',
                'client_zipcode' => 'nullable|numeric|min:0',
                'client_tax_number' => 'nullable|numeric|min:0',
                'clientCity' => 'required|string',
                'contractnumber' => 'required|string|unique:contracts,contract_number',
                'contractstartdate' => 'required|date|after_or_equal:today',
                'contractenddate' => 'required|date|after:contractstartdate',
                'visit_start_date' => 'required|date|after_or_equal:contractstartdate|before_or_equal:contractenddate',
                'Property_type' => 'required|in:Residential,Commercial,Industrial,Government',
                'contract_type_id' => 'required|exists:contracts_types,id',
                'contract_description' => 'required|string',
                'number_of_visits' => 'required|numeric|min:1',
                'payment_type' => 'required|in:prepaid,postpaid',
                'contractamount' => 'required|numeric|min:0',
                'warranty' => 'required|numeric|min:0',
                'number_of_payments' => 'required_if:payment_type,postpaid|numeric|min:1',
                'payment_schedule' => 'required_if:payment_type,postpaid|in:monthly,custom',
                'payment_date_*' => 'required_if:payment_schedule,custom|date|after_or_equal:today'
            ];

            $customMessages = [
                'clientName.required' => 'The client name field is required.',
                'clientEmail.required' => 'The client email field is required.',
                'clientEmail.email' => 'Please enter a valid email address.',
                'clientEmail.unique' => 'This email is already in use.',
                'clientPhone.required' => 'The client phone field is required.',
                'clientPhone.unique' => 'This phone number is already in use.',
                'clientPhone.size' => 'The phone number must be exactly 10 digits.',
                'clientMobile.required' => 'The client mobile field is required.',
                'clientMobile.unique' => 'This mobile number is already in use.',
                'clientAddress.required' => 'The client address field is required.',
                'clientCity.required' => 'The client city field is required.',
                'contractnumber.required' => 'The contract number field is required.',
                'contractnumber.unique' => 'This contract number is already in use.',
                'contractstartdate.required' => 'The contract start date is required.',
                'contractstartdate.after_or_equal' => 'The start date must be today or a future date.',
                'contractenddate.required' => 'The contract end date is required.',
                'contractenddate.after' => 'The end date must be after the start date.',
                'visit_start_date.required' => 'The visit start date is required.',
                'visit_start_date.date' => 'The visit start date must be a valid date.',
                'visit_start_date.after_or_equal' => 'The visit start date must be on or after the contract start date.',
                'visit_start_date.before_or_equal' => 'The visit start date must be on or before the contract end date.',
                'Property_type.required' => 'The property type field is required.',
                'contract_description.required' => 'The contract description field is required.',
                'number_of_visits.required' => 'The number of visits field is required.',
                'number_of_visits.numeric' => 'The number of visits must be a number.',
                'number_of_visits.min' => 'The number of visits must be at least 1.',
                'payment_type.required' => 'The payment type field is required.',
                'contractamount.required' => 'The contract amount field is required.',
                'contractamount.numeric' => 'The contract amount must be a number.',
                'contractamount.min' => 'The contract amount must be greater than 0.',
                'warranty.required' => 'The warranty period field is required.',
                'warranty.numeric' => 'The warranty period must be a number.',
                'warranty.min' => 'The warranty period must be 0 or greater.',
                'number_of_payments.required_if' => 'The number of payments is required for postpaid payment type.',
                'number_of_payments.numeric' => 'The number of payments must be a number.',
                'number_of_payments.min' => 'The number of payments must be at least 1.',
                'payment_schedule.required_if' => 'The payment schedule is required for postpaid payment type.',
                'payment_date_*.required_if' => 'All payment dates are required for custom payment schedule.',
                'payment_date_*.after_or_equal' => 'Payment dates must be today or future dates.'
            ];

            $request->validate($rules, $customMessages);

            #check the user already registered or not
            $client_info = client::where('email', $request->clientEmail)->first();

            if ($client_info) {
                #create a new contract
                $this->create_new_contract($request, $client_info);
            } else {
                #create a new client info
                $client = new client();
                $client->name = $request->clientName;
                $client->email = $request->clientEmail;
                $client->phone = $request->clientPhone;
                $client->mobile = $request->clientMobile;
                $client->password = Hash::make($request->clientMobile);
                $client->address = $request->clientAddress;
                $client->city = $request->clientCity;
                $client->zip_code = $request->client_zipcode;
                $client->tax_number = $request->client_tax_number;
                $client->sales_id = Auth::user()->id;
                $client->save();

                #create a new contract
                $this->create_new_contract($request, $client);
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

            $this->notifyRoles(['technical', 'sales_manager', 'client'], $notificationData, $contract->customer_id, $contract->sales_id);

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
        $contract->visit_start_date = $request->visit_start_date;
        $contract->Property_type = $request->Property_type;
        $contract->contract_type = $request->contract_type_id; // Use contract_type_id instead of contracttype
        $contract->contract_description = $request->contract_description;
        $contract->contract_price = $total_amount;
        $contract->warranty = $request->warranty;
        $contract->number_of_visits = $request->number_of_visits;
        $contract->payment_type = $request->payment_type;

        if ($request->payment_type === 'postpaid') {
            $contract->number_Payments = $request->number_of_payments;
        }

        if ($request->is_multi_branch == "yes") {
            $contract->is_multi_branch = "yes";
        }

        $contract->save();

        // Handle branch information
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
            $branch->branch_address = $request->input("branchAddress{$i}");
            $branch->branch_city = $city;
            $branch->contracts_id = $contract->id;
            $branch->save();
        }


        // Calculate payment amounts
        $amount = floatval($request->contractamount);
        $vat = $amount * 0.15;
        $total_amount = $amount + $vat;

        if ($request->payment_type === 'prepaid') {
            $this->create_payment($contract->id, $client_info->id, $total_amount, $request->first_payment_date);
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

    public function createEquipmentContract(Request $request)
    {
        // Get all clients for the dropdown if viewing existing clients form
        $clients = [];
        if ($request->has('existing')) {
            $clients = client::all();
        }

        // Get contract number
        $contract_number = $this->generator_contract_number();

        // Get all active equipment types
        $equipment_types = EquipmentType::where('is_active', true)->get();

        return view('managers.sales.create_equipment_contract', compact('clients', 'contract_number', 'equipment_types'));
    }

    public function storeEquipmentContract(Request $request)
    {
        try {
            DB::beginTransaction();
            $rules = [
                'equipment_type_id' => 'required|exists:equipment_types,id',
                'equipment_model' => 'required|string',
                'equipment_quantity' => 'required|integer|min:1',
                'equipment_description' => 'required|string',
                'contractamount' => 'required|numeric|min:0',
                'warranty' => 'required|numeric|min:0',
                'payment_type' => 'required|in:postpaid,prepaid',
                'number_of_payments' => 'required_if:payment_type,postpaid|integer|min:1|max:12',
                'branchName.*' => 'required|string',
                'branchmanager.*' => 'required|string',
                'branchmanagerPhone.*' => ['required', 'string', 'regex:/^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$/'],
                'branchAddress.*' => 'required|string',
                'branchCity.*' => 'required|string',
            ];

            $request->validate($rules);

            if (!$request->has('client_id')) {
                $rules['customer_name'] = 'required|string';
                $rules['customer_mobile'] = ['required', 'string', 'regex:/^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$/'];
                $rules['customer_email'] = 'required|email|unique:clients,email';
                $rules['customer_address'] = 'required|string';
                $rules['customer_city'] = 'required|string';
                $rules['customer_zip_code'] = 'nullable|string';
                $rules['customer_tax_number'] = 'required|string|min:10|max:15';
            } else {
                $rules['client_id'] = 'required|exists:clients,id';
            }

            // Handle client creation/selection
            if ($request->has('client_id')) {
                $client = client::findOrFail($request->client_id);
            } else {
                // Create new client
                $client = new client();
                $client->name = $request->customer_name;
                $client->email = $request->customer_email;
                $client->phone = $request->customer_mobile;
                $client->mobile = $request->customer_mobile;
                $client->address = $request->customer_address;
                $client->city = $request->customer_city;
                $client->zip_code = $request->customer_zip_code;
                $client->tax_number = $request->customer_tax_number;
                $client->password = Hash::make($request->customer_mobile);
                $client->sales_id = Auth::user()->id;
                $client->save();
            }

            $amount = floatval($request->contractamount);
            $vat = $amount * 0.15;
            $total_amount = $amount + $vat;

            $unit_price = $request->contractamount / $request->equipment_quantity;
            $vat_amount = $unit_price * 0.15;
            $total_price = $unit_price + $vat_amount;
            $total_with_vat = $total_price * $request->equipment_quantity;

            // Calculate contract end date based on warranty period
            $contract_end_date = now()->addMonths(intval($request->warranty));

            $contract = new contracts();
            $contract->customer_id = $client->id;
            $contract->sales_id = Auth::user()->id;
            $contract->contract_number = $request->contract_number;
            $contract->contract_start_date = now();
            $contract->contract_end_date = $contract_end_date;
            $contract->Property_type = 'equipment';
            $contract->contract_type = contracts_types::where('name', 'Buy equipment')->firstOrFail()->id;
            $contract->contract_price = $total_amount;
            $contract->payment_type = $request->payment_type;
            $contract->contract_status = 'pending';
            $contract->is_multi_branch = 'no';
            $contract->contract_description = $request->equipment_description;
            $contract->warranty = $request->warranty;
            $contract->save();

            // Create equipment contract details
            $equipmentContract = new EquipmentContract([
                'contract_id' => $contract->id,
                'equipment_type' => $request->equipment_type_id,
                'equipment_model' => $request->equipment_model,
                'equipment_quantity' => $request->equipment_quantity,
                'equipment_description' => $request->equipment_description,
                'unit_price' => $unit_price,
                'total_price' => $total_price,
                'vat_amount' => $vat_amount,
                'total_with_vat' => $total_with_vat,
            ]);
            $equipmentContract->save();

            // Create branch records for the contract
            if ($request->has('branchName') && is_array($request->branchName)) {
                foreach ($request->branchName as $index => $branchName) {
                    $branchContract = new branchs([
                        'branch_name' => $branchName,
                        'branch_manager_name' => $request->branchmanager[$index],
                        'branch_manager_phone' => $request->branchmanagerPhone[$index],
                        'branch_address' => $request->branchAddress[$index],
                        'branch_city' => $request->branchCity[$index],
                        'contracts_id' => $contract->id,
                    ]);
                    $branchContract->save();
                }
                
                // If there are multiple branches, update the contract to indicate it's a multi-branch contract
                if (count($request->branchName) > 1) {
                    $contract->is_multi_branch = 'yes';
                    $contract->save();
                }
            }

            // Create payment record(s)
            if ($request->payment_type === 'prepaid') {
                // Create single payment for prepaid
                $payment = new payments([
                    'customer_id' => $client->id,
                    'contract_id' => $contract->id,
                    'due_date' => now(),
                    'payment_amount' => $total_amount,
                    'payment_description' => 'Full payment for equipment purchase',
                    'invoice_number' => $this->generateInvoiceNumber()
                ]);
                $payment->save();
            } else {
                $payment_amount = $total_amount / intval($request->number_of_payments);

                // Create first payment using first_payment_date
                try {
                    $this->create_payment($contract->id, $client->id, $payment_amount, $request->first_payment_date);
                } catch (\Exception $e) {
                    Log::error('Error creating first payment: ' . $e->getMessage());
                    return back()->with('error', 'Error creating first payment: ' . $e->getMessage());
                }

                if ($request->payment_schedule === 'monthly') {
                    // Create remaining monthly payments
                    $payment_date = Carbon::parse($request->first_payment_date);
                    for ($i = 2; $i <= $request->number_of_payments; $i++) {
                        $payment_date->addMonth();
                        $this->create_payment($contract->id, $client->id, $payment_amount, $payment_date->copy());
                    }
                } else {
                    // Create remaining payments with custom dates
                    for ($i = 2; $i <= $request->number_of_payments; $i++) {
                        $this->create_payment($contract->id, $client->id, $payment_amount, $request->input("payment_date_$i"));
                    }
                }
            }

            DB::commit();
            return redirect()->route('contract.show.details', $contract->id)->with('success', 'Equipment contract created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating equipment contract: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Error creating equipment contract. Please try again.'])
                ->withInput();
        }
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
        $saudiCities = $this->getSaudiCities();

        return view('contracts.edit_contract', compact('contract', 'contract_type', 'saudiCities'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'contract_number' => 'required|string',
                'contract_type' => 'required|exists:contracts_types,id',
                'contract_description' => 'required|string',
                'warranty' => 'required|integer|min:0',
                'contract_start_date' => 'required|date',
                'contract_end_date' => 'required|date|after:contract_start_date',
                'visit_start_date' => 'required|date|after_or_equal:contract_start_date|before_or_equal:contract_end_date',
                'contract_price' => 'required|numeric|min:0',
                'payment_type' => 'required|string',
                'number_Payments' => 'nullable|integer|min:1',
                'branch_name.*' => 'required_if:is_multi_branch,yes|string',
                'branch_manager_name.*' => 'required_if:is_multi_branch,yes|string',
                'branch_manager_phone.*' => 'required_if:is_multi_branch,yes|string',
                'branch_address.*' => 'required_if:is_multi_branch,yes|string',
                'branch_city.*' => 'required_if:is_multi_branch,yes|string',
                'payment_amount.*' => 'required|numeric|min:0',
                'payment_date.*' => 'required|date|after_or_equal:contract_start_date',
                'number_of_visits' => 'required|integer|min:1',
                'Property_type' => 'required|string|in:Residential,Commercial,Industrial,Government',

            ]);

            DB::beginTransaction();

            $contract = contracts::findOrFail($id);

            // Update the contract with validated data
            try {
                $contract->update($validated);
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Failed to update contract');
            }

            // Handle branch updates if needed
            if ($request->has('branch_name')) {
                // Get arrays of branch data
                $branchNames = $request->input('branch_name');
                $branchManagerNames = $request->input('branch_manager_name');
                $branchManagerPhones = $request->input('branch_manager_phone');
                $branchAddresses = $request->input('branch_address');
                $branchCities = $request->input('branch_city');
                $branchIds = $request->input('branch_id', []);

                // Update or create branches
                foreach ($branchNames as $index => $branchName) {
                    $branchData = [
                        'branch_name' => $branchName,
                        'branch_manager_name' => $branchManagerNames[$index],
                        'branch_manager_phone' => $branchManagerPhones[$index],
                        'branch_address' => $branchAddresses[$index],
                        'branch_city' => $branchCities[$index],
                        'contracts_id' => $contract->id
                    ];

                    // If branch_id exists, update existing branch
                    if (isset($branchIds[$index])) {
                        $branch = branchs::find($branchIds[$index]);
                        if ($branch) {
                            try {
                                $branch->update($branchData);
                            } catch (Exception $e) {
                                Log::error($e->getMessage());
                            }
                        }
                    } else {
                        // Create new branch
                        try {
                            branchs::create($branchData);
                        } catch (Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }
                }
            }

            // Handle payment updates if needed
            if ($request->has('payment_amount')) {
                // Get arrays of payment data
                $paymentAmounts = $request->input('payment_amount');
                $paymentDates = $request->input('payment_date');

                // Get existing payments to preserve invoice numbers
                $existingPayments = payments::where('contract_id', $contract->id)->get()->keyBy('id');
                $paymentIds = $request->input('payment_id', []);

                // Delete payments that are no longer in the form
                payments::where('contract_id', $contract->id)
                    ->whereNotIn('id', $paymentIds)
                    ->delete();

                // Create or update payments
                foreach ($paymentAmounts as $index => $amount) {
                    $paymentId = isset($paymentIds[$index]) ? $paymentIds[$index] : null;
                    $paymentData = [
                        'contract_id' => $contract->id,
                        'customer_id' => $contract->customer_id,
                        'payment_amount' => $amount,
                        'due_date' => $paymentDates[$index],
                        'payment_status' => 'pending',
                    ];

                    if ($paymentId && isset($existingPayments[$paymentId])) {
                        // Update existing payment
                        $existingPayment = $existingPayments[$paymentId];
                        $paymentData['invoice_number'] = $existingPayment->invoice_number;
                        $existingPayment->update($paymentData);
                    } else {
                        // Create new payment with new invoice number
                        $paymentData['invoice_number'] = $this->generateInvoiceNumber();
                        payments::create($paymentData);
                    }
                }
            }

            DB::commit();

            // Send notifications to relevant roles
            if (Auth::user()->role === 'technical') {
                $url = route('technical.contract.show', $contract->id);
            } elseif (Auth::user()->role === 'sales') {
                $url = route('contract.show.details', $contract->id);
            } elseif (Auth::user()->role === 'sales_manager') {
                $url = route('sales_manager.contract.view', $contract->id);
            } elseif (Auth::user()->role === 'client') {
                $url = route('client.payment.details', $contract->id);
            }

            $notificationData = [
                'title' => 'Contract Updated Successfully',
                'message' => 'Contract ' . $contract->contract_number . ' has been updated.',
                'type' => 'info',
                'url' => $url,
                'priority' => 'normal',
            ];

            $this->notifyRoles(['client', 'sales', 'sales_manager', 'technical'], $notificationData, $contract->customer_id, $contract->sales_id);

            return redirect('/sales/Show contracts Details/' . $contract->id)
                ->with('success', 'Contract updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Contract update error: ' . $e->getMessage());
            return back()->with('error', 'Error updating contract: ' . $e->getMessage());
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
            'priority' => 'normal'
        ];

        $this->notifyRoles(['sales', 'financial', 'client'], $notificationData, $payment->customer_id, $payment->sales_id);

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

        $this->notifyRoles(['sales', 'financial', 'client'], $notificationData, $postponement->payment->customer_id, $postponement->payment->sales_id);

        return back()->with('status', 'Payment postponement request rejected successfully');
    }

    public function showContractVisitDetails($id)
    {
        $contract = contracts::findOrFail($id);
        $contract->load('customer'); // Eager load customer relationship
        $visits = $contract->visitSchedules()->paginate(10); // Paginate visits, 10 per page
        return view('contracts.view_contract_visit', compact('contract', 'visits'));
    }

    public function view_completed_contracts()
    {
        $contracts = contracts::where('sales_id', Auth::user()->id)
            ->where('contract_status', 'completed')
            ->with('customer') // Eager load customer relationship
            ->get();

        return view('managers.sales.completed_contract', compact('contracts'));
    }

    public function view_stopped_contract()
    {
        $contracts = contracts::where('sales_id', Auth::user()->id)
            ->where('contract_status', 'stopped')
            ->with('customer') // Eager load customer relationship
            ->get();

        return view('managers.sales.stoped_contract', compact('contracts'));
    }

    public function view_cancelled_contracts()
    {
        $contracts = contracts::where('sales_id', Auth::user()->id)
            ->where('contract_status', 'cancelled')
            ->orWhere('contract_status', 'Not approved')
            ->with('customer') // Eager load customer relationship
            ->get();

        return view('managers.sales.canceled_contract', compact('contracts'));
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
                    'contracts_id' => $contract->id,
                    'annex_id' => $annex->id // Link branch to annex
                ]);
                $branch->save();
            }

            // create payment for the annex
            $this->create_payment($contract_id, $contract->customer_id, $request->additional_amount, $request->due_date, $annex);

            DB::commit();

            // Notify the team leader,client,sales manager,technical
            $notificationData = [
                'title' => 'New Annex Created',
                'message' => "Annex {$annex->annex_number} has been created for contract {$contract->contract_number}",
                'type' => 'info',
                'url' => "#",
                'priority' => 'high',
            ];

            $this->notifyRoles(['team_leader', 'client', 'sales_manager', 'technical'], $notificationData, $contract->customer_id, $contract->sales_id);

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
            
            // Ensure the temporary contract has all required properties
            if (!isset($tempContract->number_of_visits) || empty($tempContract->number_of_visits)) {
                $tempContract->number_of_visits = $contract->number_of_visits;
            }
            
            if (!isset($tempContract->visit_start_date) || empty($tempContract->visit_start_date)) {
                // Use current date as the visit start date for annex branches
                $tempContract->visit_start_date = now()->format('Y-m-d');
            }

            // Schedule visits for the new branches only
            try {
                // Check if there are active teams available
                $activeTeams = Team::where('status', 'active')->count();
                if ($activeTeams == 0) {
                    throw new \Exception('No active teams available for scheduling visits');
                }
                
                $this->visitScheduleService->createVisitSchedule($tempContract);
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Failed to schedule visits for annex branches: ' . $e->getMessage());
                return back()->with('error', 'Failed to schedule visits for the new branches: ' . $e->getMessage());
            }

            // Notify relevant parties
            $notificationData = [
                'title' => 'Annex Approved',
                'message' => "Annex {$annex->annex_number} has been approved and visits scheduled for " . $newBranches->count() . " new branches",
                'type' => 'info',
                'url' => "#",
                'priority' => 'normal'
            ];

            $this->notifyRoles(['client', 'sales', 'sales_manager', 'technical'], $notificationData, $contract->customer_id, $contract->sales_id);

            DB::commit();

            return back()->with('success', 'Annex approved successfully and visits scheduled for new branches');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Annex approval error: ' . $e->getMessage());
            return back()->with('error', 'Error approving annex: ' . $e->getMessage());
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

            // Delete branches associated with this annex
            branchs::where('annex_id', $annex->id)->delete();
            
            // Delete payment associated with this annex
            payments::where('annex_id', $annex->id)->delete();

            $annex->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Notify relevant parties
            $notificationData = [
                'title' => 'Annex Rejected',
                'message' => "Annex {$annex->annex_number} has been rejected for contract {$annex->contract->contract_number}",
                'type' => 'info',
                'url' => "#",
                'priority' => 'normal'
            ];

            $this->notifyRoles(['client', 'sales', 'sales_manager', 'technical'], $notificationData, $annex->contract->customer_id, $annex->contract->sales_id);

            DB::commit();

            return back()->with('success', 'Annex rejected successfully and related branches and payments removed');
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
                    if ($branch) {
                        $branch->update([
                            'branch_name' => $branchData['branch_name'],
                            'branch_manager_name' => $branchData['branch_manager_name'],
                            'branch_manager_phone' => $branchData['branch_manager_phone'],
                            'branch_address' => $branchData['branch_address'],
                            'branch_city' => $branchData['branch_city']
                        ]);
                        $existingBranchIds[] = $branch->id;
                    }
                } else {
                    // Create new branch
                    $branch = new branchs();
                    $branch->contracts_id = $contract->id;
                    $branch->branch_name = $branchData['branch_name'];
                    $branch->branch_manager_name = $branchData['branch_manager_name'];
                    $branch->branch_manager_phone = $branchData['branch_manager_phone'];
                    $branch->branch_address = $branchData['branch_address'];
                    $branch->branch_city = $branchData['branch_city'];
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
                'message' => "Annex {$annex->annex_number} has been updated for contract {$contract->contract_number}",
                'type' => 'info',
                'url' => "#",
                'priority' => 'high',
            ];

            $this->notifyRoles(['team_leader', 'client', 'sales_manager', 'technical'], $notificationData, $annex->contract->customer_id, $annex->contract->sales_id);

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

    // stop the contract
    public function stop_contract($id)
    {
        try {
            DB::beginTransaction();
            $contract = contracts::findOrFail($id);
            $contract->update([
                'contract_status' => 'stopped'
            ]);
            
            // Cancel all scheduled visits for this contract
            $cancelledVisits = $this->visitScheduleService->cancelContractVisits($contract);
            
            DB::commit();

            // Notify the team leader,client,sales manager,technical
            $notificationData = [
                'title' => 'Contract Stopped',
                'message' => "Contract {$contract->contract_number} has been stopped",
                'type' => 'info',
                'url' => "#",
                'priority' => 'high',
            ];
            $this->notifyRoles(['team_leader', 'client', 'sales_manager', 'technical'], $notificationData, $contract->customer_id, $contract->sales_id);
            
            $message = 'Contract stopped successfully';
            if ($cancelledVisits > 0) {
                $message .= " and $cancelledVisits scheduled visits were cancelled";
            }
            
            return redirect()->route('contract.show')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Contract stop error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error stopping contract: ' . $e->getMessage())
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
                'message' => "Annex {$annex->annex_number} has been deleted for contract {$contract->contract_number}",
                'type' => 'info',
                'url' => "#",
                'priority' => 'high',
            ];

            $this->notifyRoles(['sales_manager', 'technical', 'client', 'team_leader',], $notificationData, $annex->contract->customer_id, $annex->contract->sales_id);

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

    /**
     * Download contract as PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadPDF($id)
    {
        try {
            $contract = contracts::with('client')->findOrFail($id);

            // Generate PDF using Laravel's built-in View to String conversion
            $pdf_content = view('contracts.contract_pdf', compact('contract'))->render();

            // Set response headers for PDF download
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="contract_' . $contract->contract_number . '.pdf"',
            ];

            // For now, we'll return the HTML view until we implement the PDF package
            return response($pdf_content, 200, [
                'Content-Type' => 'text/html',
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating contract PDF: ' . $e->getMessage());
            return back()->with('error', 'Unable to generate PDF at this time.');
        }
    }

    public function generatePDF($id)
    {
        $contract = contracts::with('customer', 'branchs', 'visitSchedules', 'payments', 'type', 'history')->findOrFail($id);
        $template = view('pdf_templates.contract_pdf', compact('contract'))->render();
        $filename = 'contract_' . $contract->contract_number . '.pdf';

        $mpdf = new Mpdf(['orientation' => 'L', 'margin' => [10, 10, 10, 10]]);
        $mpdf->WriteHTML($template);
        $pdf = $mpdf->Output('', 'S');

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Display contract renewal form
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showRenewalForm($id)
    {
        $contract = contracts::with(['customer', 'branchs', 'type'])->findOrFail($id);
        
        // Check if the contract is completed and belongs to the current sales rep
        if ($contract->contract_status !== 'completed' || $contract->sales_id != auth()->id()) {
            return redirect()->route('sales.dashboard')
                ->with('error', 'You can only renew completed contracts assigned to you.');
        }
        
        // Get contract types for the form
        $contract_types = contracts_types::all();
        $property_types = ['Residential', 'Commercial', 'Industrial', 'Government'];
        $saudiCities = $this->getSaudiCities();
        
        return view('contracts.renewal_form', compact('contract', 'contract_types', 'property_types', 'saudiCities'));
    }
    
    /**
     * Process contract renewal
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function processRenewal(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            // Find the original contract
            $originalContract = contracts::with(['customer', 'branchs'])->findOrFail($id);
            
            // Validate the request
            $request->validate([
                'contract_start_date' => 'required|date|after_or_equal:today',
                'contract_end_date' => 'required|date|after:contract_start_date',
                'visit_start_date' => 'required|date|after_or_equal:contract_start_date|before_or_equal:contract_end_date',
                'contract_type' => 'required|exists:contracts_types,id',
                'Property_type' => 'required|in:Residential,Commercial,Industrial,Government',
                'contract_description' => 'required|string',
                'warranty' => 'required|integer|min:0',
                'number_of_visits' => 'required|integer|min:1',
                'contract_price' => 'required|numeric|min:0',
                'payment_type' => 'required|in:prepaid,postpaid',
                'number_of_payments' => 'required_if:payment_type,postpaid|integer|min:1',
                'first_payment_date' => 'required|date|after_or_equal:contract_start_date',
                'include_branches.*' => 'nullable',
                'branch_data.*.branch_name' => 'sometimes|required|string|max:255',
                'branch_data.*.branch_manager_name' => 'sometimes|required|string|max:255',
                'branch_data.*.branch_manager_phone' => 'sometimes|required|string',
                'branch_data.*.branch_address' => 'sometimes|required|string',
                'branch_data.*.branch_city' => 'sometimes|required|string',
                'new_branch_data.*.branch_name' => 'sometimes|required|string|max:255',
                'new_branch_data.*.branch_manager_name' => 'sometimes|required|string|max:255',
                'new_branch_data.*.branch_manager_phone' => 'sometimes|required|string',
                'new_branch_data.*.branch_address' => 'sometimes|required|string',
                'new_branch_data.*.branch_city' => 'sometimes|required|string',
            ]);
            
            // Generate a new contract number
            $contract_number = $this->generator_contract_number();
            
            // Calculate VAT and total amount
            $amount = floatval($request->contract_price);
            $vat = $amount * 0.15;
            $total_amount = $amount + $vat;
            
            // Create new contract based on the original one
            $newContract = new contracts();
            $newContract->customer_id = $originalContract->customer_id;
            $newContract->sales_id = auth()->id();
            $newContract->contract_number = $contract_number;
            $newContract->contract_start_date = $request->contract_start_date;
            $newContract->contract_end_date = $request->contract_end_date;
            $newContract->visit_start_date = $request->visit_start_date;
            $newContract->Property_type = $request->Property_type;
            $newContract->contract_type = $request->contract_type;
            $newContract->contract_description = $request->contract_description;
            $newContract->contract_price = $total_amount;
            $newContract->warranty = $request->warranty;
            $newContract->number_of_visits = $request->number_of_visits;
            $newContract->payment_type = $request->payment_type;
            $newContract->contract_status = 'pending';
            
            if ($request->payment_type === 'postpaid') {
                $newContract->number_Payments = $request->number_of_payments;
            }
            
            // Check if we're including branches
            $hasBranches = false;
            
            // Check if the original contract had multiple branches or if we're adding new branches
            if (($request->has('include_branches') && count($request->include_branches) > 0) || 
                ($request->has('new_branch_data') && count($request->new_branch_data) > 0)) {
                $hasBranches = true;
                $newContract->is_multi_branch = "yes";
            }
            
            $newContract->save();
            
            // Copy selected branches from the original contract
            if ($request->has('include_branches')) {
                foreach ($request->include_branches as $branchId) {
                    // Skip if the branch ID starts with 'new_' as these are handled separately
                    if (is_string($branchId) && strpos($branchId, 'new_') === 0) {
                        continue;
                    }
                    
                    $originalBranch = $originalContract->branchs->firstWhere('id', $branchId);
                    
                    if ($originalBranch) {
                        // Get updated branch data if available
                        $branchData = $request->branch_data[$branchId] ?? null;
                        
                        $newBranch = new branchs();
                        $newBranch->contracts_id = $newContract->id;
                        $newBranch->branch_name = $branchData['branch_name'] ?? $originalBranch->branch_name;
                        $newBranch->branch_manager_name = $branchData['branch_manager_name'] ?? $originalBranch->branch_manager_name;
                        $newBranch->branch_manager_phone = $branchData['branch_manager_phone'] ?? $originalBranch->branch_manager_phone;
                        $newBranch->branch_address = $branchData['branch_address'] ?? $originalBranch->branch_address;
                        $newBranch->branch_city = $branchData['branch_city'] ?? $originalBranch->branch_city;
                        $newBranch->save();
                    }
                }
            }
            
            // Add new branches if any
            if ($request->has('new_branch_data') && is_array($request->new_branch_data)) {
                foreach ($request->new_branch_data as $key => $branchData) {
                    // Only process if this new branch is included
                    if (!$request->has('include_branches') || !in_array($key, $request->include_branches)) {
                        continue;
                    }
                    
                    $newBranch = new branchs();
                    $newBranch->contracts_id = $newContract->id;
                    $newBranch->branch_name = $branchData['branch_name'];
                    $newBranch->branch_manager_name = $branchData['branch_manager_name'];
                    $newBranch->branch_manager_phone = $branchData['branch_manager_phone'];
                    $newBranch->branch_address = $branchData['branch_address'];
                    $newBranch->branch_city = $branchData['branch_city'];
                    $newBranch->save();
                }
            }
            
            // Create payments
            if ($request->payment_type === 'prepaid') {
                $this->create_payment($newContract->id, $originalContract->customer_id, $total_amount, $request->first_payment_date);
            } else {
                $payment_amount = $total_amount / intval($request->number_of_payments);
                
                // Create first payment
                $this->create_payment($newContract->id, $originalContract->customer_id, $payment_amount, $request->first_payment_date);
                
                // Create remaining payments
                $payment_date = Carbon::parse($request->first_payment_date);
                for ($i = 2; $i <= $request->number_of_payments; $i++) {
                    $payment_date->addMonth();
                    $this->create_payment($newContract->id, $originalContract->customer_id, $payment_amount, $payment_date->copy());
                }
            }
            
            // Send notification to technical and sales managers
            $notificationData = [
                'title' => 'Contract Renewed',
                'message' => 'Contract ' . $originalContract->contract_number . ' has been renewed with new contract number ' . $newContract->contract_number,
                'type' => 'info',
                'url' => route('contract.show.details', $newContract->id),
                'priority' => 'normal'
            ];
            
            $this->notifyRoles(['technical', 'sales_manager', 'client'], $notificationData, $newContract->customer_id, $newContract->sales_id);
            
            DB::commit();
            
            return redirect()->route('contract.show.details', $newContract->id)
                ->with('success', 'Contract renewed successfully with new contract number: ' . $newContract->contract_number);
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Contract renewal error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'original_contract_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()
                ->with('error', 'Error renewing contract: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Handle contract renewal response
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function handleRenewalResponse(Request $request, $id)
    {
        $contract = contracts::findOrFail($id);
        
        // Validate the request
        $request->validate([
            'response' => 'required|in:yes,no',
        ]);
        
        if ($request->response === 'yes') {
            return redirect()->route('contract.renewal.form', ['id' => $id]);
        } else {
            // Log the decision not to renew
            Log::info('Sales representative decided not to renew contract #' . $contract->contract_number, [
                'user_id' => auth()->id(),
                'contract_id' => $id
            ]);
            
            return redirect()->route('sales.dashboard')
                ->with('info', 'You chose not to renew contract #' . $contract->contract_number);
        }
    }
}
