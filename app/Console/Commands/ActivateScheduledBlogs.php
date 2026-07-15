<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;
use Carbon\Carbon;

class ActivateScheduledBlogs extends Command
{
    protected $signature = 'blogs:activate-scheduled';
    protected $description = 'Activate blogs that should go live today';

    public function handle()
    {
        $today = Carbon::now()->toDateString();

        $createdToday = Blog::whereDate('created_at', $today)
        ->where('is_active', 0)
        ->where('is_archived', 0)
        ->update(['is_active' => 1]);

        $scheduledToday = Blog::whereDate('active_date', $today)
        ->where('is_active', 0)
        ->where('is_archived', 0)
        ->update(['is_active' => 1]);

        $this->info("Activated $createdToday blogs created today.");
        $this->info("Activated $scheduledToday blogs scheduled for today.");
    }
}
