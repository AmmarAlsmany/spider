<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\contracts_types;
use App\Models\Team;
use App\Models\tikets;
use Database\Seeders\TargetInsectSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the TargetInsectSeeder
        $this->call([
            TargetInsectSeeder::class,
        ]);
        
        User::create([
            'name' => 'sales man',
            'email' => 'sales@spider.com',
            'password' => bcrypt('sales123'),
            'role' => 'sales',
            'status' => 'active',
            'phone' => '08123456789',
            'address' => 'Jl. Raya No. 123',
        ]);

        User::create([
            'name' => 'sales manager',
            'email' => 'sales_manager@spider.com',
            'password' => bcrypt('sales_manager123'),
            'role' => 'sales_manager',
            'status' => 'active',
            'phone' => '08123456789',
            'address' => 'Jl. Raya No. 123',
        ]);

        User::create([
            'name' => 'technical manager',
            'email' => 'technical_manager@spider.com',
            'password' => bcrypt('technical_manager123'),
            'role' => 'technical',
            'status' => 'active',
            'phone' => '08123456789',
            'address' => 'Jl. Raya No. 123',
        ]);

        User::create([
            'name' => 'admin',
            'email' => 'admin@spider.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '08123456789',
            'address' => 'Jl. Raya No. 123',
        ]);

        User::create([
            'name' => 'finance',
            'email' => 'finance@spider.com',
            'password' => bcrypt('finance123'),
            'role' => 'finance',
            'status' => 'active',
            'phone' => '08123456789',
            'address' => 'Jl. Raya No. 123',
        ]);

        $data = [
            ['name' => 'Bed bug', 'status' => 'active'],
            ['name' => 'Termite before construction', 'status' => 'active'],
            ['name' => 'Termite after construction', 'status' => 'active'],
            ['name' => 'one spray', 'status' => 'active'],
            ['name' => 'Annual contract', 'status' => 'active'],
            ['name' => 'Buy equipment', 'status' => 'active'],
        ];

        contracts_types::insert($data);

        //create worker
        $workers = [
            [
                'name' => 'worker 1',
                'email' => 'worker1@worker1.com',
                'password' => bcrypt('worker123'),
                'phone' => '08123456789',
                'address' => 'Jl. Raya No. 123',
                'role' => 'worker',
                'status' => 'active',
            ],
            [
                'name' => 'worker 2',
                'email' => 'worker2@worker2.com',
                'password' => bcrypt('worker123'),
                'phone' => '08123456789',
                'address' => 'Jl. Raya No. 123',
                'role' => 'worker',
                'status' => 'active',
            ],
            [
                'name' => 'worker 3',
                'email' => 'worker3@worker3.com',
                'password' => bcrypt('worker123'),
                'phone' => '08123456789',
                'address' => 'Jl. Raya No. 123',
                'role' => 'worker',
                'status' => 'active',
            ],
            [
                'name' => 'worker 4',
                'email' => 'worker4@worker4.com',
                'password' => bcrypt('worker123'),
                'phone' => '08123456789',
                'address' => 'Jl. Raya No. 123',
                'role' => 'worker',
                'status' => 'active',
            ],
        ];

        //create team leader
        $team_leaders = [
            [
                'name' => 'team leader 1',
                'email' => 'teamleader1@teamleader1.com',
                'password' => bcrypt('teamleader123'),
                'phone' => '08123456789',
                'address' => 'Jl. Raya No. 123',
                'role' => 'team_leader',
                'status' => 'active',
            ],
            [
                'name' => 'team leader 2',
                'email' => 'teamleader2@teamleader2.com',
                'password' => bcrypt('teamleader123'),
                'phone' => '08123456789',
                'address' => 'Jl. Raya No. 123',
                'role' => 'team_leader',
                'status' => 'active',
            ],
        ];

        // create team
        $teams = [
            [
                'name' => 'team 1',
                'team_leader_id' => 1,
                'members' => [1, 2],
                'description' => 'Team 1 description',
                'status' => 'active',
            ],
            [
                'name' => 'team 2',
                'team_leader_id' => 2,
                'members' => [3, 4],
                'description' => 'Team 2 description',
                'status' => 'active',
            ],
        ];

        User::insert($workers);
        User::insert($team_leaders);
        // Team::insert($teams);
    }
}
