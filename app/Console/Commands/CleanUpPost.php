<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CleanUpPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up posts older than 1 months';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oneWeekAgo = Carbon::now()->subDays(30);

        // Delete read notifications older than a week
        DB::table('posts')
            ->where('deleted_at', '<=', $oneWeekAgo)
            ->delete();

        $this->info('Cleaned up posts older than a month.');
    }
}
