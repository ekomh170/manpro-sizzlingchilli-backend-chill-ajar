<?php

namespace App\Console;

use App\Console\Commands\HapusSesiExpired;
use App\Console\Commands\UpdateRatingMentor;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sesi:hapus-expired')->hourly();
        $schedule->command('mentor:update-rating')->everySixHours();
    }

    protected $commands = [
        HapusSesiExpired::class,
        UpdateRatingMentor::class,
    ];
}
