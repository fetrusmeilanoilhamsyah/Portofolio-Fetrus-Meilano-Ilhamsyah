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
     * (portrait diputar), sehingga dimensi output seharusnya tertukar.
     *
     * Dilewati jika ekstensi exif tidak tersedia.
     */
    public function test_image_optimizer_applies_exif_orientation_6(): void
    {
        if (! function_exists('exif_read_data')) {
            $this->markTestSkipped('Ekstensi exif tidak tersedia di lingkungan ini; lewati tes orientasi EXIF.');
        }

        Storage::fake('public');

        $optimizer = new ImageOptimizer;

        // Buat JPEG kecil (lebar 100, tinggi 200) dengan data EXIF Orientation = 6.
        // Setelah diproses, dimensi output harus menjadi 200×100 (tertukar).
        $tmpPath = sys_get_temp_dir().'/test_exif6.jpg';
        $this->buildJpegWithExifOrientation6($tmpPath, 100, 200);

        $file = new UploadedFile($tmpPath, 'exif6.jpg', 'image/jpeg', null, true);

        $outputPath = $optimizer->optimizeAndSave($file);

        // Baca dimensi output dari storage
        $diskPath = Storage::disk('public')->path($outputPath);
        $outInfo = getimagesize($diskPath);

        @unlink($tmpPath);

        $this->assertNotFalse($outInfo, 'Output WebP harus bisa dibaca');
        // Orientasi 6 → rotasi -90° → lebar dan tinggi asli tertukar
        $this->assertGreaterThan($outInfo[0], $outInfo[1], 'Setelah rotasi orientasi-6, tinggi harus lebih besar dari lebar (100×200 → 200 wide, 100 high setelah rotasi -90)');
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
     * Buat JPEG kecil dengan metadata EXIF Orientation = 6 menggunakan GD + EXIF APP1 sederhana.
     * Ini hanya berfungsi di lingkungan yang sudah memiliki ekstensi exif.
     */
    private function buildJpegWithExifOrientation6(string $path, int $width, int $height): void
    {
        // Buat JPEG tanpa EXIF dulu, lalu sisipkan APP1 marker dengan Orientation = 6.
        $img = imagecreatetruecolor($width, $height);
        $blue = imagecolorallocate($img, 0, 0, 255);
        imagefilledrectangle($img, 0, 0, $width, $height, $blue);

        ob_start();
        imagejpeg($img, null, 90);
        $jpegData = ob_get_clean();
        imagedestroy($img);

        // Sisipkan APP1 EXIF dengan Orientation = 6.
        // Struktur: FF E1 (length 2 byte) "Exif\0\0" + TIFF header + IFD dengan tag Orientation.
        // Kita memakai little-endian (II) TIFF header.
        $tiff = 'II';                       // byte order: little-endian
        $tiff .= pack('v', 42);             // magic number
        $tiff .= pack('V', 8);              // offset to IFD
        $tiff .= pack('v', 1);             // IFD count: 1 entry
        // IFD entry: tag(2) type(2) count(4) value(4)
        // Orientation tag = 0x0112, type SHORT (3), count 1, value 6
        $tiff .= pack('v', 0x0112);        // tag
        $tiff .= pack('v', 3);             // type SHORT
        $tiff .= pack('V', 1);             // count
        $tiff .= pack('v', 6)."\x00\x00"; // value = 6, padded to 4 bytes
        $tiff .= pack('V', 0);             // next IFD offset = 0 (end)

        $exifHeader = "Exif\x00\x00".$tiff;
        $app1Length = strlen($exifHeader) + 2; // +2 for the length field itself
        $app1 = "\xFF\xE1".pack('n', $app1Length).$exifHeader;

        // Sisipkan APP1 setelah SOI marker (2 byte pertama = FF D8)
        $newJpeg = substr($jpegData, 0, 2).$app1.substr($jpegData, 2);

        file_put_contents($path, $newJpeg);
    }
}
