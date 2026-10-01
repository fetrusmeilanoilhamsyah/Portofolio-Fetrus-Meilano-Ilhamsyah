<?php

namespace Tests\Feature;

use App\Services\ImageOptimizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Tes tambahan untuk ImageOptimizer (resolusi dan orientasi EXIF).
 */
class ImageOptimizerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pastikan SVG langsung ditolak demi keamanan (tidak ada optimasi, tidak ada upload).
     */
    public function test_image_optimizer_rejects_svg(): void
    {
        $optimizer = new ImageOptimizer;

        $tmpPath = sys_get_temp_dir().'/test.svg';
        file_put_contents($tmpPath, '<svg></svg>');
        $file = new UploadedFile($tmpPath, 'test.svg', 'image/svg+xml', null, true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File SVG tidak diizinkan untuk alasan keamanan');

        try {
            $optimizer->optimizeAndSave($file);
        } finally {
            @unlink($tmpPath);
        }
    }

    /**
     * 3a — ImageOptimizer menolak gambar di atas 24 megapiksel.
     *
     * Strategi: buat berkas PNG minimal dengan header IHDR yang menyatakan dimensi besar
     * (6000x4001 = 24.006.000 piksel > 24.000.000).
     * getimagesize() hanya membaca header sehingga berkas tidak perlu benar-benar berukuran besar.
     * Pengecekan terjadi SEBELUM imagecreatefrompng dipanggil.
     */
    public function test_image_optimizer_rejects_image_above_24_megapixels(): void
    {
        $optimizer = new ImageOptimizer;

        // Buat berkas PNG minimal dengan header IHDR yang menyatakan 6000×4001 piksel.
        // Struktur PNG: signature (8 byte) + IHDR chunk (25 byte) + IDAT minimal + IEND.
        $png = $this->buildMinimalPng(6000, 4001);

        // Tulis ke file temp agar bisa dipakai UploadedFile
        $tmpPath = sys_get_temp_dir().'/test_huge.png';
        file_put_contents($tmpPath, $png);

        // Verifikasi bahwa getimagesize membaca dimensi yang benar dari header
        $info = getimagesize($tmpPath);
        $this->assertNotFalse($info, 'getimagesize harus berhasil membaca header PNG minimal');
        $this->assertEquals(6000, $info[0], 'Lebar header PNG harus 6000');
        $this->assertEquals(4001, $info[1], 'Tinggi header PNG harus 4001');

        $file = new UploadedFile($tmpPath, 'huge.png', 'image/png', null, true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Gambar terlalu besar');

        try {
            $optimizer->optimizeAndSave($file);
        } finally {
            @unlink($tmpPath);
        }
    }

    /**
     * 3b — ImageOptimizer menerapkan orientasi EXIF pada JPEG dengan orientasi 6
     * (portrait diputar), sehingga dimensi output seharusnya tertukar (landscape).
     */
    public function test_image_optimizer_applies_exif_orientation_6(): void
    {
        Storage::fake('public');

        $optimizer = new ImageOptimizer;

        // Buat JPEG kecil (lebar 100, tinggi 200) dengan data EXIF Orientation = 6.
        // Setelah diproses (rotasi -90), dimensi output harus menjadi 200x100.
        $tmpPath = sys_get_temp_dir().'/test_exif6.jpg';
        $this->buildJpegWithExifOrientation($tmpPath, 100, 200, 6);

        $file = new UploadedFile($tmpPath, 'exif6.jpg', 'image/jpeg', null, true);

        $outputPath = $optimizer->optimizeAndSave($file);

        // Baca dimensi output dari storage
        $diskPath = Storage::disk('public')->path($outputPath);
        $outInfo = getimagesize($diskPath);

        @unlink($tmpPath);

        $this->assertNotFalse($outInfo, 'Output WebP harus bisa dibaca');
        // Orientasi 6: lebar 200, tinggi 100
        $this->assertEquals(200, $outInfo[0], 'Lebar harus 200 setelah rotasi orientasi 6');
        $this->assertEquals(100, $outInfo[1], 'Tinggi harus 100 setelah rotasi orientasi 6');
    }

    /**
     * 3b — ImageOptimizer menerapkan orientasi EXIF pada JPEG dengan orientasi 8
     * (portrait diputar arah sebaliknya), sehingga dimensi output tertukar (landscape).
     */
    public function test_image_optimizer_applies_exif_orientation_8(): void
    {
        Storage::fake('public');

        $optimizer = new ImageOptimizer;

        // Buat JPEG kecil (lebar 100, tinggi 200) dengan data EXIF Orientation = 8.
        // Setelah diproses (rotasi 90), dimensi output harus menjadi 200x100.
        $tmpPath = sys_get_temp_dir().'/test_exif8.jpg';
        $this->buildJpegWithExifOrientation($tmpPath, 100, 200, 8);

        $file = new UploadedFile($tmpPath, 'exif8.jpg', 'image/jpeg', null, true);

        $outputPath = $optimizer->optimizeAndSave($file);

        $diskPath = Storage::disk('public')->path($outputPath);
        $outInfo = getimagesize($diskPath);

        @unlink($tmpPath);

        $this->assertNotFalse($outInfo, 'Output WebP harus bisa dibaca');
        // Orientasi 8: lebar 200, tinggi 100
        $this->assertEquals(200, $outInfo[0], 'Lebar harus 200 setelah rotasi orientasi 8');
        $this->assertEquals(100, $outInfo[1], 'Tinggi harus 100 setelah rotasi orientasi 8');
    }

    /**
     * 3b — ImageOptimizer tidak merotasi gambar dengan orientasi 1 (normal).
     */
    public function test_image_optimizer_keeps_orientation_1(): void
    {
        Storage::fake('public');

        $optimizer = new ImageOptimizer;

        // Buat JPEG kecil (lebar 100, tinggi 200) dengan data EXIF Orientation = 1.
        // Tidak ada rotasi, dimensi output harus tetap 100x200.
        $tmpPath = sys_get_temp_dir().'/test_exif1.jpg';
        $this->buildJpegWithExifOrientation($tmpPath, 100, 200, 1);

        $file = new UploadedFile($tmpPath, 'exif1.jpg', 'image/jpeg', null, true);

        $outputPath = $optimizer->optimizeAndSave($file);

        $diskPath = Storage::disk('public')->path($outputPath);
        $outInfo = getimagesize($diskPath);

        @unlink($tmpPath);

        $this->assertNotFalse($outInfo, 'Output WebP harus bisa dibaca');
        // Orientasi 1: lebar 100, tinggi 200
        $this->assertEquals(100, $outInfo[0], 'Lebar harus tetap 100 untuk orientasi 1');
        $this->assertEquals(200, $outInfo[1], 'Tinggi harus tetap 200 untuk orientasi 1');
    }

    public function test_image_optimizer_rejects_pdf(): void
    {
        $optimizer = new ImageOptimizer;
        $tmpPath = sys_get_temp_dir().'/test.pdf';
        file_put_contents($tmpPath, '%PDF-1.4');
        $file = new UploadedFile($tmpPath, 'test.pdf', 'application/pdf', null, true);

        $this->expectException(InvalidArgumentException::class);

        try {
            $optimizer->optimizeAndSave($file);
        } finally {
            @unlink($tmpPath);
        }
    }

    public function test_image_optimizer_handles_broken_png_without_fatal_error(): void
    {
        Storage::fake('public');
        $optimizer = new ImageOptimizer;

        $tmpPath = sys_get_temp_dir().'/broken.png';
        $signature = "\x89PNG\r\n\x1a\n";
        $ihdrData = pack('N', 100).pack('N', 100)."\x08\x02\x00\x00\x00";
        $ihdr = pack('N', strlen($ihdrData)).'IHDR'.$ihdrData.pack('N', crc32('IHDR'.$ihdrData));
        file_put_contents($tmpPath, $signature.$ihdr.'BROKENDATA');

        $file = new UploadedFile($tmpPath, 'broken.png', 'image/png', null, true);

        $result = $optimizer->optimizeAndSave($file);
        $this->assertIsString($result);

        @unlink($tmpPath);
    }

    // -------------------------------------------------------------------------
    // Helper: buat PNG minimal dengan IHDR menyatakan dimensi tertentu
    // -------------------------------------------------------------------------

    /**
     * Menghasilkan binary PNG minimal yang valid (getimagesize bisa membacanya)
     * namun IDAT-nya rusak sehingga imagecreatefrompng akan gagal — tapi
     * pengecekan resolusi terjadi SEBELUM decode, jadi tes tetap benar.
     */
    private function buildMinimalPng(int $width, int $height): string
    {
        // PNG signature
        $signature = "\x89PNG\r\n\x1a\n";

        // IHDR chunk: width(4) + height(4) + bit-depth(1) + color-type(1) + compression(1) + filter(1) + interlace(1)
        $ihdrData = pack('N', $width).pack('N', $height)."\x08\x02\x00\x00\x00";
        $ihdrCrc = pack('N', crc32('IHDR'.$ihdrData));
        $ihdr = pack('N', strlen($ihdrData)).'IHDR'.$ihdrData.$ihdrCrc;

        // IEND chunk (minimal, wajib ada agar getimagesize mengenali format)
        $iendCrc = pack('N', crc32('IEND'));
        $iend = "\x00\x00\x00\x00IEND".$iendCrc;

        return $signature.$ihdr.$iend;
    }

    /**
     * Buat JPEG kecil dengan metadata EXIF Orientation kustom menggunakan GD + EXIF APP1 sederhana.
     */
    private function buildJpegWithExifOrientation(string $path, int $width, int $height, int $orientation): void
    {
        // Buat JPEG tanpa EXIF dulu, lalu sisipkan APP1 marker dengan Orientation.
        $img = imagecreatetruecolor($width, $height);
        $blue = imagecolorallocate($img, 0, 0, 255);
        imagefilledrectangle($img, 0, 0, $width, $height, $blue);

        ob_start();
        imagejpeg($img, null, 90);
        $jpegData = ob_get_clean();
        imagedestroy($img);

        // Sisipkan APP1 EXIF dengan Orientation.
        // Struktur: FF E1 (length 2 byte) "Exif\0\0" + TIFF header + IFD dengan tag Orientation.
        // Kita memakai little-endian (II) TIFF header.
        $tiff = 'II';                       // byte order: little-endian
        $tiff .= pack('v', 42);             // magic number
        $tiff .= pack('V', 8);              // offset to IFD
        $tiff .= pack('v', 1);             // IFD count: 1 entry
        // IFD entry: tag(2) type(2) count(4) value(4)
        // Orientation tag = 0x0112, type SHORT (3), count 1, value = $orientation
        $tiff .= pack('v', 0x0112);        // tag
        $tiff .= pack('v', 3);             // type SHORT
        $tiff .= pack('V', 1);             // count
        $tiff .= pack('v', $orientation)."\x00\x00"; // value = $orientation, padded to 4 bytes
        $tiff .= pack('V', 0);             // next IFD offset = 0 (end)

        $exifHeader = "Exif\x00\x00".$tiff;
        $app1Length = strlen($exifHeader) + 2; // +2 for the length field itself
        $app1 = "\xFF\xE1".pack('n', $app1Length).$exifHeader;

        // Sisipkan APP1 setelah SOI marker (2 byte pertama = FF D8)
        $newJpeg = substr($jpegData, 0, 2).$app1.substr($jpegData, 2);

        file_put_contents($path, $newJpeg);
    }
}
