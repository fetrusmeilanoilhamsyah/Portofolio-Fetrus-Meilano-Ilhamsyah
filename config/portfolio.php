<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Email
    |--------------------------------------------------------------------------
    |
    | Email untuk akun admin portofolio. Dibaca dari variabel lingkungan
    | ADMIN_EMAIL. Selalu akses via config('portfolio.admin_email')
    | (bukan env() langsung) agar tetap bekerja saat config:cache dipakai.
    |
    */
    'admin_email' => env('ADMIN_EMAIL', ''),
];
