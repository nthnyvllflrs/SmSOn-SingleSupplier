<?php

use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Customer::create(['user_id' => 6, 'name' => 'Customer One', 'address' => 'Zamboanga City', 'contact_number' => '09051406827']);

        \App\Customer::create(['user_id' => 7, 'name' => 'Customer Two', 'address' => 'Zamboanga City', 'contact_number' => '09051406828']);
    }
}
