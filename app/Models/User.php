<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    use InteractsWithAppAuthentication;
    use InteractsWithAppAuthenticationRecovery;

    /**
     * Hanya pengguna dengan email yang cocok dengan ADMIN_EMAIL yang boleh
     * masuk ke panel admin.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        $adminEmail = config('portfolio.admin_email');

        return filled($adminEmail) && strtolower($this->email) === strtolower($adminEmail);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // app_authentication_secret: encrypted via InteractsWithAppAuthentication trait
            // app_authentication_recovery_codes: encrypted:array via InteractsWithAppAuthenticationRecovery trait
        ];
    }
}
