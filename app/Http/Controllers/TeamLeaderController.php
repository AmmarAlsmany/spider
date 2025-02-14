<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\VisitSchedule;
use App\Models\VisitReport;
use App\Models\contracts;
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
            $query->whereHas('contract', function($q) use ($request) {
                $q->where('contract_number', 'like', '%' . $request->contract_number . '%');
            });
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('visit_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('visit_date', '<=', $request->end_date);
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
        $visit = VisitSchedule::findOrFail($id);

        // Check if the visit belongs to the team leader's team
        if ($visit->team->team_leader_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Check if visit is scheduled for today or a future date
        if (!Carbon::parse($visit->visit_date)->isToday() && Carbon::parse($visit->visit_date)->isFuture()) {
            return redirect()->back()->with('error', 'Can only complete today\'s or future visits.');
        }

        // Mark visit as completed
        $visit->status = 'completed';
        $visit->save();

        // Notify sales manager,sales representative about visit completion
        $data = [
            'title' => "Visit Completed: " . $visit->contract->contract_number,
            'message' => 'Your visit has been completed',
            'url' => '#',
        ];

        $this->notifyRoles(['sales', 'sales_manager', 'technical'], $data, $visit->contract->customer_id, $visit->contract->sales_id);

        // Redirect to create report
        return redirect()->route('team-leader.visit.report.create', $visit->id)
            ->with('success', 'Visit marked as completed. Please fill out the visit report.');
    }

    public function createReport($visitId)
    {
        $visit = VisitSchedule::with(['contract.customer', 'branch', 'team'])
            ->findOrFail($visitId);

        // Check if the visit belongs to the team leader's team
        if ($visit->team->team_leader_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Check if visit is completed
        if ($visit->status !== 'completed') {
            return redirect()->route('team-leader.visit.show', $visit->id)
                ->with('error', 'Visit must be marked as completed before creating a report.');
        }

        // Check if report already exists
        if ($visit->report) {
            return redirect()->route('team-leader.visit.show', $visit->id)
                ->with('error', 'Report already exists for this visit.');
        }

        // Notify sales manager,sales representative about visit report creation
        $data = [
            'title' => "Visit Report Created: " . $visit->contract->contract_number,
            'message' => 'Your visit has been completed and report is ready to download',
            'url' => '#',
        ];

        $this->notifyRoles(['sales', 'sales_manager', 'technical'], $data, $visit->contract->customer_id, $visit->contract->sales_id);

        return view('managers.team-leader.create-report', compact('visit'));
    }

    public function storeReport(Request $request, $visitId)
    {
        $visit = VisitSchedule::findOrFail($visitId);

        // Check if visit is completed
        if ($visit->status !== 'completed') {
            return redirect()->back()->with('error', 'Visit must be marked as completed before creating a report.');
        }

        // Validate request
        $request->validate([
            'time_in' => 'required',
            'time_out' => 'required',
            'visit_type' => 'required|in:regular,complementary,emergency,free,other',
            'target_insects' => 'required|array',
            'pesticides_used' => 'required|array',
            'recommendations' => 'required|string',
            'customer_notes' => 'nullable|string',
            'customer_signature' => 'required|string', // This will be a base64 image string
            'phone_signature' => 'required|string'
        ]);

        // Create visit report
        $report = new VisitReport([
            'visit_id' => $visitId,
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
            'visit_type' => $request->visit_type,
            'target_insects' => json_encode($request->target_insects),
            'pesticides_used' => json_encode($request->pesticides_used),
            'recommendations' => $request->recommendations,
            'customer_notes' => $request->customer_notes,
            'customer_signature' => $request->customer_signature,
            'phone_signature' => $request->phone_signature,
            'created_by' => Auth::id()
        ]);

        $report->save();

        return redirect()->route('team-leader.visit.show', $visit->id)
            ->with('success', 'Visit report has been created successfully.');
    }

    public function showContract($contractId)
    {
        $contract = contracts::with(['customer', 'branchs', 'visitSchedules' => function($query) {
            $query->orderBy('visit_date', 'desc');
        }])->findOrFail($contractId);

        return view('managers.team-leader.contract-details', compact('contract'));
    }
}
