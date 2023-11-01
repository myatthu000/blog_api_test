<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CleanUpReadNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up read notifications older than 1 months';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        return Command::SUCCESS;
        // Calculate the date one week ago
        $oneWeekAgo = Carbon::now()->subWeek();

        // Delete read notifications older than a week
        DB::table('notifications')
            ->where('read_at', '<=', $oneWeekAgo)
            ->delete();

        $this->info('Cleaned up read notifications older than a week.');
    }
}
