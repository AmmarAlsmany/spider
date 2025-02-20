<?php

namespace App\Services;

use App\Models\contracts;
use App\Models\Team;
use App\Models\VisitSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VisitScheduleService
{
    private const VISIT_DURATION_HOURS = 2;
    private static array $timeSlots = [
        8,  // 8:00 AM - 10:00 AM
        10, // 10:00 AM - 12:00 PM
        12, // 12:00 PM - 2:00 PM
        14,  // 2:00 PM - 4:00 PM
        16, // 4:00 PM - 6:00 PM
    ];

    private function generateVisitDates(Carbon $startDate, int $numberOfVisits, array $existingBranchDates = []): array
    {
        $visitDates = [];
        $currentDate = $startDate->copy();
        $usedDates = []; // Track used dates to ensure one visit per day

        while (count($visitDates) < $numberOfVisits) {
            // Skip Fridays
            if ($currentDate->isFriday()) {
                $currentDate->addDay();
                continue;
            }

            $dateString = $currentDate->format('Y-m-d');
            
            // Skip if we already scheduled a visit for this day
            if (in_array($dateString, $usedDates)) {
                $currentDate->addDay();
                continue;
            }
            
            // Try to find an available time slot for this day
            foreach (self::$timeSlots as $timeSlot) {
                $visitTime = $currentDate->copy()->setHour($timeSlot)->setMinute(0)->setSecond(0);
                
                if (!$visitTime->isPast()) {
                    $fullDateTime = $visitTime->format('Y-m-d H:i:s');
                    if (!in_array($fullDateTime, $existingBranchDates)) {
                        $visitDates[] = $visitTime;
                        $usedDates[] = $dateString;
                        break; // Only one visit per day
                    }
                }
            }

            $currentDate->addDay();
            
            // Add safety check to prevent infinite loop
            if ($currentDate->diffInDays($startDate) > 365) {
                throw new \Exception('Unable to schedule all visits within one year');
            }
        }

        return $visitDates;
    }

    private function findAvailableTeamAndSlot($visitDate, array $usedTeamSlots)
    {
        $teams = Team::where('status', '=', 'active')->get();
        
        // Get all visits for this date to avoid scheduling at the same time
        $existingVisits = VisitSchedule::whereDate('visit_date', $visitDate)
            ->pluck('visit_time')
            ->toArray();

        foreach (self::$timeSlots as $timeSlot) {
            $timeString = sprintf('%02d:00:00', $timeSlot);
            
            // Skip if this time slot is already used on this date
            if (in_array($timeString, $existingVisits)) {
                continue;
            }

            // Randomize teams to distribute workload
            $shuffledTeams = $teams->shuffle();
            
            foreach ($shuffledTeams as $team) {
                // Skip if this team is already assigned for this date
                $teamDateKey = $team->id . '_' . $visitDate;
                if (isset($usedTeamSlots[$teamDateKey])) {
                    continue;
                }

                if ($this->isTeamAvailable($team, $visitDate, $timeString)) {
                    return [
                        'team' => $team,
                        'time' => $timeString,
                        'slot' => $timeSlot
                    ];
                }
            }
        }

        return null;
    }

    public function createVisitSchedule(contracts $contract)
    {
        try {
            DB::beginTransaction();
            $numberOfVisits = $contract->number_of_visits;
            $isMultiBranch = $contract->is_multi_branch === 'yes';
            if ($isMultiBranch) {
                $branches = $contract->branchs;
                if ($branches->isEmpty()) {
                    throw new \Exception('Contract must have at least one branch');
                }

                $branchVisitDates = [];
                $schedules = [];

                // First, generate all visit dates for each branch
                foreach ($branches as $branch) {
                    $startDate = Carbon::parse($contract->contract_start_date);
                    $existingVisits = VisitSchedule::where('branch_id', $branch->id)
                        ->where('status', '!=', 'cancelled')
                        ->get()
                        ->map(function ($visit) {
                            return $visit->visit_date . ' ' . $visit->visit_time;
                        })
                        ->toArray();

                    $branchVisitDates[$branch->id] = $this->generateVisitDates(
                        $startDate,
                        $numberOfVisits,
                        $existingVisits
                    );
                }

                // Then, schedule visits for each date
                foreach ($branchVisitDates as $branchId => $visitDates) {
                    $usedTeamSlots = [];

                    foreach ($visitDates as $index => $visitDateTime) {
                        $dateString = $visitDateTime->format('Y-m-d');

                        // Find available team and time slot
                        $available = $this->findAvailableTeamAndSlot($dateString, $usedTeamSlots);

                        if (!$available) {
                            DB::rollBack();
                            throw new \Exception('No available team/slot found for visit on ' . $dateString);
                        }

                        try {
                            // Mark this team as used for this date
                            $teamDateKey = $available['team']->id . '_' . $dateString;
                            $usedTeamSlots[$teamDateKey] = true;

                            // Create visit schedule
                            $visitSchedule = new VisitSchedule();
                            $visitSchedule->contract_id = $contract->id;
                            $visitSchedule->branch_id = $branchId;
                            $visitSchedule->team_id = $available['team']->id;
                            $visitSchedule->visit_date = $dateString;
                            $visitSchedule->visit_time = $available['time'];
                            $visitSchedule->status = 'scheduled';
                            $visitSchedule->visit_number = $index + 1;
                            $visitSchedule->visit_type = 'regular';
                            $visitSchedule->save();

                            $schedules[] = $visitSchedule;
                            
                            // Log the creation
                            Log::info('Visit schedule created: ' . $visitSchedule->id);
                            Log::info('Visit schedule details: ' . $visitSchedule->toJson());
                        } catch (\Exception $e) {
                            DB::rollBack();
                            throw $e;
                        }
                    }
                }
            } else {
                $startDate = Carbon::parse($contract->contract_start_date);
                $visitDates = $this->generateVisitDates($startDate, $numberOfVisits, []);
                $usedTeamSlots = [];
                foreach ($visitDates as $index => $visitDateTime) {
                    $dateString = $visitDateTime->format('Y-m-d');

                    // Find available team and time slot
                    $available = $this->findAvailableTeamAndSlot($dateString, $usedTeamSlots);

                    if (!$available) {
                        DB::rollBack();
                        throw new \Exception('No available team/slot found for visit on ' . $dateString);
                    }

                    // Mark this team as used for this date
                    $teamDateKey = $available['team']->id . '_' . $dateString;
                    $usedTeamSlots[$teamDateKey] = true;

                    // Create visit schedule
                    $visitSchedule = new VisitSchedule();
                    $visitSchedule->contract_id = $contract->id;
                    $visitSchedule->team_id = $available['team']->id;
                    $visitSchedule->visit_date = $dateString;
                    $visitSchedule->visit_time = $available['time'];
                    $visitSchedule->status = 'scheduled';
                    $visitSchedule->visit_number = $index + 1;
                    $visitSchedule->visit_type = 'regular';
                    $visitSchedule->save();
                    $schedules[] = $visitSchedule;
                }
            }

            DB::commit();
            return $schedules;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function isTeamAvailable(Team $team, $visitDate, $visitTime)
    {
        $visitStart = Carbon::parse($visitDate . ' ' . $visitTime);
        $visitEnd = $visitStart->copy()->addHours(self::VISIT_DURATION_HOURS);

        return !VisitSchedule::where('team_id', $team->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($visitStart, $visitEnd) {
                $query->where(function ($q) use ($visitStart, $visitEnd) {
                    $q->whereRaw(
                        "CONCAT(visit_date, ' ', visit_time) <= ?",
                        [$visitEnd->format('Y-m-d H:i:s')]
                    )
                        ->whereRaw(
                            "DATE_ADD(CONCAT(visit_date, ' ', visit_time), 
                          INTERVAL ? HOUR) >= ?",
                            [self::VISIT_DURATION_HOURS, $visitStart->format('Y-m-d H:i:s')]
                        );
                });
            })
            ->exists();
    }
}
