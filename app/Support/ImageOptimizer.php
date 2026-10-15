<?php
namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    /**
     * Simpan file gambar sambil mengompresi/menyesuaikan ukuran bila GD tersedia.
     */
    public static function store(UploadedFile $file, string $directory, int $maxWidth = 1600, int $quality = 82): string
    {
        if (! function_exists('imagecreatefromjpeg')) {
            return $file->store($directory, 'public');
        }

        $mime = $file->getMimeType();
        $source = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($file->getRealPath()),
            'image/png' => @imagecreatefrompng($file->getRealPath()),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file->getRealPath()) : null,
            default => null,
        };

        if (! $source) {
            return $file->store($directory, 'public');
        }

        $width = imagesx($source);
        $height = imagesy($source);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round($height * ($maxWidth / $width));
            $canvas = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($source);
            $source = $canvas;
        }

        $filename = $directory.'/'.uniqid('img_', true).'.jpg';
        $tmp = tempnam(sys_get_temp_dir(), 'folio');
        imagejpeg($source, $tmp, $quality);
        imagedestroy($source);

        Storage::disk('public')->put($filename, file_get_contents($tmp));
        @unlink($tmp);

        return $filename;
    }
}