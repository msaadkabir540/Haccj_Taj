<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch users from API and insert them if not exists';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info("Cron Job running at " . now());

        // Get fake users from JSONPlaceholder API
        $response = Http::get('https://jsonplaceholder.typicode.com/users');
        $users = $response->json();

        if (!empty($users)) {
            foreach ($users as $user) {
                // Check if user already exists by email
                if (!User::where('email', $user['email'])->exists()) {
                    
                    DB::table('users')->insert([
                        'name'        => $user['name'],   // use API name
                        'employeecode'=> rand(1000, 9999),
                        'email'       => $user['email'], // use API email
                        'password'    => Hash::make('password123'),
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }
        }

        $this->info('DemoCron executed successfully!');
    }
}
