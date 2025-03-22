<?php

namespace App\Http\Controllers;

use App\Models\contracts;
use App\Models\TargetInsect;
use App\Models\VisitReport;
use Illuminate\Http\Request;
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
        $contract = contracts::with(['customer', 'visitSchedules.report'])->findOrFail($contractId);
        
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
        
        foreach ($reports as $report) {
            // Ensure we have a string before json_decode
            $targetInsectsJson = $report->target_insects;
            if (!is_string($targetInsectsJson)) {
                $targetInsectsJson = '[]';
            }
            $targetInsects = json_decode($targetInsectsJson, true) ?: [];
            
            if (is_array($targetInsects)) {
                foreach ($targetInsects as $insect) {
                    if (!isset($insectCounts[$insect])) {
                        $insectCounts[$insect] = 0;
                    }
                    $insectCounts[$insect]++;
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
        
        foreach ($reports as $report) {
            // Ensure we have a string before json_decode
            $targetInsectsJson = $report->target_insects;
            if (!is_string($targetInsectsJson)) {
                $targetInsectsJson = '[]';
            }
            $targetInsects = json_decode($targetInsectsJson, true) ?: [];
            
            if (is_array($targetInsects)) {
                foreach ($targetInsects as $insect) {
                    if (!isset($insectCounts[$insect])) {
                        $insectCounts[$insect] = 0;
                    }
                    $insectCounts[$insect]++;
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
                
                $visitData[] = [
                    'visit_id' => $visit->id,
                    'visit_date' => $visit->visit_date,
                    'target_insects' => is_array($insects) ? $insects : [],
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
        
        // Generate the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[$month] = [
                'label' => date('M Y', strtotime("-$i months")),
                'insects' => []
            ];
        }
        
        // Get all reports grouped by month
        $reports = VisitReport::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            'target_insects'
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
            
            if (is_array($insects)) {
                foreach ($insects as $insect) {
                    if (!isset($months[$month]['insects'][$insect])) {
                        $months[$month]['insects'][$insect] = 0;
                    }
                    $months[$month]['insects'][$insect]++;
                }
            }
        }
        
        // Format the data for the chart
        $insectTypes = TargetInsect::where('active', true)->pluck('name', 'value')->toArray();
        
        foreach ($insectTypes as $value => $name) {
            $data = [];
            foreach ($months as $month => $info) {
                $data[] = [
                    'month' => $info['label'],
                    'count' => $info['insects'][$value] ?? 0
                ];
            }
            
            $trends[$value] = [
                'name' => $name,
                'data' => $data
            ];
        }
        
        return $trends;
    }
}
