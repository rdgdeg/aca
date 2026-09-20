<?php

namespace App\Filament\Concerns;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

trait HasCoverUpload
{
    public static function coverUpload(string $directory, string $label = 'Photo'): FileUpload
    {
        return FileUpload::make('cover_url')
            ->label($label)
            ->image()
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
            ->maxSize(4096)
            ->downloadable()
            ->openable()
            ->imagePreviewHeight('14rem')
            ->helperText('JPG, PNG ou WebP — 4 Mo maximum. L’image actuelle est conservée si vous n’en envoyez pas une nouvelle.')
            ->belowContent(function ($record): ?HtmlString {
                $cover = $record?->cover_url ?? null;
                if (! is_string($cover) || $cover === '') {
                    return null;
                }

                $src = str_starts_with($cover, 'http://') || str_starts_with($cover, 'https://') || str_starts_with($cover, '/')
                    ? $cover
                    : Storage::disk('public')->url($cover);

                return new HtmlString(
                    '<div class="mt-2"><p class="mb-1 text-sm text-gray-500">Affiche actuelle</p><img src="'.e($src).'" alt="" class="max-h-56 rounded-xl object-cover shadow-sm"></div>'
                );
            })
            ->fetchFileInformation(false)
            ->getUploadedFileUsing(function (BaseFileUpload $component, string $file, string|array|null $storedFileNames): ?array {
                if (str_starts_with($file, 'http://') || str_starts_with($file, 'https://') || str_starts_with($file, '/')) {
                    return [
                        'name' => basename($file),
                        'size' => 0,
                        'type' => 'image/jpeg',
                        'url' => str_starts_with($file, '/') ? url($file) : $file,
                    ];
                }

                return $component->getUploadedFile($file, $storedFileNames);
            });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function preserveExistingCover(array $data, ?string $currentCover): array
    {
        if (blank($data['cover_url'] ?? null) && filled($currentCover)) {
            $data['cover_url'] = $currentCover;
        }

        return $data;
    }
}
