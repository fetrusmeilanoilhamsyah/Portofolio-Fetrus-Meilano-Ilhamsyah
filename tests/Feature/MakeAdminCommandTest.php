<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MakeAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    /** Perintah gagal jika ADMIN_EMAIL belum diisi */
    public function test_fails_if_admin_email_not_configured(): void
    {
        config(['portfolio.admin_email' => '']);

        $this->artisan('portfolio:make-admin')
            ->expectsOutput('ADMIN_EMAIL belum diisi di .env (atau config/portfolio.php).')
            ->assertExitCode(1);
    }

    /** Perintah membuat user admin baru */
    public function test_creates_admin_user(): void
    {
        config(['portfolio.admin_email' => 'admin@example.com']);

        $this->artisan('portfolio:make-admin')
            ->expectsQuestion('Nama admin', 'Admin')
            ->expectsQuestion('Kata sandi', 'secret123')
            ->expectsQuestion('Konfirmasi kata sandi', 'secret123')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'name' => 'Admin',
        ]);
    }

    /** Perintah memperbarui user jika sudah ada (idempoten) */
    public function test_updates_existing_admin_user(): void
    {
        config(['portfolio.admin_email' => 'admin@example.com']);

        User::factory()->create([
            'email' => 'admin@example.com',
            'name' => 'Lama',
        ]);

        $this->artisan('portfolio:make-admin')
            ->expectsQuestion('Nama admin', 'Baru')
            ->expectsQuestion('Kata sandi', 'newpass123')
            ->expectsQuestion('Konfirmasi kata sandi', 'newpass123')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'name' => 'Baru',
        ]);
        $this->assertEquals(1, User::where('email', 'admin@example.com')->count());
    }

    /** Perintah gagal jika konfirmasi kata sandi tidak cocok */
    public function test_fails_if_passwords_dont_match(): void
    {
        config(['portfolio.admin_email' => 'admin@example.com']);

        $this->artisan('portfolio:make-admin')
            ->expectsQuestion('Nama admin', 'Admin')
            ->expectsQuestion('Kata sandi', 'secret123')
            ->expectsQuestion('Konfirmasi kata sandi', 'salah')
            ->expectsOutput('Kata sandi tidak cocok.')
            ->assertExitCode(1);
    }

    /** Perintah membaca email dari config, bukan env() langsung */
    public function test_reads_email_from_config(): void
    {
        // Simulasi config:cache: set config manual, env() bisa berbeda
        config(['portfolio.admin_email' => 'dari-config@example.com']);

        $this->artisan('portfolio:make-admin')
            ->expectsQuestion('Nama admin', 'Admin')
            ->expectsQuestion('Kata sandi', 'pass1234')
            ->expectsQuestion('Konfirmasi kata sandi', 'pass1234')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', ['email' => 'dari-config@example.com']);
    }
}
