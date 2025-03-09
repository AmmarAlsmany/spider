<?php

namespace App\Http\Controllers;

use App\Models\Pesticide;
use App\Models\Team;
use App\Models\VisitReport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PesticideAnalyticsController extends Controller
{
    /**
     * Display the pesticide consumption analytics dashboard
     */
    public function index(Request $request)
    {
        // Get all teams for the filter
        $teams = Team::orderBy('name')->get();
        
        // Get all pesticides for the filter
        $pesticides = Pesticide::where('active', true)->orderBy('name')->get();
        
        // Default to last 30 days if no date range is specified
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get selected team and pesticide filters
        $selectedTeam = $request->input('team_id');
        $selectedPesticide = $request->input('pesticide_id');
        
        // Get the consumption data
        $consumptionData = $this->getPesticideConsumptionData($startDate, $endDate, $selectedTeam, $selectedPesticide);
        
        // Get summary statistics
        $summaryStats = $this->getSummaryStatistics($consumptionData);
        
        return view('managers.technical.pesticides.analytics', compact(
            'teams', 
            'pesticides', 
            'startDate', 
            'endDate', 
            'selectedTeam', 
            'selectedPesticide', 
            'consumptionData',
            'summaryStats'
        ));
    }
    
    /**
     * Get pesticide consumption data based on filters
     */
    private function getPesticideConsumptionData($startDate, $endDate, $teamId = null, $pesticideSlug = null)
    {
        // Get all visit reports within the date range
        $query = VisitReport::with(['visit.team'])
            ->whereHas('visit', function ($q) use ($startDate, $endDate, $teamId) {
                $q->whereDate('visit_date', '>=', $startDate)
                  ->whereDate('visit_date', '<=', $endDate);
                
                if ($teamId) {
                    $q->where('team_id', $teamId);
                }
            });
            
        $reports = $query->get();
        
        // Process the reports to extract pesticide consumption data
        $consumptionData = [];
        
        foreach ($reports as $report) {
            if (!$report->visit || !$report->visit->team) {
                continue; // Skip if visit or team is missing
            }
            
            $team = $report->visit->team;
            $visitDate = Carbon::parse($report->visit->visit_date)->format('Y-m-d');
            
            // Safely decode JSON strings to arrays
            $pesticidesUsed = [];
            if (!empty($report->pesticides_used) && is_string($report->pesticides_used)) {
                $decoded = json_decode($report->pesticides_used, true);
                if (is_array($decoded)) {
                    $pesticidesUsed = $decoded;
                }
            }
            
            $pesticideQuantities = [];
            if (!empty($report->pesticide_quantities) && is_string($report->pesticide_quantities)) {
                $decoded = json_decode($report->pesticide_quantities, true);
                if (is_array($decoded)) {
                    $pesticideQuantities = $decoded;
                }
            }
            
            foreach ($pesticidesUsed as $pesticide) {
                // Skip if we're filtering by pesticide and this isn't the one we want
                if ($pesticideSlug && $pesticide !== $pesticideSlug) {
                    continue;
                }
                
                // Get the pesticide details
                $pesticideModel = Pesticide::where('slug', $pesticide)->first();
                if (!$pesticideModel) {
                    continue; // Skip if pesticide not found
                }
                
                // Get the quantity and unit
                $quantity = isset($pesticideQuantities[$pesticide]) && isset($pesticideQuantities[$pesticide]['quantity']) 
                    ? $pesticideQuantities[$pesticide]['quantity'] 
                    : 0;
                $unit = isset($pesticideQuantities[$pesticide]) && isset($pesticideQuantities[$pesticide]['unit']) 
                    ? $pesticideQuantities[$pesticide]['unit'] 
                    : 'g';
                
                // Add to consumption data
                if (!isset($consumptionData[$team->id])) {
                    $consumptionData[$team->id] = [
                        'team_name' => $team->name,
                        'pesticides' => [],
                        'total_quantity' => 0,
                        'visit_count' => 0
                    ];
                }
                
                if (!isset($consumptionData[$team->id]['pesticides'][$pesticide])) {
                    $consumptionData[$team->id]['pesticides'][$pesticide] = [
                        'name' => $pesticideModel->name,
                        'total_quantity' => 0,
                        'unit_counts' => ['g' => 0, 'ml' => 0],
                        'dates' => []
                    ];
                }
                
                // Increment the total quantity
                $consumptionData[$team->id]['pesticides'][$pesticide]['total_quantity'] += $quantity;
                $consumptionData[$team->id]['pesticides'][$pesticide]['unit_counts'][$unit] += $quantity;
                $consumptionData[$team->id]['total_quantity'] += $quantity;
                
                // Add date-specific data
                if (!isset($consumptionData[$team->id]['pesticides'][$pesticide]['dates'][$visitDate])) {
                    $consumptionData[$team->id]['pesticides'][$pesticide]['dates'][$visitDate] = 0;
                }
                $consumptionData[$team->id]['pesticides'][$pesticide]['dates'][$visitDate] += $quantity;
            }
            
            // Increment visit count
            if (isset($consumptionData[$team->id])) {
                $consumptionData[$team->id]['visit_count']++;
            }
        }
        
        return $consumptionData;
    }
    
    /**
     * Calculate summary statistics from consumption data
     */
    private function getSummaryStatistics($consumptionData)
    {
        $stats = [
            'total_consumption' => 0,
            'total_visits' => 0,
            'most_used_pesticide' => null,
            'most_used_quantity' => 0,
            'most_efficient_team' => null,
            'most_efficient_ratio' => 0,
            'pesticide_usage' => [],
            'team_usage' => []
        ];
        
        // Calculate totals and find most used pesticide
        foreach ($consumptionData as $teamId => $teamData) {
            $stats['total_visits'] += $teamData['visit_count'];
            $stats['team_usage'][$teamId] = [
                'team_name' => $teamData['team_name'],
                'total_quantity' => $teamData['total_quantity'],
                'visit_count' => $teamData['visit_count']
            ];
            
            foreach ($teamData['pesticides'] as $pesticideSlug => $pesticideData) {
                if (!isset($stats['pesticide_usage'][$pesticideSlug])) {
                    $stats['pesticide_usage'][$pesticideSlug] = [
                        'name' => $pesticideData['name'],
                        'total_quantity' => 0,
                        'unit_counts' => ['g' => 0, 'ml' => 0]
                    ];
                }
                
                $stats['pesticide_usage'][$pesticideSlug]['total_quantity'] += $pesticideData['total_quantity'];
                $stats['pesticide_usage'][$pesticideSlug]['unit_counts']['g'] += $pesticideData['unit_counts']['g'];
                $stats['pesticide_usage'][$pesticideSlug]['unit_counts']['ml'] += $pesticideData['unit_counts']['ml'];
                
                $stats['total_consumption'] += $pesticideData['total_quantity'];
                
                // Check if this is the most used pesticide
                if ($stats['pesticide_usage'][$pesticideSlug]['total_quantity'] > $stats['most_used_quantity']) {
                    $stats['most_used_pesticide'] = $pesticideData['name'];
                    $stats['most_used_quantity'] = $stats['pesticide_usage'][$pesticideSlug]['total_quantity'];
                }
            }
            
            // Calculate efficiency ratio (visits per unit of pesticide)
            if ($teamData['total_quantity'] > 0) {
                $ratio = $teamData['visit_count'] / $teamData['total_quantity'];
                if ($ratio > $stats['most_efficient_ratio']) {
                    $stats['most_efficient_team'] = $teamData['team_name'];
                    $stats['most_efficient_ratio'] = $ratio;
                }
            }
        }
        
        return $stats;
    }
    
    /**
     * Generate a detailed report for a specific team
     */
    public function teamReport($teamId, Request $request)
    {
        $team = Team::findOrFail($teamId);
        
        // Default to last 30 days if no date range is specified
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get the consumption data for this team
        $consumptionData = $this->getPesticideConsumptionData($startDate, $endDate, $teamId);
        
        // Get the team's consumption data
        $teamData = $consumptionData[$teamId] ?? null;
        
        if (!$teamData) {
            return redirect()->route('technical.pesticides.analytics')
                ->with('error', 'No consumption data found for this team in the selected date range.');
        }
        
        // Get all visit reports for this team in the date range
        $reports = VisitReport::with(['visit', 'createdBy'])
            ->whereHas('visit', function ($q) use ($teamId, $startDate, $endDate) {
                $q->where('team_id', $teamId)
                  ->whereDate('visit_date', '>=', $startDate)
                  ->whereDate('visit_date', '<=', $endDate);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('managers.technical.pesticides.team-report', compact(
            'team',
            'startDate',
            'endDate',
            'teamData',
            'reports'
        ));
    }
    
    /**
     * Generate a detailed report for a specific pesticide
     */
    public function pesticideReport($pesticideSlug, Request $request)
    {
        $pesticide = Pesticide::where('slug', $pesticideSlug)->firstOrFail();
        
        // Default to last 30 days if no date range is specified
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get the consumption data for this pesticide
        $consumptionData = $this->getPesticideConsumptionData($startDate, $endDate, null, $pesticideSlug);
        
        // Get all teams that used this pesticide
        $teamUsage = [];
        $totalUsage = 0;
        
        foreach ($consumptionData as $teamId => $teamData) {
            if (isset($teamData['pesticides'][$pesticideSlug])) {
                $pesticideData = $teamData['pesticides'][$pesticideSlug];
                $teamUsage[$teamId] = [
                    'team_name' => $teamData['team_name'],
                    'total_quantity' => $pesticideData['total_quantity'],
                    'unit_counts' => $pesticideData['unit_counts'],
                    'dates' => $pesticideData['dates']
                ];
                $totalUsage += $pesticideData['total_quantity'];
            }
        }
        
        // Get all visit reports that used this pesticide
        $reports = VisitReport::with(['visit.team', 'createdBy'])
            ->whereHas('visit', function ($q) use ($startDate, $endDate) {
                $q->whereDate('visit_date', '>=', $startDate)
                  ->whereDate('visit_date', '<=', $endDate);
            })
            ->whereRaw("JSON_CONTAINS(pesticides_used, '\"$pesticideSlug\"')")
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('managers.technical.pesticides.pesticide-report', compact(
            'pesticide',
            'startDate',
            'endDate',
            'teamUsage',
            'totalUsage',
            'reports'
        ));
    }
}
