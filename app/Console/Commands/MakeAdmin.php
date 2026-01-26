<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-admin {email} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user with the given email and password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->option('password');

        if (!$password) {
            $this->error('Password is required. Use --password option.');
            return 1;
        }

        // Check if user already exists
        $user = User::where('email', $email)->first();

        if ($user) {
            $this->info("User with email {$email} already exists. Updating role to Admin...");
        } else {
            // Create new user
            $user = User::create([
                'name' => 'Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'Admin',
                'email_verified_at' => now(),
            ]);
            $this->info("User created successfully.");
        }

        // Ensure Admin role exists
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        // Assign Admin role to user
        if (!$user->hasRole('Admin')) {
            $user->assignRole($adminRole);
            $this->info("Admin role assigned to {$email}");
        } else {
            $this->info("User {$email} already has Admin role.");
        }

        $this->info("Admin user setup completed successfully!");
        return 0;
    }
}
