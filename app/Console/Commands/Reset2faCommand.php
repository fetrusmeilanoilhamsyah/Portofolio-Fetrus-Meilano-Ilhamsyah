<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class Reset2faCommand extends Command
{
    protected $signature = 'portfolio:reset-2fa';

    protected $description = 'Hapus rahasia 2FA dan kode pemulihan admin (kondisi terkunci)';

    public function handle(): int
    {
        $email = config('portfolio.admin_email');

        if (empty($email)) {
            $this->error('ADMIN_EMAIL belum diisi di .env (atau config/portfolio.php).');

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Pengguna admin dengan email {$email} tidak ditemukan.");

            return self::FAILURE;
        }

        if (empty($user->getAppAuthenticationSecret())) {
            $this->info("Pengguna {$email} belum mengaktifkan 2FA. Tidak ada yang perlu direset.");

            return self::SUCCESS;
        }

        $this->warn('PERHATIAN: Perintah ini akan menghapus rahasia 2FA dan semua kode pemulihan milik admin.');
        $this->warn("Email admin: {$email}");

        if (! $this->confirm('Lanjutkan? (ketik yes untuk mengonfirmasi)', false)) {
            $this->info('Dibatalkan.');

            return self::SUCCESS;
        }

        $user->saveAppAuthenticationSecret(null);
        $user->saveAppAuthenticationRecoveryCodes(null);

        $this->info("2FA berhasil direset untuk {$email}. Admin dapat mengaktifkan ulang 2FA lewat halaman profil (/admin/profile).");

        return self::SUCCESS;
    }
}
