<?php

namespace App\Console;

use App\Jobs\ProcessNoteApiCall;
use App\Models\Note;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        Log::info('Dispatching Note API jobs');
        $schedule->call(function () {
//             Fetch all notes (or filter as needed)
            Note::all()->each(function ($note) {
                ProcessNoteApiCall::dispatch($note);
            });
        })->dailyAt('16:47'); // Runs daily at 2 AM
    }

    protected $commands = [
        //
    ];
}
