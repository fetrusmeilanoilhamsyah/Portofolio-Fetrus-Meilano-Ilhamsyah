<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupCommand extends Command
{
    protected $signature = 'portfolio:backup';

    protected $description = 'Membuat arsip zip untuk database SQLite dan folder public uploads';

    public function handle()
    {
        $this->info('Memulai pencadangan...');
        $backupDir = storage_path('app/backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0750, true);
        }

        $timestamp = Carbon::now()->format('Y-m-d_His');
        $zipPath = $backupDir.'/backup_'.$timestamp.'.zip';

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            $dbPath = database_path('database.sqlite');
            if (File::exists($dbPath)) {
                $tempDbPath = $backupDir.'/temp_database.sqlite';
                if (File::exists($tempDbPath)) {
                    File::delete($tempDbPath);
                }
                
                \Illuminate\Support\Facades\DB::statement("VACUUM INTO '{$tempDbPath}'");
                $zip->addFile($tempDbPath, 'database/database.sqlite');
            } else {
                $this->warn('Database SQLite tidak ditemukan!');
            }

            $envPath = base_path('.env');
            if (File::exists($envPath)) {
                $zip->addFile($envPath, '.env');
            } else {
                $this->warn('File .env tidak ditemukan!');
            }

            $storagePath = storage_path('app/public');
            if (File::exists($storagePath)) {
                $files = File::allFiles($storagePath);
                foreach ($files as $file) {
                    $zip->addFile($file->getRealPath(), 'storage/app/public/'.$file->getRelativePathname());
                }
            }
            
            if ($zip->close()) {
                if (isset($tempDbPath) && File::exists($tempDbPath)) {
                    File::delete($tempDbPath);
                }
                $this->info("Pencadangan berhasil: {$zipPath}");
            } else {
                $this->error('Gagal menyimpan file ZIP.');
                if (isset($tempDbPath) && File::exists($tempDbPath)) {
                    File::delete($tempDbPath);
                }
                return 1;
            }
        } else {
            $this->error('Gagal membuat arsip zip.');

            return 1;
        }

        // Bersihkan cadangan lama, sisakan 7
        $backups = File::glob($backupDir.'/backup_*.zip');
        if (count($backups) > 7) {
            rsort($backups);
            $toDelete = array_slice($backups, 7);
            foreach ($toDelete as $old) {
                File::delete($old);
                $this->info("Cadangan lama dihapus: {$old}");
            }
        }

        return 0;
    }
}
