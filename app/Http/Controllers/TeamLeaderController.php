<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\VisitSchedule;
use App\Models\VisitReport;
use App\Models\contracts;
use App\Models\TargetInsect;
use App\Models\Pesticide;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Traits\NotificationDispatcher;

class TeamLeaderController extends Controller
{
    use NotificationDispatcher;
    public function dashboard()
    {
        // Get the authenticated team leader's team
        $team = Team::where('team_leader_id', Auth::id())->first();

        if (!$team) {
            return redirect()->back()->with('error', 'No team assigned.');
        }

        // Get today's visits
        $todayVisits = VisitSchedule::with(['contract.customer', 'branch'])
            ->where('team_id', $team->id)
            ->whereDate('visit_date', Carbon::today())
            ->orderBy('visit_time')
            ->get();

        // Get upcoming visits (next 7 days)
        $upcomingVisits = VisitSchedule::with(['contract.customer', 'branch'])
            ->where('team_id', $team->id)
            ->whereDate('visit_date', '>', Carbon::today())
            ->whereDate('visit_date', '<=', Carbon::today()->addDays(7))
            ->orderBy('visit_date')
            ->orderBy('visit_time')
            ->get();

        // Get statistics
        $statistics = [
            'today_visits' => $todayVisits->count(),
            'today_completed' => $todayVisits->where('status', 'completed')->count(),
            'upcoming_visits' => $upcomingVisits->count(),
            'total_completed' => VisitSchedule::where('team_id', $team->id)
                ->where('status', 'completed')
                ->count()
        ];

        return view('managers.team-leader.dashboard', compact('team', 'todayVisits', 'upcomingVisits', 'statistics'));
    }

    public function visits(Request $request)
    {
        // Get the authenticated team leader's team
        $team = Team::where('team_leader_id', Auth::id())->first();

        if (!$team) {
            return redirect()->back()->with('error', 'No team assigned.');
        }

        $query = VisitSchedule::with(['contract.customer', 'branch'])
            ->where('team_id', $team->id);

        // Filter by contract number
        if ($request->filled('contract_number')) {
            $query->whereHas('contract', function ($q) use ($request) {
                $q->where('contract_number', 'like', '%' . $request->contract_number . '%');
            });
        }

        // Filter by single date (for Today quick filter)
        if ($request->filled('date')) {
            $query->whereDate('visit_date', $request->date);
        } 
        // Filter by date range
        else {
            if ($request->filled('start_date')) {
                $query->whereDate('visit_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('visit_date', '<=', $request->end_date);
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $visits = $query->orderBy('visit_date', 'desc')
            ->orderBy('visit_time', 'desc')
            ->paginate(15);

        return view('managers.team-leader.visits', compact('team', 'visits'));
    }

    public function showVisit($id)
    {
        $visit = VisitSchedule::with(['contract.customer', 'branch', 'team', 'report'])
            ->findOrFail($id);

        // Check if the visit belongs to the team leader's team
        if ($visit->team->team_leader_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        return view('managers.team-leader.visit-details', compact('visit'));
    }

    public function completeVisit($id)
    {
        $visit = VisitSchedule::with('contract')->findOrFail($id);

        // Check if the visit belongs to the team leader's team
        if ($visit->team->team_leader_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Check if visit is scheduled for today or a future date
        if (!Carbon::parse($visit->visit_date)->isToday() && Carbon::parse($visit->visit_date)->isFuture()) {
            return redirect()->back()->with('error', 'Can only complete today\'s or future visits.');
        }
        
        // Check if the contract is stopped
        if ($visit->contract->contract_status === 'stopped') {
            return redirect()->back()->with('error', 'Cannot complete a visit associated with a stopped contract.');
        }

        // Mark visit as in-progress instead of completed
        $visit->status = 'in_progress';
        $visit->save();

        // Notify sales manager,sales representative about visit completion
        $data = [
            'title' => "Visit In Progress: " . $visit->contract->contract_number,
            'message' => 'Your visit is in progress and awaiting report submission',
        ];
        
        // Different URLs for different roles
        $roleUrls = [
            'sales' => route('view.contract.visit', $visit->contract->id),
            'sales_manager' => route('view.contract.visit', $visit->contract->id),
            'technical' => route('technical.visit.report.view', $visit->id)
        ];

        $this->notifyRoles(['sales', 'sales_manager', 'technical'], $data, $visit->contract->customer_id, $visit->contract->sales_id, $roleUrls);

        // Redirect to create report
        return redirect()->route('team-leader.visit.report.create', $visit->id)
            ->with('success', 'Visit marked as in-progress. Please fill out the visit report to complete it.');
    }

    public function createReport($visitId)
    {
        $visit = VisitSchedule::with(['contract.customer', 'branch', 'team'])
            ->findOrFail($visitId);

        // Check if the visit belongs to the team leader's team
        if ($visit->team->team_leader_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Check if visit is in-progress
        if ($visit->status !== 'in_progress') {
            return redirect()->route('team-leader.visit.show', $visit->id)
                ->with('error', 'Visit must be marked as in-progress before creating a report.');
        }

        // Check if report already exists
        if ($visit->report) {
            return redirect()->route('team-leader.visit.show', $visit->id)
                ->with('error', 'Report already exists for this visit.');
        }
        
        // Check if the contract is stopped
        if ($visit->contract->contract_status === 'stopped') {
            return redirect()->route('team-leader.visit.show', $visit->id)
                ->with('error', 'Cannot create report for a visit associated with a stopped contract.');
        }

        // Notify sales manager,sales representative about visit report creation
        $data = [
            'title' => "Visit Report Created: " . $visit->contract->contract_number,
            'message' => 'Your visit has been completed and report is ready to download',
        ];
        
        // Different URLs for different roles
        $roleUrls = [
            'sales' => route('contract.visit.report', $visit->id),
            'sales_manager' => route('contract.visit.report', $visit->id),
            'technical' => route('technical.visit.report.view', $visit->id)
        ];

        $this->notifyRoles(['sales', 'sales_manager', 'technical'], $data, $visit->contract->customer_id, $visit->contract->sales_id, $roleUrls);

        // Get active target insects for the form
        $targetInsects = TargetInsect::where('active', true)->orderBy('name')->get();
        
        // Get active pesticides for the form
        $pesticides = Pesticide::where('active', true)->orderBy('name')->get();

        return view('managers.team-leader.create-report', compact('visit', 'targetInsects', 'pesticides'));
    }

    public function storeReport(Request $request, $visitId)
    {
        try {
            $visit = VisitSchedule::with('contract')->findOrFail($visitId);

            // Check if visit is in-progress
            if ($visit->status !== 'in_progress') {
                return redirect()->back()->with('error', 'Visit must be marked as in-progress before creating a report.');
            }
            
            // Check if the contract is stopped
            if ($visit->contract->contract_status === 'stopped') {
                return redirect()->route('team-leader.visit.show', $visit->id)
                    ->with('error', 'Cannot create report for a visit associated with a stopped contract.');
            }

            // Check if the form was actually submitted by the user
            // This helps prevent saving incomplete data when users navigate away
            if (!$request->has('_token')) {
                return redirect()->back()->with('error', 'Form submission error. Please try again.');
            }

            // First validate the basic required fields
            $request->validate([
                'time_in' => 'required|date_format:H:i',
                'time_out' => 'required|date_format:H:i|after:time_in',
                'visit_type' => 'required|in:regular,complementary,emergency,free,other',
                'target_insects' => 'required|array',
                'target_insects.*' => 'required|string',
                'pesticides_used' => 'required|array',
                'pesticides_used.*' => 'required|string',
                'elimination_steps' => 'required|string|min:10',
                'recommendations' => 'required|string',
                'customer_notes' => 'nullable|string',
                'customer_satisfaction' => 'required|integer|min:1|max:5',
                'customer_signature' => 'required|string',
                'phone_signature' => 'required|string'
            ]);
            // Process and validate pesticide quantities
            $pesticide_quantities = [];
            foreach ($request->pesticides_used as $pesticide) {
                // Validate quantity and unit for each selected pesticide
                $request->validate([
                    "pesticide_quantity.$pesticide" => 'required|numeric|min:0',
                    "pesticide_unit.$pesticide" => 'required|in:g,ml'
                ], [
                    "pesticide_quantity.$pesticide.required" => "Please enter quantity for $pesticide",
                    "pesticide_quantity.$pesticide.numeric" => "Quantity for $pesticide must be a number",
                    "pesticide_quantity.$pesticide.min" => "Quantity for $pesticide cannot be negative",
                    "pesticide_unit.$pesticide.required" => "Please select unit for $pesticide",
                    "pesticide_unit.$pesticide.in" => "Unit for $pesticide must be either grams (g) or milliliters (ml)"
                ]);

                $pesticide_quantities[$pesticide] = [
                    'quantity' => $request->input("pesticide_quantity.$pesticide"),
                    'unit' => $request->input("pesticide_unit.$pesticide")
                ];
            }
            
            // Process insect quantities
            $insect_quantities = [];
            foreach ($request->target_insects as $insect) {
                // Validate quantity for each selected insect
                $request->validate([
                    "insect_quantity.$insect" => 'required|numeric|min:1'
                ], [
                    "insect_quantity.$insect.required" => "Please enter quantity for $insect",
                    "insect_quantity.$insect.numeric" => "Quantity for $insect must be a number",
                    "insect_quantity.$insect.min" => "Quantity for $insect must be at least 1"
                ]);

                $insect_quantities[$insect] = $request->input("insect_quantity.$insect");
            }

            // Create visit report
            $report = new VisitReport([
                'visit_id' => $visitId,
                'time_in' => $request->time_in,
                'time_out' => $request->time_out,
                'visit_type' => $request->visit_type,
                'target_insects' => json_encode($request->target_insects),
                'pesticides_used' => json_encode($request->pesticides_used),
                'pesticide_quantities' => json_encode($pesticide_quantities),
                'insect_quantities' => json_encode($insect_quantities),
                'elimination_steps' => $request->elimination_steps,
                'recommendations' => $request->recommendations,
                'customer_notes' => $request->customer_notes,
                'customer_satisfaction' => $request->customer_satisfaction,
                'customer_signature' => $request->customer_signature,
                'phone_signature' => $request->phone_signature,
                'created_by' => Auth::id()
            ]);

            $report->save();

            // Mark visit as completed
            $visit->status = 'completed';
            $visit->save();
        } catch (\Exception  $e) {
            return redirect()->back()->with('error', 'Failed to create visit report: ' . $e->getMessage());
        }

        return redirect()->route('team-leader.visit.show', $visit->id)
            ->with('success', 'Visit report has been created successfully.');
    }

    public function showContract($contractId)
    {
        $contract = contracts::with(['customer', 'branchs', 'visitSchedules' => function ($query) {
            $query->orderBy('visit_date', 'desc');
        }])->findOrFail($contractId);

        return view('managers.team-leader.contract-details', compact('contract'));
    }
}
