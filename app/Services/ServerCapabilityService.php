<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ServerCapabilityService
{
    /**
     * Inspect server binary/package dependencies.
     */
    public static function checkAll(): array
    {
        return [
            'ghostscript' => self::hasBinary('gs'),
            'libreoffice' => self::hasBinary('soffice') || self::hasBinary('libreoffice'),
            'poppler' => self::hasBinary('pdftoppm') || self::hasBinary('pdfinfo'),
            'imagemagick' => self::hasBinary('magick') || self::hasBinary('convert'),
            'phpspreadsheet' => class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class),
            'queue_worker' => true, // Laravel database/redis queue available
            'temp_storage' => is_writable(storage_path('app/tools_temp')),
        ];
    }

    public static function hasBinary(string $binary): bool
    {
        try {
            $result = Process::run("which {$binary}");
            return $result->successful() && !empty(trim($result->output()));
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function isDependencyAvailable(?string $dep): bool
    {
        if (empty($dep)) return true;
        $capabilities = self::checkAll();
        return !empty($capabilities[strtolower($dep)]);
    }
}
