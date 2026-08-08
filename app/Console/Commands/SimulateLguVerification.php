<?php

namespace App\Console\Commands;

use App\Models\FloodReport;
use App\Services\PointsService;
use Illuminate\Console\Command;

class SimulateLguVerification extends Command
{
    protected $signature = 'reports:simulate-verification';
    protected $description = 'Stand-in for the (out of scope) LGU Admin verification screen — moves submitted reports forward.';

    public function handle(PointsService $points): int
    {
        $submitted = FloodReport::where('status', 'submitted')->get();

        foreach ($submitted as $report) {
            // 90% of reports get verified, mirroring "admin marks Verified" in UC-04 step 2
            $isValid = rand(1, 100) <= 90;

            if (! $isValid) {
                $report->update(['status' => 'rejected']);
                $this->line("Report #{$report->id}: rejected (dummy — treat as duplicate/invalid).");
                continue;
            }

            $report->update(['status' => 'verified', 'verified_by_lgu' => true]);

            // simulate cleanup happening within 48h ~60% of the time, per the +25 vs +15 point rule
            $withinFortyEightHours = rand(1, 100) <= 60;
            $report->update([
                'status' => 'cleaned',
                'cleanup_completed_at' => now(),
            ]);

            $bonusPoints = $withinFortyEightHours ? 25 : 15;
            $ledger = $points->award($report->user, 'flood_report_verified', $bonusPoints, $report->id);
            $report->update(['points_awarded' => $ledger->points_earned]);

            $this->line("Report #{$report->id}: verified + cleaned, resident awarded +{$bonusPoints} pts.");
        }

        if ($submitted->isEmpty()) {
            $this->info('No submitted reports waiting for review.');
        }

        return self::SUCCESS;
    }
}
