<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class ImageOptimizer
{
    /**
     * Optimasi gambar ke format WebP dengan batas lebar 1600px dan quality 82.
     * Menggunakan native GD karena imagick/intervention belum terpasang.
     */
    public function optimizeAndSave(UploadedFile $file, string $directory = 'media'): string
    {
        if (! str_starts_with($file->getMimeType(), 'image/')) {
            throw new InvalidArgumentException('File bukan gambar.');
        }

        // SVG tidak didukung oleh GD/getimagesize dan tidak perlu dioptimasi
        if ($file->getMimeType() === 'image/svg+xml') {
            $path = $file->store($directory, 'public');
            return $path;
        }

        $sourcePath = $file->getRealPath();
        $info = @getimagesize($sourcePath);

        if ($info === false) {
            throw new InvalidArgumentException('Gagal membaca informasi gambar.');
        }

        [$origWidth, $origHeight, $imageType] = $info;

        if ($origWidth * $origHeight > 24000000) {
            throw new InvalidArgumentException('Gambar terlalu besar (lebih dari 24 megapiksel). Harap perkecil gambar Anda sebelum mengunggah.');
        }

        $image = match ($imageType) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => imagecreatefromwebp($sourcePath),
            IMAGETYPE_GIF => imagecreatefromgif($sourcePath),
            default => throw new InvalidArgumentException('Tipe gambar tidak didukung.'),
        };

        if (! $image) {
            throw new InvalidArgumentException('Gagal memproses gambar.');
        }

        if ($imageType === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);
            if (! empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $image = imagerotate($image, 180, 0);
                        break;
                    case 6:
                        $image = imagerotate($image, -90, 0);
                        $tmp = $origWidth;
                        $origWidth = $origHeight;
                        $origHeight = $tmp;
                        break;
                    case 8:
                        $image = imagerotate($image, 90, 0);
                        $tmp = $origWidth;
                        $origWidth = $origHeight;
                        $origHeight = $tmp;
                        break;
                }
            }
        }

        // Hitung dimensi baru
        $maxWidth = 1600;
        if ($origWidth > $maxWidth) {
            $ratio = $maxWidth / $origWidth;
            $newWidth = $maxWidth;
            $newHeight = (int) round($origHeight * $ratio);
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }

        // Buat canvas baru
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Pertahankan transparansi untuk PNG/WEBP/GIF
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);

        // Resize
        imagecopyresampled(
            $newImage, $image,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $origWidth, $origHeight
        );

        // Buat nama file WebP
        $filename = uniqid('img_').'.webp';
        $path = $directory.'/'.$filename;
        $tempPath = sys_get_temp_dir().'/'.$filename;

        // Simpan ke temp lalu pindahkan pakai Storage untuk memanfaatkan konfigurasi disk
        imagewebp($newImage, $tempPath, 82);

        Storage::disk('public')->put($path, file_get_contents($tempPath));

        // Bersihkan memori dan temp file
        imagedestroy($image);
        imagedestroy($newImage);
        @unlink($tempPath);

        return $path;
    }
}
