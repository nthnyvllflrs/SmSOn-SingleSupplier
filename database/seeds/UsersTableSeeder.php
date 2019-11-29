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

        \App\User::create(['username' => 'SupplierOne', 'password' => 123456789, 'role' => 'Supplier']);
        \App\User::create(['username' => 'SupplierTwo', 'password' => 123456789, 'role' => 'Supplier']);

        \App\User::create(['username' => 'SupplierOneLogisticOne', 'password' => 123456789, 'role' => 'Logistic']);
        \App\User::create(['username' => 'SupplierTwoLogisticOne', 'password' => 123456789, 'role' => 'Logistic']);

        \App\User::create(['username' => 'CustomerOne', 'password' => 123456789, 'role' => 'Customer']);
        \App\User::create(['username' => 'CustomerTwo', 'password' => 123456789, 'role' => 'Customer']);
    }
}
