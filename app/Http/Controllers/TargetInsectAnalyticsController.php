<?php

namespace App\Http\Controllers;

use App\Models\contracts;
use App\Models\TargetInsect;
use App\Models\VisitReport;
use App\Models\branchs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TargetInsectAnalyticsController extends Controller
{
    /**
     * Display the insect analytics dashboard
     */
    public function index()
    {
        // Get all active target insects
        $targetInsects = TargetInsect::where('active', true)->get();
        
        // Get overall statistics for all insects
        $insectStats = $this->getOverallInsectStatistics();
        
        // Get monthly trend data for the past 12 months
        $monthlyTrends = $this->getMonthlyTrends();
        
        return view('managers.technical.target-insects.analytics.index', compact('targetInsects', 'insectStats', 'monthlyTrends'));
    }
    
    /**
     * Display analytics for a specific contract
     */
    public function contractAnalytics($contractId)
    {
        $contract = contracts::with(['customer', 'visitSchedules.report', 'branchs'])->findOrFail($contractId);
        
        // Check authorization based on user role
        if (!$this->canViewContractAnalytics($contract)) {
            return redirect()->back()->with('error', 'You are not authorized to view this contract\'s analytics.');
        }
        
        // Get all active target insects
        $targetInsects = TargetInsect::where('active', true)->get();
        
        // Get insect statistics for this specific contract
        $insectStats = $this->getContractInsectStatistics($contract);
        
        // Get visit-by-visit data
        $visitData = $this->getVisitInsectData($contract);
        
        return view('shared.analytics.contract-insects', compact('contract', 'targetInsects', 'insectStats', 'visitData'));
    }
    
    /**
     * Display analytics for a specific branch within a contract
     */
    public function branchAnalytics($contractId, $branchId)
    {
        $contract = contracts::with(['customer', 'visitSchedules.report', 'branchs'])->findOrFail($contractId);
        $branch = branchs::findOrFail($branchId);
        
        // Check if branch belongs to this contract
        if ($branch->contracts_id != $contract->id) {
            return redirect()->back()->with('error', 'This branch does not belong to the specified contract.');
        }
        
        // Check authorization based on user role
        if (!$this->canViewContractAnalytics($contract)) {
            return redirect()->back()->with('error', 'You are not authorized to view this contract\'s analytics.');
        }
        
        // Get all active target insects
        $targetInsects = TargetInsect::where('active', true)->get();
        
        // Get insect statistics for this specific branch
        $insectStats = $this->getBranchInsectStatistics($contract, $branch);
        
        // Get visit-by-visit data for this branch
        $visitData = $this->getBranchVisitInsectData($contract, $branch);
        
        return view('shared.analytics.branch-insects', compact('contract', 'branch', 'targetInsects', 'insectStats', 'visitData'));
    }
    
    /**
     * Display the branch selection page for a contract with multiple branches
     */
    public function contractBranchSelection($contractId)
    {
        $contract = contracts::with(['customer', 'branchs'])->findOrFail($contractId);
        
        // Check authorization based on user role
        if (!$this->canViewContractAnalytics($contract)) {
            return redirect()->back()->with('error', 'You are not authorized to view this contract\'s analytics.');
        }
        
        // If contract has only one branch, redirect directly to branch analytics
        if ($contract->branchs->count() == 1) {
            return redirect()->route('analytics.branch', ['contractId' => $contractId, 'branchId' => $contract->branchs->first()->id]);
        }
        
        return view('shared.analytics.branch-selection', compact('contract'));
    }
    
    /**
     * Check if the current user can view the contract analytics
     */
    private function canViewContractAnalytics($contract)
    {
        $user = Auth::user();
        
        // Technical users can view all contracts
        if ($user->role === 'technical') {
            return true;
        }
        
        // Team leaders can view contracts assigned to their team
        if ($user->role === 'team_leader') {
            $teamId = DB::table('teams')->where('team_leader_id', $user->id)->value('id');
            if ($teamId) {
                $hasVisit = DB::table('visit_schedules')
                    ->where('contract_id', $contract->id)
                    ->where('team_id', $teamId)
                    ->exists();
                return $hasVisit;
            }
            return false;
        }
        
        // Sales users can view their own contracts
        if ($user->role === 'sales') {
            return $contract->sales_id === $user->id;
        }
        
        // Sales managers can view all contracts
        if ($user->role === 'sales_manager') {
            return true;
        }
        
        // Clients can view their own contracts
        if ($user->role === 'client') {
            return $contract->customer_id === $user->id;
        }
        
        return false;
    }
    
    /**
     * Get overall insect statistics across all contracts
     */
    private function getOverallInsectStatistics()
    {
        $reports = VisitReport::all();
        $insectCounts = [];
        $insectQuantities = [];
        
        foreach ($reports as $report) {
            // Ensure we have a string before json_decode
            $targetInsectsJson = $report->target_insects;
            if (!is_string($targetInsectsJson)) {
                $targetInsectsJson = '[]';
            }
            $targetInsects = json_decode($targetInsectsJson, true) ?: [];
            
            // Get insect quantities if available
            $quantities = is_array($report->insect_quantities) ? $report->insect_quantities : [];
            
            if (is_array($targetInsects)) {
                foreach ($targetInsects as $insect) {
                    if (!isset($insectCounts[$insect])) {
                        $insectCounts[$insect] = 0;
                        $insectQuantities[$insect] = 0;
                    }
                    $insectCounts[$insect]++;
                    
                    // Add quantities if available
                    if (is_array($quantities) && isset($quantities[$insect])) {
                        $insectQuantities[$insect] += (int)$quantities[$insect];
                    } else {
                        // If no quantity data, use a default of 1 per report
                        $insectQuantities[$insect] += 1;
                    }
                }
            }
        }
        
        // Sort by frequency (highest first)
        arsort($insectCounts);
        
        // Get insect names for the values
        $result = [];
        foreach ($insectCounts as $value => $count) {
            $insect = TargetInsect::where('value', $value)->first();
            $name = $insect ? $insect->name : $value;
            $result[] = [
                'name' => $name,
                'value' => $value,
                'count' => $count,
                'quantity' => $insectQuantities[$value] ?? $count, // Fallback to count if no quantity
                'avg_per_report' => $count > 0 ? round(($insectQuantities[$value] ?? $count) / $count, 1) : 0,
                'percentage' => $reports->count() > 0 ? round(($count / $reports->count()) * 100, 1) : 0
            ];
        }
        
        return $result;
    }
    
    /**
     * Get insect statistics for a specific contract
     */
    private function getContractInsectStatistics($contract)
    {
        $reports = collect();
        foreach ($contract->visitSchedules as $visit) {
            if ($visit->report) {
                $reports->push($visit->report);
            }
        }
        
        $insectCounts = [];
        $insectQuantities = [];
        
        foreach ($reports as $report) {
            // Ensure we have a string before json_decode
            $targetInsectsJson = $report->target_insects;
            if (!is_string($targetInsectsJson)) {
                $targetInsectsJson = '[]';
            }
            $targetInsects = json_decode($targetInsectsJson, true) ?: [];
            
            // Get insect quantities if available
            $quantities = is_array($report->insect_quantities) ? $report->insect_quantities : [];
            
            if (is_array($targetInsects)) {
                foreach ($targetInsects as $insect) {
                    if (!isset($insectCounts[$insect])) {
                        $insectCounts[$insect] = 0;
                        $insectQuantities[$insect] = 0;
                    }
                    $insectCounts[$insect]++;
                    
                    // Add quantities if available
                    if (is_array($quantities) && isset($quantities[$insect])) {
                        $insectQuantities[$insect] += (int)$quantities[$insect];
                    } else {
                        // If no quantity data, use a default of 1 per report
                        $insectQuantities[$insect] += 1;
                    }
                }
            }
        }
        
        // Sort by frequency (highest first)
        arsort($insectCounts);
        
        // Get insect names for the values
        $result = [];
        foreach ($insectCounts as $value => $count) {
            $insect = TargetInsect::where('value', $value)->first();
            $name = $insect ? $insect->name : $value;
            $result[] = [
                'name' => $name,
                'value' => $value,
                'count' => $count,
                'quantity' => $insectQuantities[$value] ?? $count, // Fallback to count if no quantity
                'avg_per_report' => $count > 0 ? round(($insectQuantities[$value] ?? $count) / $count, 1) : 0,
                'percentage' => $reports->count() > 0 ? round(($count / $reports->count()) * 100, 1) : 0
            ];
        }
        
        return $result;
    }
    
    /**
     * Get visit-by-visit insect data for a contract
     */
    private function getVisitInsectData($contract)
    {
        $visitData = [];
        
        foreach ($contract->visitSchedules as $visit) {
            if ($visit->report) {
                // Ensure we have a string before json_decode
                $insectsJson = $visit->report->target_insects;
                if (!is_string($insectsJson)) {
                    $insectsJson = '[]';
                }
                $insects = json_decode($insectsJson, true) ?: [];
                
                // Get insect quantities if available
                $insectQuantities = is_array($visit->report->insect_quantities) ? 
                    $visit->report->insect_quantities : [];
                
                $visitData[] = [
                    'visit_id' => $visit->id,
                    'visit_date' => $visit->visit_date,
                    'target_insects' => is_array($insects) ? $insects : [],
                    'insect_quantities' => $insectQuantities,
                    'recommendations' => $visit->report->recommendations
                ];
            }
        }
        
        // Sort by visit date (newest first)
        usort($visitData, function($a, $b) {
            return strtotime($b['visit_date']) - strtotime($a['visit_date']);
        });
        
        return $visitData;
    }
    
    /**
     * Get insect statistics for a specific branch
     */
    private function getBranchInsectStatistics($contract, $branch)
    {
        $reports = collect();
        foreach ($contract->visitSchedules as $visit) {
            // Only include reports for the specified branch
            if ($visit->branch_id == $branch->id && $visit->report) {
                $reports->push($visit->report);
            }
        }
        
        $insectCounts = [];
        $insectQuantities = [];
        
        foreach ($reports as $report) {
            // Ensure we have a string before json_decode
            $targetInsectsJson = $report->target_insects;
            if (!is_string($targetInsectsJson)) {
                $targetInsectsJson = '[]';
            }
            $targetInsects = json_decode($targetInsectsJson, true) ?: [];
            
            // Get insect quantities if available
            $quantities = is_array($report->insect_quantities) ? $report->insect_quantities : [];
            
            if (is_array($targetInsects)) {
                foreach ($targetInsects as $insect) {
                    if (!isset($insectCounts[$insect])) {
                        $insectCounts[$insect] = 0;
                        $insectQuantities[$insect] = 0;
                    }
                    $insectCounts[$insect]++;
                    
                    // Add quantities if available
                    if (is_array($quantities) && isset($quantities[$insect])) {
                        $insectQuantities[$insect] += (int)$quantities[$insect];
                    } else {
                        // If no quantity data, use a default of 1 per report
                        $insectQuantities[$insect] += 1;
                    }
                }
            }
        }
        
        // Sort by frequency (highest first)
        arsort($insectCounts);
        
        // Get insect names for the values
        $result = [];
        foreach ($insectCounts as $value => $count) {
            $insect = TargetInsect::where('value', $value)->first();
            $name = $insect ? $insect->name : $value;
            $result[] = [
                'name' => $name,
                'value' => $value,
                'count' => $count,
                'quantity' => $insectQuantities[$value] ?? $count, // Fallback to count if no quantity
                'avg_per_report' => $count > 0 ? round(($insectQuantities[$value] ?? $count) / $count, 1) : 0,
                'percentage' => $reports->count() > 0 ? round(($count / $reports->count()) * 100, 1) : 0
            ];
        }
        
        return $result;
    }
    
    /**
     * Get visit-by-visit insect data for a specific branch
     */
    private function getBranchVisitInsectData($contract, $branch)
    {
        $visitData = [];
        
        foreach ($contract->visitSchedules as $visit) {
            // Only include visits for the specified branch
            if ($visit->branch_id == $branch->id && $visit->report) {
                // Ensure we have a string before json_decode
                $insectsJson = $visit->report->target_insects;
                if (!is_string($insectsJson)) {
                    $insectsJson = '[]';
                }
                $insects = json_decode($insectsJson, true) ?: [];
                
                // Get insect quantities if available
                $insectQuantities = is_array($visit->report->insect_quantities) ? 
                    $visit->report->insect_quantities : [];
                
                $visitData[] = [
                    'visit_id' => $visit->id,
                    'visit_date' => $visit->visit_date,
                    'target_insects' => is_array($insects) ? $insects : [],
                    'insect_quantities' => $insectQuantities,
                    'recommendations' => $visit->report->recommendations
                ];
            }
        }
        
        // Sort by visit date (newest first)
        usort($visitData, function($a, $b) {
            return strtotime($b['visit_date']) - strtotime($a['visit_date']);
        });
        
        return $visitData;
    }
    
    /**
     * Get monthly trends for the past 12 months
     */
    private function getMonthlyTrends()
    {
        $trends = [];
        $months = [];
        $quantities = [];
        
        // Generate the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[$month] = [
                'label' => date('M Y', strtotime("-$i months")),
                'insects' => []
            ];
            $quantities[$month] = [
                'label' => date('M Y', strtotime("-$i months")),
                'insects' => []
            ];
        }
        
        // Get all reports grouped by month
        $reports = VisitReport::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            'target_insects',
            'insect_quantities'
        )->get();
        
        // Count insects by month
        foreach ($reports as $report) {
            $month = $report->month;
            if (!isset($months[$month])) {
                continue; // Skip if not in our 12-month window
            }
            
            // Ensure we have a string before json_decode
            $insectsJson = $report->target_insects;
            if (!is_string($insectsJson)) {
                $insectsJson = '[]';
            }
            $insects = json_decode($insectsJson, true) ?: [];
            
            // Get insect quantities
            $insectQuantities = is_array($report->insect_quantities) ? $report->insect_quantities : [];
            
            if (is_array($insects)) {
                foreach ($insects as $insect) {
                    if (!isset($months[$month]['insects'][$insect])) {
                        $months[$month]['insects'][$insect] = 0;
                        $quantities[$month]['insects'][$insect] = 0;
                    }
                    $months[$month]['insects'][$insect]++;
                    
                    // Add quantities if available
                    if (is_array($insectQuantities) && isset($insectQuantities[$insect])) {
                        $quantities[$month]['insects'][$insect] += (int)$insectQuantities[$insect];
                    } else {
                        // If no quantity data, use a default of 1 per report
                        $quantities[$month]['insects'][$insect] += 1;
                    }
                }
            }
        }
        
        // Format the data for the chart
        $insectTypes = TargetInsect::where('active', true)->pluck('name', 'value')->toArray();
        
        foreach ($insectTypes as $value => $name) {
            $occurrenceData = [];
            $quantityData = [];
            
            foreach ($months as $month => $info) {
                $occurrenceData[] = [
                    'month' => $info['label'],
                    'count' => $info['insects'][$value] ?? 0
                ];
                
                $quantityData[] = [
                    'month' => $info['label'],
                    'count' => $quantities[$month]['insects'][$value] ?? 0
                ];
            }
            
            $trends[$value] = [
                'name' => $name,
                'data' => $occurrenceData,
                'quantity_data' => $quantityData
            ];
        }
        
        return $trends;
    }
}
