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

    private function generateVisitDates(Carbon $startDate, int $numberOfVisits, array $existingBranchDates = []): array
    {
        $visitDates = [];
        $currentDate = $startDate->copy();

        while (count($visitDates) < $numberOfVisits) {
            // Skip Fridays and weekends
            if ($currentDate->isFriday()) {
                $currentDate->addDay();
                continue;
            }

            // Skip if this date is already used for this branch
            if (in_array($currentDate->format('Y-m-d'), $existingBranchDates)) {
                $currentDate->addDay();
                continue;
            }

            // Add one time slot for this day
            $visitTime = $currentDate->copy()->setHour(self::$timeSlots[0])->setMinute(0)->setSecond(0);
            if (!$visitTime->isPast()) {
                $visitDates[] = $visitTime;
            }

            $currentDate->addDay();
        }

        return $visitDates;
    }

    private function findAvailableTeamAndSlot($visitDate, array $usedTeamSlots)
    {
        $teams = Team::where('status', '=', 'active')->get();

        foreach (self::$timeSlots as $timeSlot) {
            foreach ($teams as $team) {
                $timeString = sprintf('%02d:00:00', $timeSlot);

                // Skip if this team+timeslot combination is already used
                $teamSlotKey = $team->id . '_' . $timeSlot;
                if (isset($usedTeamSlots[$teamSlotKey])) {
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
                    $branchVisitDates[$branch->id] = $this->generateVisitDates(
                        $startDate,
                        $numberOfVisits,
                        $branchVisitDates[$branch->id] ?? []
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
                            // Mark this team+slot as used for this date
                            $teamSlotKey = $available['team']->id . '_' . $available['slot'];
                            $usedTeamSlots[$teamSlotKey] = true;
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
                            // add info to log
                            Log::info('Visit schedule created: ' . $visitSchedule->id);
                            // printthe details of the visit schedule
                            Log::info('Visit schedule details: ' . $visitSchedule->toJson());
                        } catch (\Exception $e) {
                            DB::rollBack();
                            throw $e;
                        }

                        $schedules[] = $visitSchedule;
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

                    // Mark this team+slot as used for this date
                    $teamSlotKey = $available['team']->id . '_' . $available['slot'];
                    $usedTeamSlots[$teamSlotKey] = true;

                    // Create visit schedule
                    // dd($available);
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
