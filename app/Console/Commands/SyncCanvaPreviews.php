<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncCanvaPreviews extends Command
{
    protected $signature = 'canva:sync-previews {--install : Install Playwright/Sharp dependencies first}';
    protected $description = 'Compatibility command: fetch source-specific Canva media and update resource media status.';

    public function handle(): int
    {
        $arguments = ['--skip-office' => true];
        if ($this->option('install')) $arguments['--install'] = true;
        return $this->call('templates:finalize-media', $arguments);
    }
}
