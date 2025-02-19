<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DocumentationService
{
    private const DOCUMENTATION_PATH = 'documentation';

    public function getAllDocuments(): array
    {
        $path = base_path(self::DOCUMENTATION_PATH);
        if (!File::isDirectory($path)) {
            return [];
        }

        $files = File::files($path);
        return collect($files)
            ->filter(fn ($file) => Str::endsWith($file->getFilename(), '.md'))
            ->map(fn ($file) => [
                'name' => Str::beforeLast($file->getFilename(), '.md'),
                'filename' => $file->getFilename(),
                'last_modified' => File::lastModified($file->getPathname()),
                'size' => File::size($file->getPathname()),
            ])
            ->values()
            ->toArray();
    }

    public function getDocument(string $filename): ?string
    {
        $path = base_path(self::DOCUMENTATION_PATH . '/' . $filename);
        if (!File::exists($path)) {
            return null;
        }

        return File::get($path);
    }
} 