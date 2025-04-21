<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\VisitSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\contracts;
use App\Models\VisitReport;
use App\Models\client;
use App\Models\branchs;
use App\Services\VisitScheduleService;
use App\Traits\NotificationDispatcher;

class TechnicalController extends Controller
{
    use NotificationDispatcher;
    
    /**
     * Constructor to share data with all technical views
     */
    public function __construct()
    {
        // Share pending team assignments count with all technical views
        view()->composer('managers.technical.*', function ($view) {
            // Only compute this for authenticated technical users
            if (Auth::check() && Auth::user()->role === 'technical') {
                // Get count of contracts waiting for team assignment
                $pendingTeamAssignments = contracts::where('contract_status', 'under_processing')->count();
                $view->with('pendingTeamAssignments', $pendingTeamAssignments);
            }
        });
    }
    
    public function dashboard()
    {
        // Get all teams with their leaders and members
        $teams = Team::with(['leader', 'members'])->get();

        // Get today's schedules with contract and team information
        $teamSchedules = VisitSchedule::with(['team', 'contract', 'contract.customer', 'branch'])
            ->whereDate('visit_date', Carbon::today())
            ->orderBy('visit_time')
            ->get();

        // Get count of today's appointments
        $todayAppointments = VisitSchedule::whereDate('visit_date', Carbon::today())->count();

        // Get count of pending tasks
        $pendingTasks = VisitSchedule::whereDate('visit_date', '>=', Carbon::today())
            ->where('status', '!=', 'completed')
            ->count();

        // Get count of open tickets
        $openTickets = Tiket::where('status', 'open')->count();
        
        // Get count of contracts waiting for team assignment
        $pendingTeamAssignments = contracts::where('contract_status', 'under_processing')->count();

        // Get active contracts with upcoming visits
        $activeContracts = contracts::with(['customer', 'visitSchedules' => function ($query) {
            $query->whereDate('visit_date', '>=', Carbon::today())
                ->orderBy('visit_date', 'asc');
        }])
            ->where('contract_status', 'approved')
            ->whereHas('visitSchedules', function ($query) {
                $query->whereDate('visit_date', '>=', Carbon::today());
            })
            ->get();

        // Get statistics by contract type
        $contractStats = contracts::where('contract_status', 'approved')
            ->selectRaw('contract_type, count(*) as total')
            ->groupBy('contract_type')
            ->get();

        // Get visit reports for today's schedules
        $visitReports = VisitReport::with('visit')
            ->whereHas('visit', function ($query) {
                $query->whereDate('visit_date', Carbon::today());
            })
            ->get();

        // Fetch reports created by team leaders
        $reports = VisitReport::with(['visitSchedule.team', 'visitSchedule.contract.customer', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('managers.technical.dashboard', compact(
            'teams',
            'teamSchedules',
            'todayAppointments',
            'pendingTasks',
            'openTickets',
            'pendingTeamAssignments',
            'activeContracts',
            'contractStats',
            'visitReports',
            'reports'
        ));
    }

    public function index()
    {
        $teams = Team::with(['leader', 'members'])->get();
        return view('managers.technical.teams.index', compact('teams'));
    }
    
    /**
     * Display contracts waiting for team assignment
     */
    public function pendingTeamAssignments()
    {
        $contracts = contracts::with(['customer', 'branchs'])
            ->where('contract_status', 'under_processing')
            ->latest()
            ->paginate(10);
            
        $teams = Team::where('status', 'active')->get();
        
        return view('managers.technical.pending_team_assignments', compact('contracts', 'teams'));
    }
    
    /**
     * Process a contract that is waiting for team assignment
     */
    public function processContract(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            // Find the contract
            $contract = contracts::findOrFail($id);
            
            // Verify contract is in under_processing status
            if ($contract->contract_status !== 'under_processing') {
                return back()->with('error', 'This contract is not in under_processing status');
            }
            
            // Check if there are active teams available
            $activeTeams = Team::where('status', 'active')->count();
            if ($activeTeams == 0) {
                return back()->with('error', 'No active teams available for scheduling visits');
            }
            
            // Check if the contract has a valid visit_start_date
            // If not, set it to today's date for proper scheduling
            if (!isset($contract->visit_start_date) || empty($contract->visit_start_date)) {
                $contract->visit_start_date = now()->format('Y-m-d');
                \Illuminate\Support\Facades\Log::info("Setting visit_start_date for contract {$contract->id} to today: {$contract->visit_start_date}");
            } else {
                \Illuminate\Support\Facades\Log::info("Using existing visit_start_date for contract {$contract->id}: {$contract->visit_start_date}");
            }
            
            // Update contract status to approved
            $contract->contract_status = 'approved';
            $contract->save();
            
            // Create visit schedule
            $visitScheduleService = app(VisitScheduleService::class);
            $visitScheduleService->createVisitSchedule($contract);
            
            // Notify relevant parties
            $notificationData = [
                'title' => 'Contract Fully Approved: ' . $contract->contract_number,
                'message' => 'Contract has been processed and visits scheduled',
                'type' => 'success',
                'priority' => 'normal'
            ];
            
            // Different URLs for different roles
            $roleUrls = [
                'sales' => route('contract.show.details', $contract->id),
                'sales_manager' => route('sales_manager.contract.view', $contract->id),
                'client' => route('client.contract.details', $contract->id)
            ];

            // Notify client, sales manager and sales rep
            $this->notifyRoles(['client', 'sales', 'sales_manager'], $notificationData, 
                $contract->customer_id, $contract->sales_id, $roleUrls);
            
            DB::commit();
            return back()->with('success', 'Contract has been approved and visits scheduled successfully');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to process contract: ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'team_leader_id' => 'required|exists:users,id',
                'description' => 'nullable|string',
                'members' => 'nullable|array',
                'members.*' => 'exists:users,id'
            ]);

            // Initialize members array if not set
            $members = isset($validated['members']) ? $validated['members'] : [];

            // Remove team leader from members if present
            $members = array_diff($members, [$validated['team_leader_id']]);

            // Check if the team leader is already assigned to another team
            $existingLeaderTeam = Team::where('team_leader_id', $validated['team_leader_id'])->first();
            if ($existingLeaderTeam) {
                return redirect()->back()->with('error', 'This user is already a team leader in team: ' . $existingLeaderTeam->name);
            }

            // Check if the team leader is a member in another team
            $leaderMemberTeam = DB::table('team_members')
                ->join('teams', 'team_members.team_id', '=', 'teams.id')
                ->where('team_members.user_id', $validated['team_leader_id'])
                ->first();

            if ($leaderMemberTeam) {
                return redirect()->back()->with('error', 'The selected team leader is already a member in team: ' . $leaderMemberTeam->name);
            }

            // Check if any of the members are already in other teams or are team leaders
            if (!empty($members)) {
                // Check members who are already in other teams
                $existingMembers = DB::table('team_members')
                    ->join('teams', 'team_members.team_id', '=', 'teams.id')
                    ->join('users', 'team_members.user_id', '=', 'users.id')
                    ->whereIn('team_members.user_id', $members)
                    ->first();

                if ($existingMembers) {
                    return redirect()->back()->with('error', "User {$existingMembers->name} is already a member in team: {$existingMembers->name}");
                }

                // Check if any member is a team leader
                $memberLeaderTeam = Team::whereIn('team_leader_id', $members)->first();
                if ($memberLeaderTeam) {
                    $leader = User::find($memberLeaderTeam->team_leader_id);
                    return redirect()->back()->with('error', "User {$leader->name} is already a team leader in team: {$memberLeaderTeam->name}");
                }
            }

            // Create the team
            $team = Team::create([
                'name' => $validated['name'],
                'team_leader_id' => $validated['team_leader_id'],
                'description' => $validated['description'] ?? null
            ]);

            // Attach members if any
            if (!empty($members)) {
                $team->members()->attach($members);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Team created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating team: ' . $e->getMessage());
        }
    }

    public function modify(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'team_leader_id' => 'sometimes|exists:users,id',
                'description' => 'nullable|string',
                'members' => 'nullable|array',
                'members.*' => 'exists:users,id'
            ]);

            // Initialize members array if not set
            $members = isset($validated['members']) ? $validated['members'] : [];

            // Remove team leader from members if present
            if (isset($validated['team_leader_id'])) {
                $members = array_diff($members, [$validated['team_leader_id']]);
            }

            // If changing team leader
            if (isset($validated['team_leader_id']) && $validated['team_leader_id'] !== $team->team_leader_id) {
                // Check if current team has active schedules
                if ($team->visitSchedules()->exists()) {
                    return redirect()->back()->with('error', 'Cannot change team leader: This team has active visit schedules. Please complete or reassign the schedules first.');
                }

                // Check if new leader is already a leader elsewhere
                $existingLeaderTeam = Team::where('team_leader_id', $validated['team_leader_id'])
                    ->where('id', '!=', $team->id)
                    ->first();
                if ($existingLeaderTeam) {
                    return redirect()->back()->with('error', "This user is already a team leader in team: {$existingLeaderTeam->name}");
                }

                // Check if new leader is a member in another team
                $leaderMemberTeam = DB::table('team_members')
                    ->join('teams', 'team_members.team_id', '=', 'teams.id')
                    ->where('team_members.user_id', $validated['team_leader_id'])
                    ->where('teams.id', '!=', $team->id)
                    ->first();

                if ($leaderMemberTeam) {
                    return redirect()->back()->with('error', "The selected team leader is already a member in team: {$leaderMemberTeam->name}");
                }
            }

            // Handle member changes
            if (isset($validated['members'])) {
                $currentMembers = $team->members->pluck('id')->toArray();
                $removedMembers = array_diff($currentMembers, $members);
                $newMembers = array_diff($members, $currentMembers);

                // Check if we're removing members and have active schedules
                if (!empty($removedMembers) && $team->visitSchedules()->exists()) {
                    return redirect()->back()->with('error', 'Cannot remove team members: This team has active visit schedules. Please complete or reassign the schedules first.');
                }

                // Check if new members are already in other teams or are team leaders
                if (!empty($newMembers)) {
                    // Check if any new member is already in another team
                    $existingMembers = DB::table('team_members')
                        ->join('teams', 'team_members.team_id', '=', 'teams.id')
                        ->join('users', 'team_members.user_id', '=', 'users.id')
                        ->whereIn('team_members.user_id', $newMembers)
                        ->where('teams.id', '!=', $team->id)
                        ->first();

                    if ($existingMembers) {
                        return redirect()->back()->with('error', "User {$existingMembers->name} is already a member in team: {$existingMembers->name}");
                    }

                    // Check if any new member is a team leader
                    $memberLeaderTeam = Team::whereIn('team_leader_id', $newMembers)->first();
                    if ($memberLeaderTeam) {
                        $leader = User::find($memberLeaderTeam->team_leader_id);
                        return redirect()->back()->with('error', "User {$leader->name} is already a team leader in team: {$memberLeaderTeam->name}");
                    }
                }

                // All checks passed, sync the members
                $team->members()->sync($members);
            }

            $team->update($validated);

            DB::commit();
            return redirect()->back()->with('success', 'Team updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating team: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $team = Team::findOrFail($id);

        // Check if team has any linked visit schedules
        if ($team->visitSchedules()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete team: This team is linked to visit schedules. Please reassign or delete the schedules first.');
        }

        try {
            DB::beginTransaction();
            $team->forceDelete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting team: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Team deleted successfully');
    }

    // Workers Management
    public function workersIndex()
    {
        $workers = User::where('role', 'worker')->get();
        return view('managers.technical.workers.index', compact('workers'));
    }

    public function createWorker(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'phone' => 'required|string|unique:users,phone',
                'address' => 'nullable|string',
            ]);

            $worker = new User();
            $worker->name = $validated['name'];
            $worker->email = $validated['email'];
            $worker->password = Hash::make($validated['password']);
            $worker->phone = $validated['phone'];
            $worker->address = $validated['address'];
            $worker->role = 'worker';
            $worker->status = 'active';
            $worker->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating worker: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Worker created successfully');
    }

    public function updateWorker(Request $request, $id)
    {
        $worker = User::findOrFail($id);

        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'required|string',
                'address' => 'nullable|string',
                'password' => 'nullable|min:6',
                'status' => 'required|in:active,inactive'
            ]);

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'status' => $validated['status']
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $worker->update($updateData);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating worker: ' . $e->getMessage());
        }

        DB::commit();
        return redirect()->back()->with('success', 'Worker updated successfully');
    }

    public function deleteWorker($id)
    {
        $worker = User::findOrFail($id);
        $worker->forceDelete();

        return redirect()->back()->with('success', 'Worker deleted successfully');
    }

    // Team Leaders Management
    public function teamLeadersIndex()
    {
        $query = User::where('role', 'team_leader');

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $teamLeaders = $query->get();
        return view('managers.technical.team-leaders.index', compact('teamLeaders'));
    }

    public function createTeamLeader(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'phone' => 'required|string|unique:users,phone',
                'address' => 'nullable|string',
            ]);

            $teamLeader = new User();
            $teamLeader->name = $validated['name'];
            $teamLeader->email = $validated['email'];
            $teamLeader->password = Hash::make($validated['password']);
            $teamLeader->phone = $validated['phone'];
            $teamLeader->address = $validated['address'];
            $teamLeader->role = 'team_leader';
            $teamLeader->status = 'active';
            $teamLeader->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating team leader: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Team Leader created successfully');
    }

    public function updateTeamLeader(Request $request, $id)
    {
        $teamLeader = User::findOrFail($id);

        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'required|string',
                'address' => 'required|string',
                'password' => 'nullable|min:6',
                'status' => 'required|in:active,inactive'
            ]);

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'status' => $validated['status']
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $teamLeader->update($updateData);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating team leader: ' . $e->getMessage());
        }

        DB::commit();
        return redirect()->back()->with('success', 'Team Leader updated successfully');
    }

    public function deleteTeamLeader($id)
    {
        $teamLeader = User::findOrFail($id);
        // Check if team leader is assigned to any team
        if ($teamLeader->teams()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete team leader with assigned teams');
        }

        $teamLeader->forceDelete();

        return redirect()->back()->with('success', 'Team Leader deleted successfully');
    }

    public function viewScheduledAppointments(Request $request)
    {
        // Base query for visit schedules with relationships
        $baseQuery = VisitSchedule::with([
            'contract',
            'contract.customer',
            'contract.branchs',
            'team',
            'branch'
        ])->whereHas('contract', function ($q) {
            $q->where('contract_status', 'approved');
        });

        // Clone the base query for filtering
        $query = clone $baseQuery;

        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('visit_date', [$request->start_date, $request->end_date]);
        }
        // Filter by specific date if provided
        elseif ($request->has('date')) {
            $query->whereDate('visit_date', $request->date);
        }
        // Filter by month and year if provided
        elseif ($request->has('month') && $request->has('year')) {
            $query->whereYear('visit_date', $request->year)
                ->whereMonth('visit_date', $request->month);
        }
        // Default to current month
        else {
            $query->whereMonth('visit_date', now()->month)
                ->whereYear('visit_date', now()->year);
        }

        // Get today's date
        $today = now()->toDateString();

        // Get pending visits count
        $pendingVisits = VisitSchedule::where('status', 'scheduled')
            ->count();

        // Get today's total visits
        $todayVisits = VisitSchedule::whereDate('visit_date', $today)->count();

        // Get today's completed visits
        $todayCompletedVisits = VisitSchedule::whereDate('visit_date', $today)
            ->where('status', 'completed')
            ->count();

        // Get contracts with their visits
        $contracts = contracts::with(['customer', 'branchs'])
            ->where('contract_status', 'approved')
            ->whereHas('visitSchedules')
            ->get();

        // Get sort direction from request or default to descending
        $sortDirection = $request->input('sort_direction', 'desc');
        // Validate sort direction
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'desc';

        // Get paginated visits for each contract
        $contractVisits = [];
        $branchVisitsPagination = [];

        foreach ($contracts as $contract) {
            // Get the contract's visits
            $contractVisits[$contract->id] = VisitSchedule::with(['team', 'branch'])
                ->where('contract_id', $contract->id)
                ->orderBy('visit_date', $sortDirection)
                ->orderBy('visit_time', $sortDirection)
                ->paginate(5, ['*'], 'contract_' . $contract->id . '_page');

            // Group visits by branch for branch-specific pagination
            if ($contract->branchs->count() > 0) {
                foreach ($contract->branchs as $branch) {
                    $branchVisitsPagination[$branch->id] = VisitSchedule::with(['team', 'branch'])
                        ->where('contract_id', $contract->id)
                        ->where('branch_id', $branch->id)
                        ->orderBy('visit_date', $sortDirection)
                        ->orderBy('visit_time', $sortDirection)
                        ->paginate(6, ['*'], 'branch_' . $branch->id . '_page');
                }
            }

            // Also handle main location (no branch_id or null branch_id)
            $branchVisitsPagination['main'] = VisitSchedule::with(['team', 'branch'])
                ->where('contract_id', $contract->id)
                ->where(function ($query) {
                    $query->whereNull('branch_id')
                        ->orWhere('branch_id', 0);
                })
                ->orderBy('visit_date', $sortDirection)
                ->orderBy('visit_time', $sortDirection)
                ->paginate(6, ['*'], 'branch_main_' . $contract->id . '_page');
        }

        // Get teams for assignment
        $teams = Team::all();

        // Get active clients with approved contracts
        $clients = client::whereHas('contracts', function ($query) {
            $query->where('contract_status', 'approved');
        })->get();

        return view('managers.technical.scheduled_appointments', compact(
            'contracts',
            'contractVisits',
            'branchVisitsPagination',
            'pendingVisits',
            'todayVisits',
            'todayCompletedVisits',
            'teams',
            'clients',
            'sortDirection'
        ));
    }

    public function markAppointmentComplete($appointmentId)
    {
        try {
            $appointment = VisitSchedule::findOrFail($appointmentId);
            $appointment->status = 'completed';
            $appointment->save();

            return redirect()->route('technical.scheduled-appointments')
                ->with('success', 'Appointment marked as completed successfully');
        } catch (\Exception $e) {
            return redirect()->route('technical.scheduled-appointments')
                ->with('error', 'Error marking appointment as completed: ' . $e->getMessage());
        }
    }

    public function cancelAppointment($appointmentId)
    {
        try {
            $appointment = VisitSchedule::findOrFail($appointmentId);
            $appointment->status = 'cancelled';
            $appointment->save();

            return redirect()->route('technical.scheduled-appointments')
                ->with('success', 'Appointment cancelled successfully');
        } catch (\Exception $e) {
            return redirect()->route('technical.scheduled-appointments')
                ->with('error', 'Error cancelling appointment: ' . $e->getMessage());
        }
    }

    public function viewCompletedVisits(Request $request)
    {
        $query = VisitSchedule::with(['contract', 'contract.customer', 'team'])
            ->where('status', 'completed');

        // Filter by date range if provided
        if ($request->filled('start_date')) {
            $query->whereDate('visit_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('visit_date', '<=', $request->end_date);
        }

        // Filter by team if provided
        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        // Filter by contract number if provided
        if ($request->filled('contract_number')) {
            $query->whereHas('contract', function ($q) use ($request) {
                $q->where('contract_number', 'like', '%' . $request->contract_number . '%');
            });
        }

        // Get sort direction from request or default to descending
        $sortDirection = $request->input('sort_direction', 'desc');
        // Validate sort direction
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'desc';

        $visits = $query->orderBy('visit_date', $sortDirection)
            ->orderBy('visit_time', $sortDirection)
            ->paginate(10);

        $teams = Team::where('type', '=', 'scattered')
            ->where('status', '=', 'active')
            ->get();

        return view('managers.technical.completed_visits', compact('visits', 'teams', 'sortDirection'));
    }

    public function viewCancelledVisits(Request $request)
    {
        $query = VisitSchedule::with(['contract', 'contract.customer', 'team'])
            ->where('status', 'cancelled');

        // Filter by date range if provided
        if ($request->filled('start_date')) {
            $query->whereDate('visit_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('visit_date', '<=', $request->end_date);
        }

        // Filter by team if provided
        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        // Filter by contract number if provided
        if ($request->filled('contract_number')) {
            $query->whereHas('contract', function ($q) use ($request) {
                $q->where('contract_number', 'like', '%' . $request->contract_number . '%');
            });
        }

        // Get sort direction from request or default to descending
        $sortDirection = $request->input('sort_direction', 'desc');
        // Validate sort direction
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'desc';

        $visits = $query->orderBy('visit_date', $sortDirection)
            ->orderBy('visit_time', $sortDirection)
            ->paginate(10);

        $teams = Team::where('type', '=', 'scattered')
            ->where('status', '=', 'active')
            ->get();

        return view('managers.technical.cancelled_visits', compact('visits', 'teams', 'sortDirection'));
    }

    public function rescheduleVisit(Request $request, $id)
    {
        try {
            $request->validate([
                'visit_date' => 'required',
                'visit_time' => 'required',
                'team_id' => 'required',
            ]);

            $visit = VisitSchedule::findOrFail($id);
            $visitDateTime = Carbon::parse($request->visit_date . ' ' . $request->visit_time);
            $visitEndTime = $visitDateTime->copy()->addHours(2);

            // Validation checks:
            // 1. Check if the team is already assigned to another visit that overlaps with this time slot
            $existingTeamVisit = VisitSchedule::where('team_id', $request->team_id)
                ->where('visit_date', $visitDateTime->format('Y-m-d'))
                ->where('id', '!=', $id)
                ->where('status', 'scheduled')
                ->where(function ($query) use ($visitDateTime, $visitEndTime) {
                    // Check overlapping time slots
                    $query->where(function ($q) use ($visitDateTime, $visitEndTime) {
                        // New visit starts during an existing visit
                        $q->where(function ($innerQ) use ($visitDateTime) {
                            $innerQ->whereTime('visit_time', '<=', $visitDateTime->format('H:i:s'))
                                ->whereRaw("ADDTIME(visit_time, '02:00:00') > ?", [$visitDateTime->format('H:i:s')]);
                        });
                        // OR Existing visit starts during our new visit
                        $q->orWhere(function ($innerQ) use ($visitDateTime, $visitEndTime) {
                            $innerQ->whereTime('visit_time', '>=', $visitDateTime->format('H:i:s'))
                                ->whereTime('visit_time', '<', $visitEndTime->format('H:i:s'));
                        });
                    });
                })
                ->exists();

            if ($existingTeamVisit) {
                return redirect()->back()->with('error', 'Selected team is already booked during this time slot or there is a scheduling conflict within the 2-hour visit window.');
            }
            
            // We don't need to check for all visits overlapping - we only need to ensure
            // the selected team isn't double-booked, which is handled by the first validation check.
            // First validation is sufficient - we don't need a second global time check.

            // Update the visit schedule
            $visit->update([
                'visit_date' => $visitDateTime->format('Y-m-d'),
                'visit_time' => $visitDateTime->format('H:i:s'),
                'team_id' => $request->team_id,
                'status' => 'scheduled'
            ]);

            return redirect()->back()->with('success', 'Visit rescheduled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reschedule visit: ' . $e->getMessage());
        }
    }

    public function editAppointment(Request $request, $id)
    {
        try {
            $appointment = VisitSchedule::findOrFail($id);
            // Parse the date and time
            $visitDateTime = Carbon::parse($request->visit_date . ' ' . $request->visit_time);
            $visitEndTime = $visitDateTime->copy()->addHours(2);

            // Validation checks:
            // 1. Check if the team is already assigned to another visit that overlaps with this time slot
            $existingTeamVisit = VisitSchedule::where('team_id', $request->team_id)
                ->where('visit_date', $visitDateTime->format('Y-m-d'))
                ->where('id', '!=', $id)
                ->where('status', 'scheduled')
                ->where(function ($query) use ($visitDateTime, $visitEndTime) {
                    // Check overlapping time slots
                    $query->where(function ($q) use ($visitDateTime, $visitEndTime) {
                        // New visit starts during an existing visit
                        $q->where(function ($innerQ) use ($visitDateTime) {
                            $innerQ->whereTime('visit_time', '<=', $visitDateTime->format('H:i:s'))
                                ->whereRaw("ADDTIME(visit_time, '02:00:00') > ?", [$visitDateTime->format('H:i:s')]);
                        });
                        // OR Existing visit starts during our new visit
                        $q->orWhere(function ($innerQ) use ($visitDateTime, $visitEndTime) {
                            $innerQ->whereTime('visit_time', '>=', $visitDateTime->format('H:i:s'))
                                ->whereTime('visit_time', '<', $visitEndTime->format('H:i:s'));
                        });
                    });
                })
                ->exists();

            if ($existingTeamVisit) {
                return redirect()->back()->with('error', 'Selected team is already booked during this time slot or there is a scheduling conflict within the 2-hour visit window.');
            }

            // We don't need to check for all visits overlapping - we only need to ensure
            // the selected team isn't double-booked, which is handled by the first validation check.
            // First validation is sufficient - we don't need a second global time check.

            // Update the appointment
            $appointment->update([
                'visit_date' => $visitDateTime->format('Y-m-d'),
                'visit_time' => $visitDateTime->format('H:i:s'),
                'team_id' => $request->team_id
            ]);

            return redirect()->back()->with('success', 'Appointment updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update appointment: ' . $e->getMessage());
        }
    }

    /**
     * Display team schedules
     */
    public function teamSchedules(Request $request)
    {
        $teams = Team::with(['leader'])->get();

        // Transform the teams collection to include paginated visit schedules
        $teams = $teams->map(function ($team) use ($request) {
            // First create the base query
            $baseQuery = VisitSchedule::where('team_id', $team->id)
                ->with(['contract.customer'])
                ->orderBy('visit_date', 'asc');

            // Apply date filters to a clone of the base query
            $query = clone $baseQuery;

            // Handle month/year filter
            if ($request->filled('month') && $request->filled('year')) {
                $query->whereMonth('visit_date', $request->month)
                    ->whereYear('visit_date', $request->year);
            }
            // Handle single date filter
            elseif ($request->filled('date')) {
                $query->whereDate('visit_date', $request->date);
            }
            // Handle custom date range filter
            elseif ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereDate('visit_date', '>=', $request->from_date)
                    ->whereDate('visit_date', '<=', $request->to_date);
            }
            // Handle just from_date without to_date (filter from a date onwards)
            elseif ($request->filled('from_date')) {
                $query->whereDate('visit_date', '>=', $request->from_date);
            }
            // Handle just to_date without from_date (filter up to a date)
            elseif ($request->filled('to_date')) {
                $query->whereDate('visit_date', '<=', $request->to_date);
            }

            // Count total filtered records before pagination
            $team->totalSchedules = $query->count();

            // Apply pagination after count
            $team->paginatedSchedules = $query->paginate(10, ['*'], 'page_' . $team->id)
                ->appends($request->except('page_' . $team->id));

            return $team;
        });

        // Get all active clients with approved contracts
        $clients = client::whereHas('contracts', function ($query) {
            $query->where('contract_status', 'approved');
        })->get();

        return view('managers.technical.teams.schedules', compact('teams', 'clients'));
    }

    /**
     * Display all client tickets
     */
    public function clientTickets(Request $request)
    {
        $query = Tiket::with(['customer', 'solver']);

        // Search by ticket number, title, or client name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tiket_number', 'like', "%{$search}%")
                    ->orWhere('tiket_title', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('managers.technical.client_tickets', compact('tickets'));
    }

    /**
     * Display specific client ticket
     */
    public function showClientTicket($id)
    {
        $ticket = Tiket::with(['customer', 'solver', 'replies'])
            ->findOrFail($id);

        return view('managers.technical.client_tikets', compact('ticket'));
    }

    /**
     * Solve a client ticket with response
     */
    public function solveClientTicket(Request $request, $id)
    {
        $ticket = Tiket::findOrFail($id);

        // Create a new reply
        $ticket->replies()->create([
            'reply' => $request->response,
            'user_id' => Auth::id(),
        ]);

        // Update ticket status
        $ticket->update([
            'status' => $request->status,
            'solver_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Ticket has been updated successfully');
    }

    /**
     * View visit report
     */
    public function viewReport($id)
    {
        $visit = VisitSchedule::findOrFail($id);
        return view('managers.technical.visit_report', compact('visit'));
    }

    public function viewContractDetails($id)
    {
        $contract = contracts::with('type')->findOrFail($id);
        $visitSchedules = $contract->visitSchedules()->paginate(10);
        return view('managers.technical.contract_details', compact('contract', 'visitSchedules'));
    }

    /**
     * Schedule a new visit for a contract
     */
    public function scheduleVisit(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'client_id' => 'required|exists:clients,id',
                'contract_id' => 'required|exists:contracts,id',
                'branch_id' => 'nullable|exists:branchs,id',
                'visit_type' => 'required|in:regular,complementary,free,emergency',
                'visit_date' => 'required|date|after_or_equal:today',
                'visit_time' => 'required',
                'team_id' => 'required|exists:teams,id',
            ]);

            // Get the contract
            $contract = contracts::findOrFail($request->contract_id);

            // Count existing visits for this contract
            $visitCount = VisitSchedule::where('contract_id', $contract->id)->count();

            // Create the visit
            $visit = new VisitSchedule();
            $visit->contract_id = $contract->id;
            $visit->visit_date = $request->visit_date;
            $visit->visit_time = $request->visit_time;
            $visit->visit_type = $request->visit_type;
            $visit->team_id = $request->team_id;
            $visit->status = 'scheduled';
            $visit->visit_number = $visitCount + 1;

            // If branch is selected, validate it belongs to the contract
            if ($request->branch_id) {
                $branch = branchs::where('id', $request->branch_id)
                    ->where('contracts_id', $contract->id)
                    ->firstOrFail();
                $visit->branch_id = $branch->id;
            }

            $visit->save();

            return redirect()->back()->with('success', 'Visit scheduled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to schedule visit: ' . $e->getMessage());
        }
    }

    public function getClientContracts($clientId)
    {
        $contracts = contracts::where('customer_id', $clientId)
            ->where('contract_status', 'approved')
            ->select('id', 'contract_number')
            ->get();

        return response()->json($contracts);
    }

    public function getContractBranches($contractId)
    {
        $contract = contracts::findOrFail($contractId);
        $branches = $contract->branchs()
            ->select('id', 'branch_name as name')
            ->get();

        return response()->json($branches);
    }

    public function visitChangeRequests()
    {
        $pendingVisits = VisitSchedule::where('status', 'pending')
            ->with(['contract.customer', 'contract.branchs'])
            ->latest()
            ->paginate(10);
        return view('managers.technical.visit_change_requests', compact('pendingVisits'));
    }

    public function updateVisit(Request $request, $id)
    {
        $visit = VisitSchedule::findOrFail($id);

        // Update the visit schedule with the new date and time
        $visit->update([
            'visit_date' => $request->visit_date,
            'visit_time' => $request->visit_time,
            'status' => 'scheduled'
        ]);

        return redirect()->back()->with('success', 'Visit schedule has been updated successfully.');
    }

    /**
     * Display visits for a specific contract (optionally filtered by branch)
     */
    public function contractVisits(Request $request, $contractId, $branchId = null)
    {
        // Get the contract with customer and branches
        $contract = contracts::with(['customer', 'branchs'])
            ->where('contract_status', 'approved')
            ->findOrFail($contractId);

        // Base query for visits of this contract
        $visitsQuery = VisitSchedule::with(['team', 'branch'])
            ->where('contract_id', $contract->id);

        // If branch ID is provided and not 'all', filter by branch
        if ($branchId && $branchId != 'all') {
            if ($branchId == 'main') {
                // Check if there are any visits without a branch assigned
                $mainVisitsCheck = VisitSchedule::where('contract_id', $contract->id)
                    ->where(function ($q) {
                        $q->whereNull('branch_id')
                            ->orWhere('branch_id', 0);
                    })->count();

                if ($mainVisitsCheck > 0) {
                    // Filter for main location (no branch_id or branch_id = 0)
                    $visitsQuery->where(function ($query) {
                        $query->whereNull('branch_id')
                            ->orWhere('branch_id', 0);
                    });
                }
            } else {
                $visitsQuery->where('branch_id', $branchId);
                $branch = branchs::findOrFail($branchId);
            }
        } else {
            $branch = null;
        }

        // Apply date filters if provided
        if ($request->filled('start_date')) {
            $visitsQuery->whereDate('visit_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $visitsQuery->whereDate('visit_date', '<=', $request->end_date);
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $visitsQuery->where('status', $request->status);
        }

        // Order visits by visit_number, then date and time
        $visits = $visitsQuery->orderBy('visit_number', 'asc')
            ->orderBy('visit_date', 'desc')
            ->orderBy('visit_time', 'desc')
            ->paginate(12)
            ->withQueryString();

        // Get teams for edit modal
        $teams = Team::all();

        // Statistics for this contract/branch
        $totalVisits = $visitsQuery->count();
        $completedVisits = $visitsQuery->where('status', 'completed')->count();
        $pendingVisits = $visitsQuery->where('status', 'scheduled')->count();
        $cancelledVisits = $visitsQuery->where('status', 'cancelled')->count();

        return view('managers.technical.contract_visits', compact(
            'contract',
            'branch',
            'visits',
            'teams',
            'totalVisits',
            'completedVisits',
            'pendingVisits',
            'cancelledVisits'
        ));
    }
}
