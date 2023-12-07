<?php

namespace Database\Seeders;

use App\Models\CommonConnection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommonConnectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $commonConnections = CommonConnection::factory()->count(10)->create(); // Creating 10 common connections
    }
}
