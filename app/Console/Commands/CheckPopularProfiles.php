<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Profile;

class CheckPopularProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tinder:check-popular';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for profiles with > 50 likes and email admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Find profiles with > 50 likes
        // We group swipes by profile_id, filter by is_like=true, and count
        $popularProfiles = DB::table('swipes')
            ->select('profile_id', DB::raw('count(*) as total_likes'))
            ->where('is_like', true)
            ->groupBy('profile_id')
            ->having('total_likes', '>', 50)
            ->get();

        if ($popularProfiles->isEmpty()) {
            $this->info('No popular profiles found today.');
            return;
        }

        // 2. Prepare Email Content
        $details = "The following profiles have reached over 50 likes:\n\n";
        
        foreach ($popularProfiles as $record) {
            $profile = Profile::find($record->profile_id);
            if ($profile) {
                $details .= "- ID: {$profile->id}, Name: {$profile->name} (Likes: {$record->total_likes})\n";
            }
        }

        // 3. Send Email (Using raw mail for simplicity, normally use Mailable class)
        // Ensure MAIL_ variables are set in .env
        Mail::raw($details, function ($message) {
            $message->to('admin@example.com')
                    ->subject('Popular Profiles Alert!');
        });

        $this->info('Admin notified about ' . $popularProfiles->count() . ' popular profiles.');
    }
}