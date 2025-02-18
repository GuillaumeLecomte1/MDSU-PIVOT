<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ImageService
{
    private const ALLOWED_FORMATS = ['jpg', 'jpeg', 'png', 'webp'];
    private const DEFAULT_QUALITY = 80;
    private const DEFAULT_FORMAT = 'webp';
    private const IMAGE_SIZES = [
        'thumb' => ['width' => 150, 'height' => 150],
        'medium' => ['width' => 300, 'height' => 300],
        'large' => ['width' => 800, 'height' => 800],
    ];

    /**
     * Crée une image de test avec un format spécifique
     */
    public function createTestImage(int $width = 800, int $height = 600, string $text = null): string
    {
        $text = $text ?? 'Test Image';
        $image = Image::canvas($width, $height, '#f0f0f0');
        
        // Add text
        $image->text($text, $width / 2, $height / 2, function ($font) {
            $font->size(72);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });

        // Add a white background for better text readability
        $textBox = [
            'width' => $width * 0.9,
            'height' => 120,
            'x' => $width * 0.05,
            'y' => ($height / 2) - 60,
        ];

        $image->rectangle(
            $textBox['x'],
            $textBox['y'],
            $textBox['x'] + $textBox['width'],
            $textBox['y'] + $textBox['height'],
            function ($draw) {
                $draw->background('rgba(255, 255, 255, 0.9)');
            }
        );

        // Add text over the white background
        $image->text($text, $width / 2, $height / 2, function ($font) {
            $font->size(72);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });

        // Generate unique filename
        $filename = $this->normalizeImageName('test-image', self::DEFAULT_FORMAT);
        $path = 'products/' . $filename;

        // Save image
        Storage::disk('public')->put($path, $image->encode(self::DEFAULT_FORMAT, self::DEFAULT_QUALITY));

        return $path;
    }

    /**
     * Normalise le nom d'un fichier image selon la convention
     */
    public function normalizeImageName(string $baseName, string $extension): string
    {
        $extension = strtolower($extension);
        if (!in_array($extension, self::ALLOWED_FORMATS)) {
            $extension = self::DEFAULT_FORMAT;
        }

        $baseName = Str::slug($baseName);
        $uniqueId = Str::random(8);

        return sprintf('%s-%s.%s', $baseName, $uniqueId, $extension);
    }

    /**
     * Convertit et optimise une image existante
     */
    public function convertImage(string $sourcePath, string $targetFormat = self::DEFAULT_FORMAT, int $quality = self::DEFAULT_QUALITY): ?string
    {
        try {
            if (!Storage::disk('public')->exists($sourcePath)) {
                return null;
            }

            $image = Image::make(Storage::disk('public')->path($sourcePath));
            $pathInfo = pathinfo($sourcePath);
            $newFilename = $this->normalizeImageName($pathInfo['filename'], $targetFormat);
            $newPath = $pathInfo['dirname'] . '/' . $newFilename;

            Storage::disk('public')->put(
                $newPath,
                $image->encode($targetFormat, $quality)
            );

            return $newPath;
        } catch (\Exception $e) {
            \Log::error('Image conversion failed: ' . $e->getMessage());
            return null;
        }
    }

    public function generateThumbnails(string $sourcePath): array
    {
        $thumbnails = [];

        try {
            if (!Storage::disk('public')->exists($sourcePath)) {
                return $thumbnails;
            }

            $image = Image::make(Storage::disk('public')->path($sourcePath));
            $pathInfo = pathinfo($sourcePath);

            foreach (self::IMAGE_SIZES as $size => $dimensions) {
                $newFilename = sprintf(
                    '%s-%s.%s',
                    $pathInfo['filename'],
                    $size,
                    self::DEFAULT_FORMAT
                );
                $newPath = $pathInfo['dirname'] . '/' . $newFilename;

                // Create thumbnail
                $thumbnail = clone $image;
                $thumbnail->fit($dimensions['width'], $dimensions['height']);

                Storage::disk('public')->put(
                    $newPath,
                    $thumbnail->encode(self::DEFAULT_FORMAT, self::DEFAULT_QUALITY)
                );

                $thumbnails[$size] = [
                    'path' => $newPath,
                    'width' => $dimensions['width'],
                    'height' => $dimensions['height']
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Thumbnail generation failed: ' . $e->getMessage());
        }

        return $thumbnails;
    }
} 