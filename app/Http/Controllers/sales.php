<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\client;
use App\Models\contracts;
use App\Models\contracts_types;
use App\Models\payments;
use App\Models\tikets;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class sales extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;

        // Get total contracts and approved contracts
        $totalContracts = contracts::where('sales_id', $userId)->count();
        $approvedContracts = contracts::where('sales_id', $userId)
            ->where('contract_status', 'approved')
            ->count();

        // Get total revenue from approved contracts
        $totalRevenue = payments::whereHas('contract', function ($query) use ($userId) {
            $query->where('sales_id', $userId)
                ->where('contract_status', 'approved');
        })
            ->where('payment_status', 'paid')
            ->sum('payment_amount');

        // Get total clients
        $totalClients = client::where('sales_id', $userId)->count();

        // Get tickets statistics
        $openTickets = tikets::whereHas('client_info', function ($query) use ($userId) {
            $query->where('sales_id', $userId);
        })
            ->where('status', 'open')
            ->count();

        $urgentTickets = tikets::whereHas('client_info', function ($query) use ($userId) {
            $query->where('sales_id', $userId);
        })
            ->where('status', 'open')
            ->where('priority', 'high')
            ->count();

        // Get recent contracts
        $recentContracts = contracts::where('sales_id', $userId)
            ->with(['type', 'customer'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent tickets
        $recentTickets = tikets::whereHas('client_info', function ($query) use ($userId) {
            $query->where('sales_id', $userId);
        })
            ->with(['client_info'])
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('managers.sales.dashboard', compact(
            'totalContracts',
            'approvedContracts',
            'totalRevenue',
            'totalClients',
            'openTickets',
            'urgentTickets',
            'recentContracts',
            'recentTickets'
        ));
    }

    public function contractTypeCards()
    {
        $contracts_types = contracts_types::all();
        $clients = client::all();
        return view('managers.sales.create_new_contract_cards', compact('contracts_types', 'clients'));
    }

    public function view_my_clients()
    {
        $clients = client::where('sales_id', Auth::user()->id)->get();
        return view('managers.sales.view_my_clients', compact('clients'));
    }

    public function showClientDetails($id)
    {
        $client = client::find($id);
        $contracts = contracts::where('customer_id', $id)->get();
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
        return view('managers.sales.client_details', compact('client', 'contracts', 'saudiCities'));
    }

    public function updateClientInfo(Request $request, $id)
    {
        try {
            $client = client::findOrFail($id);

            // Validate the request
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:clients,email,' . $id,
                'phone' => 'required|string|size:10',
                'mobile' => 'required|string',
                'address' => 'required|string',
                'city' => 'required|string',
                'zip_code' => 'nullable|numeric|min:0',
                'tax_number' => 'nullable|numeric|min:0',
            ]);

            // Update client information
            $client->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'city' => $request->city,
                'zip_code' => $request->zip_code,
                'tax_number' => $request->tax_number,
            ]);

            return redirect()->back()->with('success', 'Client information updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating client information: ' . $e->getMessage())->withInput();
        }
    }

    public function generateReport(Request $request)
    {
        // Get the authenticated sales user
        $salesId = Auth::user()->id;

        // Get the report type and date range
        $reportType = $request->input('report_type');
        $startDate = null;
        $endDate = null;

        // Set date range based on report type
        switch ($reportType) {
            case 'daily':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'quarterly':
                $startDate = Carbon::now()->startOfQuarter();
                $endDate = Carbon::now()->endOfQuarter();
                break;
            case 'annual':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
                $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                break;
            default:
                // If no report type specified, show all time
                $startDate = Carbon::minValue();
                $endDate = Carbon::now();
        }

        // Get all contracts for the sales person within the date range
        $contracts = contracts::where('sales_id', $salesId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['customer', 'type', 'payments'])
            ->get()
            ->groupBy('contract_status');

        // Get all payments grouped by status within the date range
        $payments = payments::whereHas('contract', function ($query) use ($salesId) {
            $query->where('sales_id', $salesId);
        })
            ->whereBetween('due_date', [$startDate, $endDate])
            ->with('contract')
            ->get()
            ->groupBy('payment_status');

        // Calculate financial summaries
        $financialSummary = [
            'total_contract_value' => $contracts->flatten()->sum('contract_price'),
            'total_paid' => $payments->flatten()->where('payment_status', 'paid')->sum('payment_amount'),
            'total_pending' => $payments->flatten()->where('payment_status', 'unpaid')->sum('payment_amount'),
            'total_overdue' => $payments->flatten()->where('payment_status', 'overdue')->sum('payment_amount')
        ];

        // Contract statistics
        $contractStats = [
            'total_contracts' => $contracts->flatten()->count(),
            'active_contracts' => $contracts->get('approved', collect())->count(),
            'pending_contracts' => $contracts->get('pending', collect())->count(),
            'completed_contracts' => $contracts->get('completed', collect())->count(),
            'cancelled_contracts' => $contracts->get('cancelled', collect())->count()
        ];

        // Add period information
        $periodInfo = [
            'report_type' => $reportType,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'period_label' => $this->getPeriodLabel($reportType, $startDate, $endDate)
        ];

        return view('managers.sales.report', compact(
            'contracts',
            'payments',
            'financialSummary',
            'contractStats',
            'periodInfo'
        ));
    }

    private function getPeriodLabel($reportType, $startDate, $endDate)
    {
        switch ($reportType) {
            case 'daily':
                return 'Daily Report - ' . $startDate->format('F d, Y');
            case 'monthly':
                return 'Monthly Report - ' . $startDate->format('F Y');
            case 'quarterly':
                return 'Quarterly Report - Q' . ceil($startDate->month / 3) . ' ' . $startDate->year;
            case 'annual':
                return 'Annual Report - ' . $startDate->year;
            case 'custom':
                return 'Custom Report (' . $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y') . ')';
            default:
                return 'All Time Report';
        }
    }

    public function toDoList(Request $request)
    {
        $userId = Auth::user()->id;
        $filter = $request->input('filter', 'today'); // Default to today if no filter specified

        $query = payments::whereHas('contract', function ($query) use ($userId) {
            $query->where('sales_id', $userId);
        })->with(['customer', 'contract']);

        switch ($filter) {
            case 'today':
                $query->whereDate('due_date', Carbon::today());
                break;
            case 'month':
                $query->whereMonth('due_date', Carbon::now()->month)
                    ->whereYear('due_date', Carbon::now()->year);
                break;
            case 'year':
                $query->whereYear('due_date', Carbon::now()->year);
                break;
            case 'all':
                // No additional filter needed
                break;
        }

        $payments = $query->orderBy('due_date', 'asc')->get();

        return view('managers.sales.ToDoList', compact('payments', 'filter'));
    }

    public function generatePDF(Request $request)
    {
        // Get the authenticated sales user
        $salesId = Auth::user()->id;

        // Get the report type and date range
        $reportType = $request->input('report_type');
        $startDate = null;
        $endDate = null;

        // Set date range based on report type
        switch ($reportType) {
            case 'daily':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'quarterly':
                $startDate = Carbon::now()->startOfQuarter();
                $endDate = Carbon::now()->endOfQuarter();
                break;
            case 'annual':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
                $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                break;
            default:
                // If no report type specified, show all time
                $startDate = Carbon::minValue();
                $endDate = Carbon::now();
        }

        // Get all contracts for the sales person within the date range
        $contracts = contracts::where('sales_id', $salesId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['customer', 'type', 'payments'])
            ->get()
            ->groupBy('contract_status');

        // Get all payments grouped by status within the date range
        $payments = payments::whereHas('contract', function ($query) use ($salesId) {
            $query->where('sales_id', $salesId);
        })
            ->whereBetween('due_date', [$startDate, $endDate])
            ->with('contract')
            ->get()
            ->groupBy('payment_status');

        // Calculate financial summaries
        $financialSummary = [
            'total_contract_value' => $contracts->flatten()->sum('contract_price'),
            'total_paid' => $payments->flatten()->where('payment_status', 'paid')->sum('payment_amount'),
            'total_pending' => $payments->flatten()->where('payment_status', 'unpaid')->sum('payment_amount'),
            'total_overdue' => $payments->flatten()->where('payment_status', 'overdue')->sum('payment_amount')
        ];

        // Contract statistics
        $contractStats = [
            'total_contracts' => $contracts->flatten()->count(),
            'active_contracts' => $contracts->get('approved', collect())->count(),
            'pending_contracts' => $contracts->get('pending', collect())->count(),
            'completed_contracts' => $contracts->get('completed', collect())->count(),
            'cancelled_contracts' => $contracts->get('cancelled', collect())->count()
        ];

        // Add period information
        $periodInfo = [
            'report_type' => $reportType,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'period_label' => $this->getPeriodLabel($reportType, $startDate, $endDate)
        ];

        // Initialize mPDF
        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        // Generate PDF content
        $html = view('pdf_templates.sales_report_pdf', compact(
            'contracts',
            'payments',
            'financialSummary',
            'contractStats',
            'periodInfo'
        ))->render();

        // Write PDF content
        $mpdf->WriteHTML($html);

        // Return the PDF as a download
        return $mpdf->Output($periodInfo['period_label'] . '.pdf', 'D');
    }

    private function getContractsForPeriod($startDate, $endDate)
    {
        $salesId = Auth::user()->id;
        return contracts::where('sales_id', $salesId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['customer', 'type', 'payments'])
            ->get()
            ->groupBy('contract_status');
    }

    private function getPaymentsForPeriod($startDate, $endDate)
    {
        $salesId = Auth::user()->id;
        return payments::whereHas('contract', function ($query) use ($salesId) {
            $query->where('sales_id', $salesId);
        })
            ->whereBetween('due_date', [$startDate, $endDate])
            ->with('contract')
            ->get()
            ->groupBy('payment_status');
    }

    private function calculateFinancialSummary($contracts, $payments)
    {
        return [
            'total_contract_value' => $contracts->flatten()->sum('contract_price'),
            'total_paid' => $payments->flatten()->where('payment_status', 'paid')->sum('payment_amount'),
            'total_pending' => $payments->flatten()->where('payment_status', 'unpaid')->sum('payment_amount'),
            'total_overdue' => $payments->flatten()->where('payment_status', 'overdue')->sum('payment_amount')
        ];
    }

    private function calculateContractStats($contracts)
    {
        return [
            'total_contracts' => $contracts->flatten()->count(),
            'active_contracts' => $contracts->get('approved', collect())->count(),
            'pending_contracts' => $contracts->get('pending', collect())->count(),
            'completed_contracts' => $contracts->get('completed', collect())->count(),
            'cancelled_contracts' => $contracts->get('cancelled', collect())->count()
        ];
    }

    // chnage status of the cancled or not approved contracts
    public function return_contract(Request $request)
    {
        $contract = contracts::findOrFail($request->id);
        $contract->contract_status = $request->status;
        $contract->rejection_reason = null;
        $contract->save();
        return redirect()->back()->with('success', 'Contract status changed successfully');
    }
}
