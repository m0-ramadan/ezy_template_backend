<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupToolsTempFiles extends Command
{
    protected $signature = 'tools:cleanup-temp';
    protected $description = 'Cleanup isolated temporary tool upload files older than 30-60 minutes';

    public function handle()
    {
        $tempPath = storage_path('app/tools_temp');
        if (!File::isDirectory($tempPath)) {
            File::makeDirectory($tempPath, 0755, true);
            $this->info("Created tools_temp directory.");
            return 0;
        }

        $files = File::files($tempPath);
        $now = time();
        $deleted = 0;

        foreach ($files as $file) {
            // Delete if older than 30 minutes (1800 seconds)
            if ($now - $file->getMTime() > 1800) {
                File::delete($file->getPathname());
                $deleted++;
            }
        }

        $this->info("Cleaned up {$deleted} temporary tool files older than 30 minutes.");
        return 0;
    }
}
