<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class publish_scheduled_blogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish_scheduled_blogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
     
public function handle()
{
    $today = Carbon::today();

    $scheduledBlogs = Blog::where('is_active', 2)
       ->whereDate('active_date', '<=', $today)
        ->get();

    $count = 0;

    foreach ($scheduledBlogs as $blog) {
        $blog->is_active = 1;
        $blog->save();
        $count++;

        // Log each published blog ID (optional)
        Log::info("Blog published by scheduler", [
            'blog_id' => $blog->id,
            'published_at' => now()->toDateTimeString(),
        ]);
    }

    // Console output
    $this->info("$count scheduled blog(s) published at " . now());

    // Log summary
    Log::info("Scheduled publish command completed", [
        'published_count' => $count,
        'timestamp' => now()->toDateTimeString(),
    ]);
}
}
