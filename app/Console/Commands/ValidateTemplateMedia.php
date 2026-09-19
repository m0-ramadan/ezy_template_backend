<?php

namespace App\Console\Commands;

use App\Models\Resource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ValidateTemplateMedia extends Command
{
    protected $signature = 'templates:validate-media';
    protected $description = 'Validate that template cover/gallery files exist and report missing media.';

    public function handle(): int
    {
        $resources = Resource::whereIn('resource_type', ['design','excel','word','presentation'])->get();
        $missing = 0;
        foreach ($resources as $resource) {
            $paths = array_filter(array_merge(
                [$resource->preview_image, $resource->detail_image],
                is_array($resource->screenshots) ? $resource->screenshots : []
            ));
            foreach (array_unique($paths) as $publicPath) {
                $relative = preg_replace('#^/storage/#', '', $publicPath);
                if (!$relative || !Storage::disk('public')->exists($relative)) {
                    $this->line("MISSING {$resource->slug}: {$publicPath}");
                    $missing++;
                }
            }
        }
        $this->info("Validated {$resources->count()} template resources. Missing media files: {$missing}");
        return $missing === 0 ? self::SUCCESS : self::FAILURE;
    }
}
