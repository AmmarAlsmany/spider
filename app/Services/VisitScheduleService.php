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
        18, // 6:00 PM - 8:00 PM
        20, // 8:00 PM - 10:00 PM
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
            case $numberOfVisits == 3: // 1 visit every 4 months
                $monthInterval = 4;
                $visitsPerInterval = 1;
                break;
            case $numberOfVisits == 2: // Special handling for 2 visits - give them more spacing
                $monthInterval = 4; // Changed from 6 to 4 months to ensure both visits fit
                $visitsPerInterval = 1;
                break;
            default:
                // For other numbers, calculate the best distribution
                if ($numberOfVisits > 12) {
                    // More than monthly visits needed
                    $monthInterval = 1;
                    $visitsPerInterval = ceil($numberOfVisits / 12);
                } else if ($numberOfVisits <= 0) {
                    // Safety check for invalid inputs
                    $monthInterval = 1;
                    $visitsPerInterval = 1;
                    Log::warning("Invalid number of visits: $numberOfVisits, defaulting to 1");
                } else {
                    // Less than monthly visits needed
                    $monthInterval = max(1, floor(12 / $numberOfVisits));
                    $visitsPerInterval = 1;
                }
        }

        Log::info("Visit frequency calculated: numVisits=$numberOfVisits, monthInterval=$monthInterval, visitsPerInterval=$visitsPerInterval");
        return [$monthInterval, $visitsPerInterval];
    }

    private function generateVisitDates(Carbon $startDate, Carbon $endDate, int $numberOfVisits, array $existingBranchDates = []): array
    {
        $visitDates = [];
        $currentDate = $startDate->copy();

        // Debug logging
        Log::info("Generating visits for: Start: $startDate, End: $endDate, Requested visits: $numberOfVisits");

        // Special case for very few visits (1-2) - distribute evenly across contract period
        if ($numberOfVisits <= 2) {
            Log::info("Using special handling for small number of visits: $numberOfVisits");
            
            // Calculate contract duration in days
            $contractDuration = $startDate->diffInDays($endDate);
            Log::info("Contract duration: $contractDuration days");
            
            // For 1 visit, place it in the middle of the contract
            if ($numberOfVisits == 1) {
                $visitAttemptDate = $startDate->copy()->addDays((int)($contractDuration / 2));
                Log::info("Single visit attempt date: $visitAttemptDate");
                
                $visitDate = $this->findSuitableDayForVisit(
                    $visitAttemptDate,
                    [],
                    $existingBranchDates,
                    $endDate
                );
                
                if ($visitDate) {
                    $visitDates[] = $visitDate;
                    Log::info("Successfully scheduled single visit for: $visitDate");
                }
            }
            // For 2 visits, place them at 1/3 and 2/3 of the contract duration
            else if ($numberOfVisits == 2) {
                // First visit at 1/3 of contract duration
                $firstVisitDate = $startDate->copy()->addDays((int)($contractDuration / 3));
                Log::info("First visit attempt date: $firstVisitDate");
                
                $visit1 = $this->findSuitableDayForVisit(
                    $firstVisitDate,
                    [],
                    $existingBranchDates,
                    $endDate
                );
                
                if ($visit1) {
                    $visitDates[] = $visit1;
                    Log::info("Successfully scheduled first visit for: $visit1");
                    
                    // Second visit at 2/3 of contract duration
                    $secondVisitDate = $startDate->copy()->addDays((int)(2 * $contractDuration / 3));
                    Log::info("Second visit attempt date: $secondVisitDate");
                    
                    // Store the date string from the Carbon object for checking
                    $usedDateString = $visit1->format('Y-m-d');
                    $usedDates = [$usedDateString];
                    
                    $visit2 = $this->findSuitableDayForVisit(
                        $secondVisitDate,
                        $usedDates,
                        $existingBranchDates,
                        $endDate
                    );
                    
                    if ($visit2) {
                        $visitDates[] = $visit2;
                        Log::info("Successfully scheduled second visit for: $visit2");
                    } else {
                        Log::warning("Could not schedule second visit at preferred time, will try alternative method");
                    }
                }
            }
            
            // If we couldn't schedule all visits with the special handling, fall back to regular method
            if (count($visitDates) < $numberOfVisits) {
                Log::info("Special handling didn't schedule all visits, falling back to regular method");
            } else {
                // We successfully scheduled all visits with the special handling
                Log::info("Successfully scheduled all $numberOfVisits visits with special handling");
                
                // Sort visits by date and return them
                usort($visitDates, function ($a, $b) {
                    return $a->timestamp - $b->timestamp;
                });
                
                return $visitDates;
            }
        }

        // Continue with regular visit scheduling for larger numbers of visits
        // or if special handling failed to schedule all visits
        
        // Calculate visit frequency
        [$monthInterval, $visitsPerInterval] = $this->calculateVisitFrequency($numberOfVisits);
        Log::info("Visit frequency: monthInterval=$monthInterval, visitsPerInterval=$visitsPerInterval");

        // Calculate how many intervals we need
        $totalIntervals = ceil($numberOfVisits / $visitsPerInterval);
        Log::info("Total intervals calculated: $totalIntervals");

        for ($interval = 0; $interval < $totalIntervals; $interval++) {
            // Set the month for this interval
            $intervalDate = $currentDate->copy()->addMonths($interval * $monthInterval);
            Log::info("Processing interval $interval, date: $intervalDate");

            // If this interval would exceed the contract end date, break
            if ($intervalDate->gt($endDate)) {
                Log::info("Interval date $intervalDate exceeds end date $endDate, breaking");
                break;
            }

            // Schedule the visits for this interval
            $visitsThisInterval = min($visitsPerInterval, $numberOfVisits - count($visitDates));
            Log::info("Planning $visitsThisInterval visits for this interval");
            $usedDatesThisMonth = [];

            // Try to distribute visits evenly within the month
            $weekSpacing = floor(4 / $visitsThisInterval);
            Log::info("Week spacing: $weekSpacing");

            for ($visitNum = 0; $visitNum < $visitsThisInterval; $visitNum++) {
                $weekOffset = $visitNum * max(1, $weekSpacing);
                $visitAttemptDate = $intervalDate->copy()->addWeeks($weekOffset);
                Log::info("Visit $visitNum, attempt date: $visitAttemptDate");

                // Skip if this date exceeds the contract end date
                if ($visitAttemptDate->gt($endDate)) {
                    Log::info("Visit attempt date $visitAttemptDate exceeds end date $endDate, skipping");
                    continue;
                }

                // Find a suitable day for this visit
                $visitDate = $this->findSuitableDayForVisit(
                    $visitAttemptDate,
                    $usedDatesThisMonth,
                    $existingBranchDates,
                    $endDate
                );

                if ($visitDate) {
                    Log::info("Found suitable date: $visitDate");
                    $visitDates[] = $visitDate;
                    $usedDatesThisMonth[] = $visitDate->format('Y-m-d');
                } else {
                    Log::info("Could not find suitable date for visit $visitNum in interval $interval");
                }
            }

            // Safety check to prevent infinite loop
            if (count($visitDates) >= $numberOfVisits) {
                Log::info("Reached requested number of visits ($numberOfVisits), breaking early");
                break;
            }
        }

        // If we couldn't schedule all visits, try to fill in the gaps
        if (count($visitDates) < $numberOfVisits) {
            $remainingVisits = $numberOfVisits - count($visitDates);
            Log::info("Could not schedule all visits through main algorithm. Trying to fill $remainingVisits remaining visits");
            $fillerDates = $this->fillRemainingVisits(
                $startDate,
                $endDate,
                $remainingVisits,
                $visitDates,
                $existingBranchDates
            );

            Log::info("Filler algorithm found " . count($fillerDates) . " additional dates");
            $visitDates = array_merge($visitDates, $fillerDates);
        }

        // Sort visits by date
        usort($visitDates, function ($a, $b) {
            return $a->timestamp - $b->timestamp;
        });

        Log::info("Final visit count: " . count($visitDates) . " out of $numberOfVisits requested");
        return $visitDates;
    }

    /**
     * Find a suitable day for a visit, avoiding Fridays and already used dates
     */
    private function findSuitableDayForVisit(Carbon $attemptDate, array $usedDates, array $existingBranchDates, Carbon $endDate = null): ?Carbon
    {
        $maxAttempts = 20; // Increased from 10 to 20 to allow more flexibility
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            // Skip Fridays
            if ($attemptDate->isFriday()) {
                $attemptDate->addDay();
                $attempts++;
                continue;
            }

            // Skip if this date would exceed the contract end date
            if ($endDate && $attemptDate->gt($endDate)) {
                return null;
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

                // Check if this would exceed the contract end date
                if ($endDate && $visitTime->gt($endDate)) {
                    continue;
                }

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
    private function fillRemainingVisits(Carbon $startDate, Carbon $endDate, int $remainingVisits, array $scheduledVisits, array $existingBranchDates): array
    {
        $additionalDates = [];
        $usedDates = array_map(function ($date) {
            return $date->format('Y-m-d');
        }, $scheduledVisits);

        $currentDate = $startDate->copy();
        Log::info("Filling remaining visits from $startDate to $endDate, need $remainingVisits more visits");

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

                // Skip if this would exceed the contract end date
                if ($visitTime->gt($endDate)) {
                    continue;
                }

                if (!$visitTime->isPast()) {
                    $fullDateTime = $visitTime->format('Y-m-d H:i:s');
                    if (!in_array($fullDateTime, $existingBranchDates)) {
                        $additionalDates[] = $visitTime;
                        $usedDates[] = $currentDate->format('Y-m-d');
                        Log::info("Added filler date: $visitTime");
                        break; // Only one visit per day
                    }
                }
            }

            $currentDate->addDay();
        }

        Log::info("Fill remaining visits found " . count($additionalDates) . " dates");
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
        $fallbackSlots = [15, 16, 17, 18, 19, 20]; // 3:00 PM and 4:00 PM as fallback options
        
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
            
            // Ensure number of visits is valid
            if ($numberOfVisits <= 0) {
                Log::error("Invalid number of visits in contract: $numberOfVisits");
                throw new \Exception('Number of visits must be greater than 0');
            }
            
            Log::info("Creating schedule for contract {$contract->id} with $numberOfVisits visits");
            
            $branches = $contract->branchs;
            if ($branches->isEmpty()) {
                throw new \Exception('Contract must have at least one branch');
            }
            
            Log::info("Contract has " . count($branches) . " branches");

            // Use visit_start_date if available, otherwise fallback to contract_start_date
            $startDate = $contract->visit_start_date 
                ? Carbon::parse($contract->visit_start_date) 
                : Carbon::parse($contract->contract_start_date);
            
            // Ensure we have a contract end date
            $endDate = Carbon::parse($contract->contract_end_date);

            // Validate dates
            if ($startDate->gt($endDate)) {
                throw new \Exception('Visit start date cannot be after contract end date');
            }
            
            $branchVisitDates = [];
            $schedules = [];
            
            // Each branch gets visits based on its own number_of_visits if available
            // otherwise default to the contract's number_of_visits
            $branchCount = count($branches);
            Log::info("Total branches: $branchCount, contract visits: $numberOfVisits");

            // First, generate all visit dates for each branch
            foreach ($branches as $branch) {
                // Check if this branch has its own number_of_visits
                $branchVisits = (isset($branch->number_of_visits) && !empty($branch->number_of_visits)) 
                    ? $branch->number_of_visits 
                    : $numberOfVisits;
                
                Log::info("Generating visit dates for branch ID: {$branch->id}, branch visits: $branchVisits");
                
                $existingVisits = VisitSchedule::where('branch_id', $branch->id)
                    ->where('status', '!=', 'cancelled')
                    ->where('status', '!=', 'stopped')
                    ->get()
                    ->map(function ($visit) {
                        return $visit->visit_date . ' ' . $visit->visit_time;
                    })
                    ->toArray();
                
                Log::info("Branch {$branch->id} has " . count($existingVisits) . " existing visits");

                $branchVisitDates[$branch->id] = $this->generateVisitDates(
                    $startDate,
                    $endDate,
                    $branchVisits, // Use branch-specific visit number if available
                    $existingVisits
                );
                
                Log::info("Branch {$branch->id} generated " . count($branchVisitDates[$branch->id]) . " visit dates");
                
                // Alert if we couldn't generate all the requested visits
                if (count($branchVisitDates[$branch->id]) < $numberOfVisits) {
                    Log::warning("Could only generate " . count($branchVisitDates[$branch->id]) . " out of $numberOfVisits visits for branch {$branch->id}");
                }
            }

            // Check if we have any visits to schedule
            $totalVisitsToSchedule = 0;
            foreach ($branchVisitDates as $branchId => $visitDates) {
                $totalVisitsToSchedule += count($visitDates);
            }
            
            if ($totalVisitsToSchedule == 0) {
                Log::error("No visits could be scheduled for any branch in contract " . $contract->id);
                throw new \Exception('Could not schedule any visits for this contract. Please try different dates.');
            }
            
            Log::info("Total visits to schedule across all branches: $totalVisitsToSchedule");

            // Then, schedule visits for each date
            foreach ($branchVisitDates as $branchId => $visitDates) {
                $usedTeamSlots = [];
                Log::info("Scheduling " . count($visitDates) . " visits for branch $branchId");

                foreach ($visitDates as $index => $visitDateTime) {
                    $dateString = $visitDateTime->format('Y-m-d');
                    Log::info("Processing visit " . ($index+1) . " for branch $branchId on date $dateString");

                    // Find available team and time slot
                    $available = $this->findAvailableTeamAndSlot($dateString, $usedTeamSlots);

                    if (!$available) {
                        Log::error("No available team/slot found for visit on $dateString");
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
                        Log::info("Visit created: Contract " . $contract->id . ", Branch $branchId, Visit " . ($index+1) . ", Date $dateString, Team " . $available['team']->id);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error("Error creating visit: " . $e->getMessage());
                        throw $e;
                    }
                }
            }

            DB::commit();
            Log::info("Successfully scheduled " . count($schedules) . " visits for contract " . $contract->id);
            return $schedules;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create visit schedule: " . $e->getMessage());
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
            
            // Find only visits with 'stopped' status for this contract that were stopped due to contract stop
            // We'll only restore visits that are still in the future
            $stoppedVisits = VisitSchedule::where('contract_id', $contract->id)
                ->where('status', 'stopped')
                ->whereDate('visit_date', '>=', now()->format('Y-m-d'))
                ->get();
            
            $restoredCount = 0;
            
            // Update each stopped visit back to scheduled status
            foreach ($stoppedVisits as $visit) {
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
