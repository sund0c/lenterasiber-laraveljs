<?php

namespace App\Console\Commands;

use App\Models\AdminUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * php artisan admin:create
 *
 * Run this ONCE after migration to create the first admin account.
 * Delete or disable this command after use in production.
 */
class CreateAdminUser extends Command
{
    protected $signature   = 'admin:create';
    protected $description = 'Create a new admin user for the CMS panel';

    public function handle(): int
    {
        $this->info('=== Lentera Siber — Create Admin User ===');
        $this->warn('Run this ONCE, then you may disable this command.');

        $username = $this->ask('Username (alphanumeric, 3-40 chars)');
        $fullName = $this->ask('Full name');
        $email    = $this->ask('Email address');

        $password = $this->secret('Password (min 12 chars, upper+lower+digit+symbol)');
        $confirm  = $this->secret('Confirm password');

        // Validate
        $v = Validator::make(compact('username', 'email', 'password'), [
            'username' => ['required', 'alpha_dash', 'min:3', 'max:40', 'unique:admin_users'],
            'email'    => ['required', 'email', 'max:255', 'unique:admin_users'],
            'password' => [
                'required', 'min:12',
                'regex:/[A-Z]/', 'regex:/[a-z]/',
                'regex:/[0-9]/', 'regex:/[\W]/',
            ],
        ], [
            'password.regex' => 'Password harus mengandung huruf besar, kecil, angka, dan simbol.',
        ]);

        if ($v->fails()) {
            foreach ($v->errors()->all() as $err) {
                $this->error($err);
            }
            return self::FAILURE;
        }

        if ($password !== $confirm) {
            $this->error('Password tidak cocok.');
            return self::FAILURE;
        }

        $user = AdminUser::create([
            'username'  => $username,
            'full_name' => $fullName,
            'email'     => $email,
            'password'  => $password, // hashed by model mutator
        ]);

        $this->info("✓ Admin user '{$username}' berhasil dibuat (ID: {$user->id}).");
        $this->info('Login di: ' . env('APP_URL') . env('ADMIN_LOGIN_PATH', '/portal-internal-x83fj9/login'));
        $this->warn('2FA akan diminta saat login pertama kali.');

        return self::SUCCESS;
    }
}
