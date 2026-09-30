<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Reset2faCommandTest extends TestCase
{
    use RefreshDatabase;

    /** Perintah gagal jika ADMIN_EMAIL belum dikonfigurasi */
    public function test_fails_if_admin_email_not_configured(): void
    {
        config(['portfolio.admin_email' => '']);

        $this->artisan('portfolio:reset-2fa')
            ->expectsOutput('ADMIN_EMAIL belum diisi di .env (atau config/portfolio.php).')
            ->assertExitCode(1);
    }

    /** Perintah gagal jika pengguna admin tidak ditemukan */
    public function test_fails_if_admin_user_not_found(): void
    {
        config(['portfolio.admin_email' => 'admin@example.com']);

        $this->artisan('portfolio:reset-2fa')
            ->expectsOutput('Pengguna admin dengan email admin@example.com tidak ditemukan.')
            ->assertExitCode(1);
    }

    /** Perintah memberi tahu jika 2FA belum aktif */
    public function test_skips_if_2fa_not_enabled(): void
    {
        config(['portfolio.admin_email' => 'admin@example.com']);

        User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $this->artisan('portfolio:reset-2fa')
            ->expectsOutput('Pengguna admin@example.com belum mengaktifkan 2FA. Tidak ada yang perlu direset.')
            ->assertExitCode(0);
    }

    /** Perintah dibatalkan jika admin menjawab tidak */
    public function test_cancels_if_user_declines_confirmation(): void
    {
        config(['portfolio.admin_email' => 'admin@example.com']);

        $user = User::factory()->create(['email' => 'admin@example.com']);
        $user->saveAppAuthenticationSecret('FAKESECRET123456');

        $this->artisan('portfolio:reset-2fa')
            ->expectsConfirmation('Lanjutkan? (ketik yes untuk mengonfirmasi)', 'no')
            ->expectsOutput('Dibatalkan.')
            ->assertExitCode(0);

        // Secret masih ada
        $user->refresh();
        $this->assertNotNull($user->getAppAuthenticationSecret());
    }

    /** Perintah mereset 2FA setelah dikonfirmasi */
    public function test_resets_2fa_after_confirmation(): void
    {
        config(['portfolio.admin_email' => 'admin@example.com']);

        $user = User::factory()->create(['email' => 'admin@example.com']);
        $user->saveAppAuthenticationSecret('FAKESECRET123456');
        $user->saveAppAuthenticationRecoveryCodes(['CODE1', 'CODE2', 'CODE3']);

        $this->artisan('portfolio:reset-2fa')
            ->expectsConfirmation('Lanjutkan? (ketik yes untuk mengonfirmasi)', 'yes')
            ->assertExitCode(0);

        $user->refresh();
        $this->assertNull($user->getAppAuthenticationSecret());
        $this->assertNull($user->getAppAuthenticationRecoveryCodes());
    }
}
