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
        $reportsByInsect = [];
        $totalReports = $reports->count();

        foreach ($reports as $report) {
            $targetInsectsJson = $report->target_insects;
            $targetInsects = is_string($targetInsectsJson) ? json_decode($targetInsectsJson, true) : [];
            $targetInsects = is_array($targetInsects) ? $targetInsects : [];

            // Get insect quantities with better validation
            $quantitiesJson = $report->insect_quantities;
            $quantities = is_string($quantitiesJson) ? json_decode($quantitiesJson, true) : [];
            $quantities = is_array($quantities) ? $quantities : [];

            foreach ($targetInsects as $insect) {
                if (!isset($insectCounts[$insect])) {
                    $insectCounts[$insect] = 0;
                    $insectQuantities[$insect] = 0;
                    $reportsByInsect[$insect] = [];
                }

                $insectCounts[$insect]++;
                $reportsByInsect[$insect][] = $report->id;

                // Add quantities with validation
                if (isset($quantities[$insect]) && is_numeric($quantities[$insect])) {
                    $insectQuantities[$insect] += (int)$quantities[$insect];
                } else {
                    // If no specific quantity data, use a default of 1 per report
                    $insectQuantities[$insect] += 1;
                }
            }
        }

        // Sort by frequency (highest first)
        arsort($insectCounts);

        // Calculate statistical significance
        $significanceThreshold = sqrt($totalReports) * 1.5; // Statistical significance threshold

        // Get insect names and calculate more meaningful statistics
        $result = [];
        foreach ($insectCounts as $value => $count) {
            $insect = TargetInsect::where('value', $value)->first();
            $name = $insect ? $insect->name : $value;

            // Calculate temporal distribution (how spread out the sightings are)
            $temporalSpread = 0;
            if (count($reportsByInsect[$value]) >= 2) {
                $reportDates = VisitReport::whereIn('id', $reportsByInsect[$value])
                    ->orderBy('created_at')
                    ->pluck('created_at');

                if ($reportDates->count() >= 2) {
                    $firstDate = strtotime($reportDates->first());
                    $lastDate = strtotime($reportDates->last());
                    $dateSpan = ($lastDate - $firstDate) / (60 * 60 * 24); // Span in days
                    $temporalSpread = $dateSpan > 0 ? $count / $dateSpan : 0;
                }
            }

            $quantity = $insectQuantities[$value] ?? $count;
            $percentage = $totalReports > 0 ? ($count / $totalReports) * 100 : 0;
            $isStatisticallySignificant = $count > $significanceThreshold;

            $result[] = [
                'name' => $name,
                'value' => $value,
                'count' => $count,
                'quantity' => $quantity,
                'avg_per_report' => $count > 0 ? round($quantity / $count, 2) : 0,
                'percentage' => round($percentage, 1),
                'statistical_significance' => $isStatisticallySignificant ? 'High' : 'Low',
                'temporal_spread' => round($temporalSpread, 4),
                'confidence_interval' => $this->calculateConfidenceInterval($count, $totalReports)
            ];
        }

        return $result;
    }

    /**
     * Calculate 95% confidence interval for proportions
     */
    private function calculateConfidenceInterval($count, $total)
    {
        if ($total == 0) return ['lower' => 0, 'upper' => 0];

        $proportion = $count / $total;
        $z = 1.96; // 95% confidence

        // Wilson score interval - better for small samples and edge cases
        $denominator = 1 + ($z * $z / $total);
        $center = ($proportion + ($z * $z) / (2 * $total)) / $denominator;
        $deviation = $z * sqrt(($proportion * (1 - $proportion) + ($z * $z) / (4 * $total)) / $total) / $denominator;

        return [
            'lower' => max(0, round(($center - $deviation) * 100, 1)),
            'upper' => min(100, round(($center + $deviation) * 100, 1))
        ];
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
        usort($visitData, function ($a, $b) {
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
        usort($visitData, function ($a, $b) {
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
        $insectTypes = TargetInsect::where('active', true)->pluck('name', 'value')->toArray();

        // Generate the last 24 months for more comprehensive trend analysis
        for ($i = 23; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[$month] = [
                'label' => date('M Y', strtotime("-$i months")),
                'insects' => array_fill_keys(array_keys($insectTypes), 0)
            ];
            $quantities[$month] = [
                'label' => date('M Y', strtotime("-$i months")),
                'insects' => array_fill_keys(array_keys($insectTypes), 0)
            ];
        }

        // Get all reports with better date filtering
        $reports = VisitReport::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            'target_insects',
            'insect_quantities',
            'created_at'
        )
            ->where('created_at', '>=', date('Y-m-d', strtotime('-24 months')))
            ->orderBy('created_at')
            ->get();

        // Count insects by month with improved validation
        foreach ($reports as $report) {
            $month = $report->month;
            if (!isset($months[$month])) {
                continue;
            }

            $insectsJson = $report->target_insects;
            $insects = is_string($insectsJson) ? json_decode($insectsJson, true) : [];
            $insects = is_array($insects) ? $insects : [];

            $quantitiesJson = $report->insect_quantities;
            $insectQuantities = is_string($quantitiesJson) ? json_decode($quantitiesJson, true) : [];
            $insectQuantities = is_array($insectQuantities) ? $insectQuantities : [];

            foreach ($insects as $insect) {
                if (isset($months[$month]['insects'][$insect])) {
                    $months[$month]['insects'][$insect]++;

                    // Add quantities with validation
                    if (isset($insectQuantities[$insect]) && is_numeric($insectQuantities[$insect])) {
                        $quantities[$month]['insects'][$insect] += (int)$insectQuantities[$insect];
                    } else {
                        $quantities[$month]['insects'][$insect] += 1;
                    }
                }
            }
        }

        // Format the data with improved trend analysis
        foreach ($insectTypes as $value => $name) {
            $occurrenceData = [];
            $quantityData = [];
            $movingAverages = [];
            $growthRates = [];
            $seasonalFactors = [];

            // First pass to collect raw data
            $previousCount = null;
            foreach ($months as $month => $info) {
                $count = $info['insects'][$value] ?? 0;
                $quantity = $quantities[$month]['insects'][$value] ?? 0;

                $occurrenceData[] = [
                    'month' => $info['label'],
                    'count' => $count
                ];

                $quantityData[] = [
                    'month' => $info['label'],
                    'count' => $quantity
                ];

                // Calculate month-over-month growth rate
                if ($previousCount !== null && $previousCount > 0) {
                    $growthRate = (($count / $previousCount) - 1) * 100;
                    $growthRates[] = [
                        'month' => $info['label'],
                        'rate' => round($growthRate, 1)
                    ];
                }
                $previousCount = $count;
            }

            // Calculate 3-month moving average for smoother trend line
            for ($i = 2; $i < count($occurrenceData); $i++) {
                $sum = $occurrenceData[$i - 2]['count'] + $occurrenceData[$i - 1]['count'] + $occurrenceData[$i]['count'];
                $movingAverages[] = [
                    'month' => $occurrenceData[$i]['month'],
                    'average' => round($sum / 3, 1)
                ];
            }

            // Calculate seasonal factors (based on same month last year)
            for ($i = 12; $i < count($occurrenceData); $i++) {
                $currentValue = $occurrenceData[$i]['count'];
                $lastYearValue = $occurrenceData[$i - 12]['count'];

                if ($lastYearValue > 0) {
                    $seasonalFactor = $currentValue / $lastYearValue;
                    $seasonalFactors[] = [
                        'month' => $occurrenceData[$i]['month'],
                        'factor' => round($seasonalFactor, 2)
                    ];
                }
            }

            $trends[$value] = [
                'name' => $name,
                'data' => $occurrenceData,
                'quantity_data' => $quantityData,
                'moving_average' => $movingAverages,
                'growth_rates' => $growthRates,
                'seasonal_factors' => $seasonalFactors
            ];
        }

        return $trends;
    }

    /**
     * Generate PDF for contract insect analytics
     */
    public function generateContractAnalyticsPDF($contractId)
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

        // Generate HTML content for PDF
        $htmlContent = view('shared.analytics.pdf.contract-insects', compact('contract', 'targetInsects', 'insectStats', 'visitData'))->render();

        // Initialize mPDF with specific settings
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape orientation
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'default_font' => 'cairo'
        ]);

        // Create a complete HTML document with proper structure
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap");
                body {
                    font-family: "Cairo", sans-serif;
                    margin: 0;
                    padding: 0;
                    font-size: 12px;
                }
                .header {
                    text-align: center;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #ffcc00;
                    margin-bottom: 20px;
                }
                .logo {
                    max-width: 100px;
                    margin: 0 auto;
                    display: block;
                }
                .company-name {
                    font-weight: bold;
                    margin: 5px 0;
                    font-size: 14px;
                }
                .report-title {
                    font-size: 16px;
                    font-weight: bold;
                    margin: 10px 0;
                    background-color: #ffcc00;
                    padding: 5px;
                    color: #000;
                }
                .report-period {
                    font-size: 12px;
                    margin-bottom: 10px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                table, th, td {
                    border: 1px solid #ddd;
                }
                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                    padding: 8px;
                    text-align: center;
                }
                td {
                    padding: 8px;
                    text-align: left;
                }
                .text-center {
                    text-align: center;
                }
                .text-right {
                    text-align: right;
                }
                .fw-bold {
                    font-weight: bold;
                }
                .stats-card {
                    border: 1px solid #ddd;
                    padding: 10px;
                    margin-bottom: 15px;
                    background-color: #f9f9f9;
                }
                .stats-title {
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .stats-value {
                    font-size: 16px;
                    font-weight: bold;
                }
                .row {
                    display: flex;
                    flex-wrap: wrap;
                    margin-right: -15px;
                    margin-left: -15px;
                }
                .col-4 {
                    flex: 0 0 33.333333%;
                    max-width: 33.333333%;
                    padding-right: 15px;
                    padding-left: 15px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="' . public_path('backend/assets/images/logo-icon.png') . '" alt="Spider Web Logo" class="logo">
                <div class="company-name">خيوط العنكبوت لمكافحة الحشرات</div>
                <div class="company-name">Spider Web For Pest Control</div>
                <div class="report-title">Insect Analytics for Contract #' . $contract->contract_number . '</div>
                <div class="report-period">Customer: ' . $contract->customer->name . ' | Contract Period: ' . date('d M Y', strtotime($contract->contract_start_date)) . ' to ' . date('d M Y', strtotime($contract->contract_end_date)) . '</div>
            </div>
            <div class="content">' . $htmlContent . '</div>
        </body>
        </html>';

        // Write the complete HTML to the PDF
        $mpdf->WriteHTML($html);

        // Return the PDF as a download
        return $mpdf->Output('contract_' . $contract->id . '_insect_analytics.pdf', 'D');
    }

    /**
     * Generate PDF for branch insect analytics
     */
    public function generateBranchAnalyticsPDF($contractId, $branchId)
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

        // Generate HTML content for PDF
        $htmlContent = view('shared.analytics.pdf.branch-insects', compact('contract', 'branch', 'targetInsects', 'insectStats', 'visitData'))->render();

        // Initialize mPDF with specific settings
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape orientation
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'default_font' => 'cairo'
        ]);

        // Create a complete HTML document with proper structure
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap");
                body {
                    font-family: "Cairo", sans-serif;
                    margin: 0;
                    padding: 0;
                    font-size: 12px;
                }
                .header {
                    text-align: center;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #ffcc00;
                    margin-bottom: 20px;
                }
                .logo {
                    max-width: 100px;
                    margin: 0 auto;
                    display: block;
                }
                .company-name {
                    font-weight: bold;
                    margin: 5px 0;
                    font-size: 14px;
                }
                .report-title {
                    font-size: 16px;
                    font-weight: bold;
                    margin: 10px 0;
                    background-color: #ffcc00;
                    padding: 5px;
                    color: #000;
                }
                .report-period {
                    font-size: 12px;
                    margin-bottom: 10px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                table, th, td {
                    border: 1px solid #ddd;
                }
                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                    padding: 8px;
                    text-align: center;
                }
                td {
                    padding: 8px;
                    text-align: left;
                }
                .text-center {
                    text-align: center;
                }
                .text-right {
                    text-align: right;
                }
                .fw-bold {
                    font-weight: bold;
                }
                .stats-card {
                    border: 1px solid #ddd;
                    padding: 10px;
                    margin-bottom: 15px;
                    background-color: #f9f9f9;
                }
                .stats-title {
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .stats-value {
                    font-size: 16px;
                    font-weight: bold;
                }
                .row {
                    display: flex;
                    flex-wrap: wrap;
                    margin-right: -15px;
                    margin-left: -15px;
                }
                .col-4 {
                    flex: 0 0 33.333333%;
                    max-width: 33.333333%;
                    padding-right: 15px;
                    padding-left: 15px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="' . public_path('backend/assets/images/logo-icon.png') . '" alt="Spider Web Logo" class="logo">
                <div class="company-name">خيوط العنكبوت لمكافحة الحشرات</div>
                <div class="company-name">Spider Web For Pest Control</div>
                <div class="report-title">Branch Insect Analytics for Contract #' . $contract->contract_number . '</div>
                <div class="report-period">Customer: ' . $contract->customer->name . ' | Branch: ' . $branch->branch_name . '</div>
            </div>
            <div class="content">' . $htmlContent . '</div>
        </body>
        </html>';

        // Write the complete HTML to the PDF
        $mpdf->WriteHTML($html);

        // Return the PDF as a download
        return $mpdf->Output('branch_' . $branch->id . '_insect_analytics.pdf', 'D');
    }
}
