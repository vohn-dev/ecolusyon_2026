<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Junkshop;
use Illuminate\Console\Command;

class SimulateEprMatch extends Command
{
    protected $signature = 'junkshop:simulate-epr-match {junkshop_id? : Junkshop to match, random if omitted}';
    protected $description = 'Dummy simulation of an EPR buyer match, for demoing the Notifications screen without a real EPR-enterprise module.';

    public function handle(): int
    {
        $junkshop = $this->argument('junkshop_id')
            ? Junkshop::findOrFail($this->argument('junkshop_id'))
            : Junkshop::whereNotNull('owner_user_id')->inRandomOrder()->first();

        if (! $junkshop) {
            $this->error('No junkshop with a linked operator account exists yet — finish Module 02 first.');
            return self::FAILURE;
        }

        $kg = rand(20, 80);

        AppNotification::create([
            'user_id' => $junkshop->owner_user_id,
            'type' => 'epr_match',
            'message' => "EPR buyer matched — {$kg}kg cardboard ready for pickup coordination.",
        ]);

        $this->info("Simulated EPR match for {$junkshop->name} ({$kg}kg).");
        return self::SUCCESS;
    }
}
