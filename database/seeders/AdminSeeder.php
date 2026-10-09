<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Creates (or promotes) the portal administrator from ADMIN_NAME / ADMIN_EMAIL / ADMIN_PASSWORD.
     * Without ADMIN_PASSWORD a development-only password is used locally; outside `local`
     * nothing is created, so no default credentials can reach production.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@bouesti.test');
        $password = env('ADMIN_PASSWORD');

        if (! $password) {
            if (! app()->environment('local', 'testing')) {
                $this->command?->warn('AdminSeeder skipped: set ADMIN_PASSWORD in .env to create the admin account.');

                return;
            }

            $password = 'password';
            $this->command?->warn("Local admin created with the development password \"password\" ({$email}). Set ADMIN_PASSWORD before deploying.");
        }

        $user = User::firstOrNew(['email' => $email]);

        if (! $user->exists) {
            $user->name = env('ADMIN_NAME', 'Portal Administrator');
            $user->password = $password;
            $user->email_verified_at = now();
        }

        $user->role = User::ROLE_ADMIN;
        $user->save();
    }
}
