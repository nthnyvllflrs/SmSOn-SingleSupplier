<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create(['username' => 'administrator', 'password' => 123456789, 'role' => 'Administrator']);
    }
}
