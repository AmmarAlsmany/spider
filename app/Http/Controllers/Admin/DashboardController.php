<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\contracts;
use App\Models\payments;
use App\Models\Tiket;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Tickets Statistics
        $totalTickets = Tiket::count();
        $openTickets = Tiket::where('status', 'open')->count();
        $recentTickets = Tiket::latest()->take(5)->get();

        // Contracts Statistics
        $totalContracts = contracts::count();
        $activeContracts = contracts::where('contract_status', 'active')    
            ->count();
        $recentContracts = contracts::latest()->take(5)->get();

        // Financial Statistics
        $monthlyRevenue = payments::whereMonth('created_at', Carbon::now()->month)
            ->sum('payment_amount');
        $yearlyRevenue = payments::whereYear('created_at', Carbon::now()->year)
            ->sum('payment_amount');

        return view('admin.dashboard', compact(
            'totalTickets',
            'openTickets',
            'recentTickets',
            'totalContracts',
            'activeContracts',
            'recentContracts',
            'monthlyRevenue',
            'yearlyRevenue'
        ));
    }
}
