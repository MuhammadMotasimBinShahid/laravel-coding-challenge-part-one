<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            RequestsSeeder::class,
            ConnectionsSeeder::class,
            CommonConnectionsSeeder::class,
        ]);
    }
}
