<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdminCommand extends Command
{
    protected $signature = 'portfolio:make-admin';

    protected $description = 'Buat pengguna admin portofolio';

    public function handle(): int
    {
        $email = config('portfolio.admin_email');

        if (empty($email)) {
            $this->error('ADMIN_EMAIL belum diisi di .env (atau config/portfolio.php).');
            $this->line('Tambahkan ADMIN_EMAIL=email@kamu.com ke .env lalu jalankan php artisan config:clear.');

            return self::FAILURE;
        }

        $this->info("Email admin: {$email}");

        $name = $this->ask('Nama admin', 'Admin');

        $password = $this->secret('Kata sandi');

        if (empty($password)) {
            $this->error('Kata sandi tidak boleh kosong.');

            return self::FAILURE;
        }

        $confirm = $this->secret('Konfirmasi kata sandi');

        if ($password !== $confirm) {
            $this->error('Kata sandi tidak cocok.');

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $action = $user->wasRecentlyCreated ? 'dibuat' : 'diperbarui';
        $this->info("Pengguna admin berhasil {$action}: {$email}");

        return self::SUCCESS;
    }
}
