<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\VisitSchedule;
use App\Models\VisitReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TeamKpiController extends Controller
{
    /**
     * Display the team KPI dashboard for technical managers
     */
    public function index(Request $request)
    {
        // Only technical managers can access this
        if (Auth::user()->role !== 'technical') {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Get all teams
        $teams = Team::with(['leader', 'members'])->get();
        
        // Get selected team if provided
        $selectedTeam = null;
        if ($request->filled('team_id')) {
            $selectedTeam = Team::with(['leader', 'members'])->find($request->team_id);
        }
        
        // Get date range
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        // Calculate KPIs for all teams or selected team
        $kpiData = $this->calculateTeamKpis($startDate, $endDate, $selectedTeam ? $selectedTeam->id : null);
        
        return view('managers.technical.kpi.index', compact('teams', 'selectedTeam', 'startDate', 'endDate', 'kpiData'));
    }
    
    /**
     * Display KPIs for a specific team
     */
    public function teamDetail($teamId, Request $request)
    {
        // Only technical managers can access this
        if (Auth::user()->role !== 'technical') {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        
        $team = Team::with(['leader', 'members'])->findOrFail($teamId);
        
        // Get date range
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        // Calculate KPIs for the team
        $kpiData = $this->calculateTeamKpis($startDate, $endDate, $teamId);
        
        // Get detailed visit data for the team
        $visitData = $this->getTeamVisitData($teamId, $startDate, $endDate);
        
        // Get detailed report data for the team
        $reportData = $this->getTeamReportData($teamId, $startDate, $endDate);
        
        return view('managers.technical.kpi.team_detail', compact('team', 'startDate', 'endDate', 'kpiData', 'visitData', 'reportData'));
    }
    
    /**
     * Calculate KPIs for teams
     */
    private function calculateTeamKpis($startDate, $endDate, $teamId = null)
    {
        $query = Team::with(['leader', 'members']);
        
        if ($teamId) {
            $query->where('id', $teamId);
        }
        
        $teams = $query->get();
        $kpiData = [];
        
        foreach ($teams as $team) {
            // Get all visits for the team in the date range
            $allVisits = VisitSchedule::where('team_id', $team->id)
                ->whereBetween('visit_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();
            
            // Get completed visits
            $completedVisits = $allVisits->where('status', 'completed')->count();
            
            // Get pending visits
            $pendingVisits = $allVisits->where('status', '!=', 'completed')->count();
            
            // Get reports created
            $reportsCreated = VisitReport::whereIn('visit_id', $allVisits->pluck('id'))->count();
            
            // Calculate completion rate
            $completionRate = $allVisits->count() > 0 ? round(($completedVisits / $allVisits->count()) * 100, 2) : 0;
            
            // Calculate report submission rate
            $reportRate = $completedVisits > 0 ? round(($reportsCreated / $completedVisits) * 100, 2) : 0;
            
            // Calculate average time between visit completion and report submission
            $avgReportTime = 0;
            $reportsWithTime = 0;
            
            foreach ($allVisits->where('status', 'completed') as $visit) {
                $report = VisitReport::where('visit_id', $visit->id)->first();
                if ($report) {
                    $visitDate = Carbon::parse($visit->updated_at);
                    $reportDate = Carbon::parse($report->created_at);
                    $diffInHours = $visitDate->diffInHours($reportDate);
                    $avgReportTime += $diffInHours;
                    $reportsWithTime++;
                }
            }
            
            $avgReportTime = $reportsWithTime > 0 ? round($avgReportTime / $reportsWithTime, 2) : 0;
            
            // Calculate average visit duration
            $avgVisitDuration = 0;
            $visitsWithDuration = 0;
            
            foreach ($allVisits as $visit) {
                $report = VisitReport::where('visit_id', $visit->id)->first();
                if ($report && $report->time_in && $report->time_out) {
                    $timeIn = Carbon::parse($report->time_in);
                    $timeOut = Carbon::parse($report->time_out);
                    $diffInMinutes = $timeIn->diffInMinutes($timeOut);
                    $avgVisitDuration += $diffInMinutes;
                    $visitsWithDuration++;
                }
            }
            
            $avgVisitDuration = $visitsWithDuration > 0 ? round($avgVisitDuration / $visitsWithDuration, 2) : 0;
            
            // Calculate customer satisfaction
            $totalSatisfaction = 0;
            $satisfactionCount = 0;
            
            foreach ($allVisits as $visit) {
                $report = VisitReport::where('visit_id', $visit->id)->first();
                if ($report && $report->customer_satisfaction) {
                    $totalSatisfaction += $report->customer_satisfaction;
                    $satisfactionCount++;
                }
            }
            
            $avgSatisfaction = $satisfactionCount > 0 ? round($totalSatisfaction / $satisfactionCount, 2) : 0;
            
            // Get most common target insects
            $targetInsects = [];
            foreach ($allVisits as $visit) {
                $report = VisitReport::where('visit_id', $visit->id)->first();
                if ($report && $report->target_insects) {
                    // Ensure target_insects is an array
                    $insects = is_string($report->target_insects) ? json_decode($report->target_insects, true) : $report->target_insects;
                    
                    if (is_array($insects)) {
                        foreach ($insects as $insect) {
                            if (isset($targetInsects[$insect])) {
                                $targetInsects[$insect]++;
                            } else {
                                $targetInsects[$insect] = 1;
                            }
                        }
                    }
                }
            }
            
            arsort($targetInsects);
            $topInsects = array_slice($targetInsects, 0, 3, true);
            
            // Get most common pesticides used
            $pesticidesUsed = [];
            foreach ($allVisits as $visit) {
                $report = VisitReport::where('visit_id', $visit->id)->first();
                if ($report && $report->pesticides_used) {
                    // Ensure pesticides_used is an array
                    $pesticides = is_string($report->pesticides_used) ? json_decode($report->pesticides_used, true) : $report->pesticides_used;
                    
                    if (is_array($pesticides)) {
                        foreach ($pesticides as $pesticide) {
                            if (isset($pesticidesUsed[$pesticide])) {
                                $pesticidesUsed[$pesticide]++;
                            } else {
                                $pesticidesUsed[$pesticide] = 1;
                            }
                        }
                    }
                }
            }
            
            arsort($pesticidesUsed);
            $topPesticides = array_slice($pesticidesUsed, 0, 3, true);
            
            $kpiData[$team->id] = [
                'team' => $team,
                'total_visits' => $allVisits->count(),
                'completed_visits' => $completedVisits,
                'pending_visits' => $pendingVisits,
                'reports_created' => $reportsCreated,
                'completion_rate' => $completionRate,
                'report_rate' => $reportRate,
                'avg_report_time' => $avgReportTime,
                'avg_visit_duration' => $avgVisitDuration,
                'avg_satisfaction' => $avgSatisfaction,
                'top_insects' => $topInsects,
                'top_pesticides' => $topPesticides
            ];
        }
        
        return $kpiData;
    }
    
    /**
     * Get detailed visit data for a team
     */
    private function getTeamVisitData($teamId, $startDate, $endDate)
    {
        return VisitSchedule::with(['contract.customer', 'branch', 'report'])
            ->where('team_id', $teamId)
            ->whereBetween('visit_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('visit_date', 'desc')
            ->get();
    }
    
    /**
     * Get detailed report data for a team
     */
    private function getTeamReportData($teamId, $startDate, $endDate)
    {
        return VisitReport::with(['visit.contract.customer', 'createdBy'])
            ->whereHas('visit', function($query) use ($teamId, $startDate, $endDate) {
                $query->where('team_id', $teamId)
                    ->whereBetween('visit_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Generate PDF report for team KPIs
     */
    public function generatePdfReport(Request $request)
    {
        // Only technical managers can access this
        if (Auth::user()->role !== 'technical') {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        
        $teamId = $request->team_id;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        $team = Team::with(['leader', 'members'])->findOrFail($teamId);
        $kpiData = $this->calculateTeamKpis($startDate, $endDate, $teamId);
        
        // Generate HTML content for the report
        $htmlContent = view('managers.technical.kpi.pdf_report', [
            'team' => $team,
            'kpiData' => $kpiData[$teamId],
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->render();
        
        // Use the existing ReportsController to generate the PDF
        $reportController = new ReportsController();
        
        return $reportController->generatePDF(new Request([
            'report_type' => 'team_kpi',
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'title' => 'Team KPI Report: ' . $team->name,
            'html_content' => $htmlContent
        ]));
    }
    
    /**
     * Compare KPIs between teams
     */
    public function compareTeams(Request $request)
    {
        // Only technical managers can access this
        if (Auth::user()->role !== 'technical') {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        
        // Get teams for selection
        $teams = Team::with(['leader'])->get();
        
        // Get selected teams
        $selectedTeams = [];
        if ($request->filled('team_ids')) {
            $selectedTeams = Team::with(['leader', 'members'])->whereIn('id', $request->team_ids)->get();
        }
        
        // Get date range
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        // Calculate KPIs for selected teams
        $kpiData = [];
        if (!empty($selectedTeams)) {
            $kpiData = $this->calculateTeamKpis($startDate, $endDate, null);
            // Filter to only include selected teams
            $kpiData = array_filter($kpiData, function($key) use ($request) {
                return in_array($key, $request->team_ids);
            }, ARRAY_FILTER_USE_KEY);
        }
        
        return view('managers.technical.kpi.compare', compact('teams', 'selectedTeams', 'startDate', 'endDate', 'kpiData'));
    }
}
