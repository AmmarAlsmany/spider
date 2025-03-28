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
    ];

    /**
     * Calculate visit frequency based on the total number of visits
     * 
     * @param int $numberOfVisits Total number of visits in the contract
     * @return array Returns [monthInterval, visitsPerInterval]
     */
    private function calculateVisitFrequency(int $numberOfVisits): array
    {
        // Default: 1 visit per month
        $monthInterval = 1;
        $visitsPerInterval = 1;

        // Determine frequency based on number of visits
        switch (true) {
            case $numberOfVisits == 24: // 2 visits per month
                $monthInterval = 1;
                $visitsPerInterval = 2;
                break;
            case $numberOfVisits == 12: // 1 visit per month
                $monthInterval = 1;
                $visitsPerInterval = 1;
                break;
            case $numberOfVisits == 6: // 1 visit every 2 months
                $monthInterval = 2;
                $visitsPerInterval = 1;
                break;
            case $numberOfVisits == 4: // 1 visit every 3 months
                $monthInterval = 3;
                $visitsPerInterval = 1;
                break;
            default:
                // For other numbers, calculate the best distribution
                // Contract duration is typically 1 year (12 months)
                if ($numberOfVisits > 12) {
                    // More than monthly visits needed
                    $monthInterval = 1;
                    $visitsPerInterval = ceil($numberOfVisits / 12);
                } else {
                    // Less than monthly visits needed
                    $monthInterval = floor(12 / $numberOfVisits);
                    $visitsPerInterval = 1;
                }
        }

        return [$monthInterval, $visitsPerInterval];
    }

    private function generateVisitDates(Carbon $startDate, int $numberOfVisits, array $existingBranchDates = []): array
    {
        $visitDates = [];
        $currentDate = $startDate->copy();

        // Calculate visit frequency
        [$monthInterval, $visitsPerInterval] = $this->calculateVisitFrequency($numberOfVisits);

        // Calculate how many intervals we need
        $totalIntervals = ceil($numberOfVisits / $visitsPerInterval);

        for ($interval = 0; $interval < $totalIntervals; $interval++) {
            // Set the month for this interval
            $intervalDate = $currentDate->copy()->addMonths($interval * $monthInterval);

            // Schedule the visits for this interval
            $visitsThisInterval = min($visitsPerInterval, $numberOfVisits - count($visitDates));
            $usedDatesThisMonth = [];

            // Try to distribute visits evenly within the month
            $weekSpacing = floor(4 / $visitsThisInterval);

            for ($visitNum = 0; $visitNum < $visitsThisInterval; $visitNum++) {
                $weekOffset = $visitNum * max(1, $weekSpacing);
                $visitAttemptDate = $intervalDate->copy()->addWeeks($weekOffset);

                // Find a suitable day for this visit
                $visitDate = $this->findSuitableDayForVisit(
                    $visitAttemptDate,
                    $usedDatesThisMonth,
                    $existingBranchDates
                );

                if ($visitDate) {
                    $visitDates[] = $visitDate;
                    $usedDatesThisMonth[] = $visitDate->format('Y-m-d');
                }
            }

            // Safety check to prevent infinite loop
            if (count($visitDates) >= $numberOfVisits) {
                break;
            }
        }

        // If we couldn't schedule all visits, try to fill in the gaps
        if (count($visitDates) < $numberOfVisits) {
            $remainingVisits = $numberOfVisits - count($visitDates);
            $fillerDates = $this->fillRemainingVisits(
                $startDate,
                $remainingVisits,
                $visitDates,
                $existingBranchDates
            );

            $visitDates = array_merge($visitDates, $fillerDates);
        }

        // Sort visits by date
        usort($visitDates, function ($a, $b) {
            return $a->timestamp - $b->timestamp;
        });

        return $visitDates;
    }

    /**
     * Find a suitable day for a visit, avoiding Fridays and already used dates
     */
    private function findSuitableDayForVisit(Carbon $attemptDate, array $usedDates, array $existingBranchDates): ?Carbon
    {
        $maxAttempts = 10; // Prevent infinite loops
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            // Skip Fridays
            if ($attemptDate->isFriday()) {
                $attemptDate->addDay();
                $attempts++;
                continue;
            }

            $dateString = $attemptDate->format('Y-m-d');

            // Skip if we already scheduled a visit for this day
            if (in_array($dateString, $usedDates)) {
                $attemptDate->addDay();
                $attempts++;
                continue;
            }

            // Try to find an available time slot for this day
            foreach (self::$timeSlots as $timeSlot) {
                $visitTime = $attemptDate->copy()->setHour($timeSlot)->setMinute(0)->setSecond(0);

                if (!$visitTime->isPast()) {
                    $fullDateTime = $visitTime->format('Y-m-d H:i:s');
                    if (!in_array($fullDateTime, $existingBranchDates)) {
                        return $visitTime;
                    }
                }
            }

            // No available slots on this day, try the next day
            $attemptDate->addDay();
            $attempts++;
        }

        return null; // Could not find a suitable day
    }

    /**
     * Fill in any remaining visits that couldn't be scheduled with the main algorithm
     */
    private function fillRemainingVisits(Carbon $startDate, int $remainingVisits, array $scheduledVisits, array $existingBranchDates): array
    {
        $additionalDates = [];
        $usedDates = array_map(function ($date) {
            return $date->format('Y-m-d');
        }, $scheduledVisits);

        $currentDate = $startDate->copy();
        $endDate = $startDate->copy()->addYear();

        while (count($additionalDates) < $remainingVisits && $currentDate->lt($endDate)) {
            // Skip if this date is already used
            if (in_array($currentDate->format('Y-m-d'), $usedDates)) {
                $currentDate->addDay();
                continue;
            }

            // Skip Fridays
            if ($currentDate->isFriday()) {
                $currentDate->addDay();
                continue;
            }

            // Try to find an available time slot
            foreach (self::$timeSlots as $timeSlot) {
                $visitTime = $currentDate->copy()->setHour($timeSlot)->setMinute(0)->setSecond(0);

                if (!$visitTime->isPast()) {
                    $fullDateTime = $visitTime->format('Y-m-d H:i:s');
                    if (!in_array($fullDateTime, $existingBranchDates)) {
                        $additionalDates[] = $visitTime;
                        $usedDates[] = $currentDate->format('Y-m-d');
                        break; // Only one visit per day
                    }
                }
            }

            $currentDate->addDay();
        }

        return $additionalDates;
    }

    private function findAvailableTeamAndSlot($visitDate, array $usedTeamSlots)
    {
        $teams = Team::where('status', '=', 'active')->get();
        
        if ($teams->isEmpty()) {
            Log::warning("No active teams found when scheduling visits for date: $visitDate");
            return null;
        }

        // Get all visits for this date to avoid scheduling at the same time
        $existingVisits = VisitSchedule::whereDate('visit_date', $visitDate)
            ->pluck('visit_time')
            ->toArray();
        
        Log::info("Scheduling for date: $visitDate. Existing visits: " . json_encode($existingVisits));

        foreach (self::$timeSlots as $timeSlot) {
            $timeString = sprintf('%02d:00:00', $timeSlot);

            // Skip if this time slot is already used on this date
            if (in_array($timeString, $existingVisits)) {
                Log::info("Time slot $timeString is already used on $visitDate");
                continue;
            }

            // Randomize teams to distribute workload
            $shuffledTeams = $teams->shuffle();

            foreach ($shuffledTeams as $team) {
                // Skip if this team is already assigned for this date
                $teamDateKey = $team->id . '_' . $visitDate;
                if (isset($usedTeamSlots[$teamDateKey])) {
                    Log::info("Team {$team->name} (ID: {$team->id}) is already assigned for date $visitDate");
                    continue;
                }

                $isAvailable = $this->isTeamAvailable($team, $visitDate, $timeString);
                Log::info("Team {$team->name} (ID: {$team->id}) availability for $visitDate $timeString: " . ($isAvailable ? 'Available' : 'Not available'));
                
                if ($isAvailable) {
                    return [
                        'team' => $team,
                        'time' => $timeString,
                        'slot' => $timeSlot
                    ];
                }
            }
        }

        // If we get here, no standard slot was available - try a fallback time slot
        $fallbackSlots = [15, 16]; // 3:00 PM and 4:00 PM as fallback options
        
        foreach ($fallbackSlots as $fallbackSlot) {
            $timeString = sprintf('%02d:00:00', $fallbackSlot);
            
            // Skip if this fallback slot is already used
            if (in_array($timeString, $existingVisits)) {
                continue;
            }
            
            foreach ($teams as $team) {
                $teamDateKey = $team->id . '_' . $visitDate;
                if (isset($usedTeamSlots[$teamDateKey])) {
                    continue;
                }
                
                if ($this->isTeamAvailable($team, $visitDate, $timeString)) {
                    Log::info("Using fallback slot $timeString for team {$team->name} on $visitDate");
                    return [
                        'team' => $team,
                        'time' => $timeString,
                        'slot' => $fallbackSlot
                    ];
                }
            }
        }

        Log::warning("No available team/slot found for visit on $visitDate after trying all options");
        return null;
    }

    private function isTeamAvailable(Team $team, $visitDate, $visitTime)
    {
        $visitStart = Carbon::parse($visitDate . ' ' . $visitTime);
        $visitEnd = $visitStart->copy()->addHours(self::VISIT_DURATION_HOURS);
        
        // First check if the team exists and is active
        if (!$team || $team->status !== 'active') {
            Log::info("Team {$team->name} is not active");
            return false;
        }
        
        // Convert to database-friendly format
        $visitStartStr = $visitStart->format('Y-m-d H:i:s');
        $visitEndStr = $visitEnd->format('Y-m-d H:i:s');

        // Check for conflicts with existing schedules
        $conflicts = VisitSchedule::where('team_id', $team->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($visitStartStr, $visitEndStr) {
                // Find any schedule that overlaps with our proposed time window
                $query->where(function ($q) use ($visitStartStr, $visitEndStr) {
                    // Schedule starts during our window
                    $q->whereRaw("CONCAT(visit_date, ' ', visit_time) >= ?", [$visitStartStr])
                      ->whereRaw("CONCAT(visit_date, ' ', visit_time) < ?", [$visitEndStr]);
                })->orWhere(function ($q) use ($visitStartStr, $visitEndStr) {
                    // Schedule ends during our window
                    $q->whereRaw(
                        "DATE_ADD(CONCAT(visit_date, ' ', visit_time), INTERVAL ? HOUR) > ?",
                        [self::VISIT_DURATION_HOURS, $visitStartStr]
                    )->whereRaw(
                        "CONCAT(visit_date, ' ', visit_time) <= ?",
                        [$visitStartStr]
                    );
                });
            })
            ->get();
            
        if ($conflicts->count() > 0) {
            Log::info("Team {$team->name} has conflicts for $visitDate $visitTime: " . $conflicts->count() . " conflicting visits");
            foreach ($conflicts as $conflict) {
                Log::info("Conflict: Visit ID {$conflict->id} on {$conflict->visit_date} at {$conflict->visit_time}");
            }
            return false;
        }

        return true;
    }

    public function createVisitSchedule(contracts $contract)
    {
        try {
            DB::beginTransaction();
            $numberOfVisits = $contract->number_of_visits;
            $branches = $contract->branchs;
            if ($branches->isEmpty()) {
                throw new \Exception('Contract must have at least one branch');
            }

            // Use visit_start_date if available, otherwise fallback to contract_start_date
            $startDate = $contract->visit_start_date 
                ? Carbon::parse($contract->visit_start_date) 
                : Carbon::parse($contract->contract_start_date);

            $branchVisitDates = [];
            $schedules = [];

            // First, generate all visit dates for each branch
            foreach ($branches as $branch) {
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

            DB::commit();
            return $schedules;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancel scheduled visits for a contract when it's stopped
     * 
     * @param contracts $contract The contract that has been stopped
     * @return int Number of scheduled visits that were cancelled
     */
    public function cancelContractVisits(contracts $contract): int
    {
        try {
            DB::beginTransaction();
            
            // Find only visits with 'scheduled' status for this contract
            $scheduledVisits = VisitSchedule::where('contract_id', $contract->id)
                ->where('status', 'scheduled')
                ->get();
            
            $cancelledCount = 0;
            
            // Update each scheduled visit to cancelled status
            foreach ($scheduledVisits as $visit) {
                $visit->status = 'cancelled';
                $visit->save();
                $cancelledCount++;
                
                // Log the cancellation
                Log::info('Scheduled visit cancelled due to contract stop: Visit ID ' . $visit->id);
            }
            
            DB::commit();
            return $cancelledCount;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling scheduled visits for stopped contract: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Restore cancelled visits for a contract when it's reactivated (changed from stopped to approved)
     * 
     * @param contracts $contract The contract that has been reactivated
     * @return int Number of visits that were restored
     */
    public function restoreContractVisits(contracts $contract): int
    {
        try {
            DB::beginTransaction();
            
            // Find only visits with 'cancelled' status for this contract that were cancelled due to contract stop
            // We'll only restore visits that are still in the future
            $cancelledVisits = VisitSchedule::where('contract_id', $contract->id)
                ->where('status', 'cancelled')
                ->whereDate('visit_date', '>=', now()->format('Y-m-d'))
                ->get();
            
            $restoredCount = 0;
            
            // Update each cancelled visit back to scheduled status
            foreach ($cancelledVisits as $visit) {
                $visit->status = 'scheduled';
                $visit->save();
                $restoredCount++;
                
                // Log the restoration
                Log::info('Visit restored due to contract reactivation: Visit ID ' . $visit->id);
            }
            
            DB::commit();
            return $restoredCount;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring visits for reactivated contract: ' . $e->getMessage());
            throw $e;
        }
    }
}
