<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('DevelopmentTableSeeder');
    }
}

class DevelopmentTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert(
            [
                ['name' => 'Admin'],
                ['name' => 'User'],
            ]
        );

        DB::table('users')->insert(
            [
                'firstName' => "Brave",
                'lastName' => "Administrator",
                'role_id' => 1,
                'email' => 'admin@brave.com',
                'password' => Hash::make('admin'),
            ]
        );

        DB::table('status')->insert(
            [
                ['name' => 'ACTIVE', 'description' => ''],
                ['name' => 'REDEEMED', 'description' => ''],
                ['name' => 'EXPIRED', 'description' => ''],
                ['name' => 'DEACTIVATED', 'description' => ''],
            ]
        );

    }
}
