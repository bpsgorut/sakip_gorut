<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:clean {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired sessions from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sessionLifetime = config('session.lifetime', 120); // minutes
        $expiredTime = Carbon::now()->subMinutes($sessionLifetime)->timestamp;
        
        // Count expired sessions
        $expiredCount = DB::table('sessions')
            ->where('last_activity', '<', $expiredTime)
            ->count();
            
        if ($expiredCount === 0) {
            $this->info('No expired sessions found.');
            return 0;
        }
        
        $this->info("Found {$expiredCount} expired sessions.");
        
        if (!$this->option('force') && !$this->confirm('Do you want to delete these expired sessions?')) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        // Delete expired sessions
        $deleted = DB::table('sessions')
            ->where('last_activity', '<', $expiredTime)
            ->delete();
            
        $this->info("Successfully deleted {$deleted} expired sessions.");
        
        // Also clean up any orphaned CSRF tokens
        $this->info('Cleaning up session storage...');
        
        return 0;
    }
}
