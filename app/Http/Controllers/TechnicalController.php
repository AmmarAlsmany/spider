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

class TechnicalController extends Controller
{
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

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'team_leader_id' => 'required|exists:users,id',
                'description' => 'nullable|string',
                'members' => 'array',
                'members.*' => 'exists:users,id'
            ]);

            // Check if the team leader is already assigned to another team
            if (Team::where('team_leader_id', $validated['team_leader_id'])->exists()) {
                return redirect()->back()->with('error', 'This user is already a team leader in another team');
            }

            // Create the team
            $team = new Team();
            $team->name = $validated['name'];
            $team->team_leader_id = $validated['team_leader_id'];

            $team->description = $validated['description'];
            $team->save();

            // Attach members if any
            if (!empty($validated['members'])) {
                $team->members()->attach($validated['members']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating team: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Team created successfully');
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
                'members' => 'sometimes|array',
                'members.*' => 'exists:users,id'
            ]);

            // If changing team leader, check if new leader is not already leading another team
            if (isset($validated['team_leader_id']) && $validated['team_leader_id'] !== $team->team_leader_id) {
                if (Team::where('team_leader_id', $validated['team_leader_id'])->exists()) {
                    return redirect()->back()->with('error', 'This user is already a team leader in another team');
                }
            }

            $team->update($validated);

            // Update members if provided
            if (isset($validated['members'])) {
                $team->members()->sync($validated['members']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating team: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Team updated successfully');
    }

    public function delete($id)
    {
        $team = Team::findOrFail($id);

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
        ])->whereHas('contract', function($q) {
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

        // Get paginated visits for each contract
        $contractVisits = [];
        foreach ($contracts as $contract) {
            $contractVisits[$contract->id] = VisitSchedule::with(['team', 'branch'])
                ->where('contract_id', $contract->id)
                ->orderBy('visit_date', 'desc')
                ->paginate(5, ['*'], 'contract_' . $contract->id . '_page');
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
            'pendingVisits',
            'todayVisits',
            'todayCompletedVisits',
            'teams',
            'clients'
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

        $visits = $query->orderBy('visit_date', 'desc')
            ->orderBy('visit_time', 'desc')
            ->paginate(10);

        $teams = Team::where('type', '=', 'scattered')
            ->where('status', '=', 'active')
            ->get();

        return view('managers.technical.completed_visits', compact('visits', 'teams'));
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

        $visits = $query->orderBy('visit_date', 'desc')
            ->orderBy('visit_time', 'desc')
            ->paginate(10);

        $teams = Team::where('type', '=', 'scattered')
            ->where('status', '=', 'active')
            ->get();

        return view('managers.technical.cancelled_visits', compact('visits', 'teams'));
    }

    public function rescheduleVisit(Request $request, $id)
    {
        try {
            $request->validate([
                'visit_date' => 'required',
                'visit_time' => 'required',
            ]);

            $visit = VisitSchedule::findOrFail($id);
            $visitDateTime = Carbon::parse($request->visit_date . ' ' . $request->visit_time);

            // Get the hour and day for warning purposes
            $hour = $visitDateTime->hour;
            $dayOfWeek = $visitDateTime->dayOfWeek;

            // Prepare warning messages if needed
            $warnings = [];
            if ($hour < 8 || $hour >= 14) {
                $warnings[] = 'This appointment is scheduled outside regular working hours (8 AM to 2 PM).';
            }
            if ($dayOfWeek === Carbon::FRIDAY) {
                $warnings[] = 'This appointment is scheduled on a Friday.';
            }

            // Check if the team is already assigned to another visit at the same time
            $existingVisit = VisitSchedule::where('team_id', $visit->team_id)
                ->where('visit_date', $visitDateTime->format('Y-m-d'))
                ->where('id', '!=', $id)
                ->where('status', 'scheduled')
                ->where(function ($query) use ($visitDateTime) {
                    $query->whereBetween('visit_time', [
                        $visitDateTime->format('H:i:s'),
                        $visitDateTime->copy()->addHours(2)->format('H:i:s')
                    ]);
                })
                ->exists();

            if ($existingVisit) {
                return redirect()->back()->with('error', 'Selected team is already booked during this time slot.');
            }

            // Update the visit schedule
            $visit->update([
                'visit_date' => $visitDateTime->format('Y-m-d'),
                'visit_time' => $visitDateTime->format('H:i:s')
            ]);

            // If there are warnings, include them in the success message
            $message = 'Visit rescheduled successfully.';
            if (!empty($warnings)) {
                $message .= ' Note: ' . implode(' ', $warnings);
            }

            return redirect()->back()->with('success', $message);
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

            // Get the hour for informational purposes
            $hour = $visitDateTime->hour;
            $dayOfWeek = $visitDateTime->dayOfWeek;

            // Prepare warning messages if needed
            $warnings = [];
            if ($hour < 8 || $hour >= 14) {
                $warnings[] = 'Note: This appointment is scheduled outside regular working hours (8 AM to 2 PM).';
            }
            if ($dayOfWeek === Carbon::FRIDAY) {
                $warnings[] = 'Note: This appointment is scheduled on a Friday.';
            }

            // If there are warnings, add them as a flash message but continue with the scheduling
            if (!empty($warnings)) {
                session()->flash('warning', implode(' ', $warnings));
            }

            // Check if the team is already assigned to another visit at the same time
            $existingVisit = VisitSchedule::where('team_id', $request->team_id)
                ->where('visit_date', $visitDateTime->format('Y-m-d'))
                ->where('id', '!=', $id)
                ->where('status', 'scheduled')
                ->where(function ($query) use ($visitDateTime) {
                    $query->whereBetween('visit_time', [
                        $visitDateTime->format('H:i:s'),
                        $visitDateTime->copy()->addHours(2)->format('H:i:s')
                    ]);
                })
                ->exists();

            if ($existingVisit) {
                return redirect()->back()->with('error', 'Selected team is already booked during this time slot.');
            }

            // Update the appointment
            $appointment->update([
                'visit_date' => $request->visit_date,
                'visit_time' => $request->visit_time,
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
            $query = VisitSchedule::where('team_id', $team->id)
                ->with(['contract.customer'])
                ->orderBy('visit_date', 'asc');

            if ($request->has('month') && $request->has('year')) {
                $query->whereMonth('visit_date', $request->month)
                      ->whereYear('visit_date', $request->year);
            }
            if ($request->has('date')) {
                $query->whereDate('visit_date', $request->date);
            }

            $team->paginatedSchedules = $query->paginate(10, ['*'], 'page_'.$team->id);
            $team->totalSchedules = $query->count();
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
}
