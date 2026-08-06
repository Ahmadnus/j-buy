<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Creates (or updates) the dashboard administrator account.
 *
 * Credentials are read from the environment — never hardcoded — so this file
 * is safe to commit. Set these in .env before running:
 *
 *   ADMIN_EMAIL=you@example.com
 *   ADMIN_PASSWORD=<a strong password>
 *   ADMIN_PHONE=0790000000        # optional, phone is the login identifier
 *   ADMIN_USERNAME=admin          # optional
 *   ADMIN_NAME_AR=المدير           # optional
 *
 * Then either of:
 *   php artisan db:seed --class=AdminUserSeeder
 *   php artisan db:seed                          # runs as part of the chain
 *
 * Without ADMIN_EMAIL and ADMIN_PASSWORD it prints a warning and skips, so a
 * plain db:seed still succeeds.
 *
 * Re-running is safe: the account is matched on email and updated in place,
 * so this doubles as a password reset. It never touches other users.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        // Skip rather than invent a default. This seeder runs as part of the
        // full `db:seed` chain, so throwing here would break seeding for anyone
        // who has not set these — but falling back to a guessable password is
        // how test credentials reach production. Skipping is the safe middle.
        if (blank($email) || blank($password)) {
            $this->command->warn(
                'AdminUserSeeder skipped: set ADMIN_EMAIL and ADMIN_PASSWORD in .env to create the admin account.'
            );

            return;
        }

        // The dashboard authenticates over the session guard, so the role must
        // be created on the 'web' guard for hasRole('admin') to match.
        $role = Role::firstOrCreate([
            'name'       => 'admin',
            'guard_name' => 'web',
        ]);

        $user = User::withTrashed()->updateOrCreate(
            ['email' => $email],
            [
                'name_ar'  => env('ADMIN_NAME_AR', 'المدير'),
                'username' => env('ADMIN_USERNAME', 'admin'),
                'phone'    => env('ADMIN_PHONE', '0790000000'),
                // No 'hashed' cast on this model, so hash explicitly.
                'password' => Hash::make($password),
            ]
        );

        // 'is_active' and 'deleted_at' are absent from User::$fillable, so mass
        // assignment would drop them silently. Set them directly instead —
        // this is what revives a soft-deleted or deactivated admin.
        $user->is_active  = true;
        $user->deleted_at = null;
        $user->save();

        $user->assignRole($role);

        $this->command->info("Admin ready: {$email}");
        $this->command->warn('Password was taken from ADMIN_PASSWORD. Remove it from .env once you have logged in.');
    }
}
