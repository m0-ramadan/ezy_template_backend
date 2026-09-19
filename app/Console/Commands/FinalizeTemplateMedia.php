<?php

namespace App\Console\Commands;

use App\Models\Resource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;

class FinalizeTemplateMedia extends Command
{
    protected $signature = 'templates:finalize-media
        {--install : Install Node dependencies and Playwright Chromium}
        {--skip-canva : Do not fetch Canva source previews}
        {--skip-office : Do not rebuild/normalize Office previews}';

    protected $description = 'Build source-specific 1200x900 preview media for Canva and Office templates, then update database media status.';

    public function handle(): int
    {
        if ($this->option('install')) {
            $this->info('Installing Node dependencies...');
            $npm = new Process(['npm', 'install'], base_path());
            $npm->setTimeout(900);
            $npm->run(fn ($type, $buffer) => $this->output->write($buffer));
            if (!$npm->isSuccessful()) return self::FAILURE;

            $this->info('Installing Playwright Chromium...');
            $pw = new Process(['npx', 'playwright', 'install', 'chromium'], base_path());
            $pw->setTimeout(1200);
            $pw->run(fn ($type, $buffer) => $this->output->write($buffer));
            if (!$pw->isSuccessful()) return self::FAILURE;
        }

        if (!$this->option('skip-office')) {
            $this->info('Rebuilding Office preview media from each source file...');
            $python = new Process(['python3', 'scripts/rebuild_office_real_media.py'], base_path());
            $python->setTimeout(3600);
            $python->run(fn ($type, $buffer) => $this->output->write($buffer));
            if (!$python->isSuccessful()) {
                $this->error('Office media rebuild failed.');
                return self::FAILURE;
            }
            $this->applyOfficeStatus();
        }

        if (!$this->option('skip-canva')) {
            $this->info('Fetching real Canva preview media from the 188 source links...');
            $node = new Process(['node', 'scripts/sync_canva_real_media.mjs', base_path()], base_path());
            $node->setTimeout(7200);
            $node->run(fn ($type, $buffer) => $this->output->write($buffer));
            $this->applyCanvaReport();
            if (!$node->isSuccessful()) {
                $this->warn('Some Canva links could not be synchronized. See storage/app/canva-media-sync-report.json.');
            }
        }

        $this->call('templates:validate-media');
        return self::SUCCESS;
    }

    private function applyOfficeStatus(): void
    {
        if (!Schema::hasColumn('resources','media_sync_status')) return;
        Resource::whereIn('resource_type', ['excel','word','presentation'])
            ->update([
                'media_sync_status' => 'synced',
                'media_synced_at' => now(),
                'media_width' => 1200,
                'media_height' => 900,
                'gallery_status' => 'source-specific',
                'media_sync_message' => null,
            ]);
    }

    private function applyCanvaReport(): void
    {
        $path = storage_path('app/canva-media-sync-report.json');
        if (!is_file($path) || !Schema::hasColumn('resources','media_sync_status')) return;
        $rows = json_decode(file_get_contents($path), true) ?: [];
        foreach ($rows as $row) {
            $resource = Resource::where('slug', $row['slug'] ?? '')->first();
            if (!$resource) continue;
            $ok = ($row['status'] ?? '') === 'synced';
            $resource->update([
                'media_sync_status' => $ok ? 'synced' : 'failed',
                'media_synced_at' => $ok ? now() : null,
                'media_width' => $ok ? 1200 : null,
                'media_height' => $ok ? 900 : null,
                'gallery_status' => $ok ? 'source-specific' : 'sync-failed',
                'media_source_meta' => $row,
                'media_sync_message' => $ok ? null : ($row['error'] ?? 'Canva preview extraction failed'),
                'status' => $ok ? 'published' : 'draft',
                'published_at' => $ok ? ($resource->published_at ?? now()) : null,
            ]);
        }
    }
}
