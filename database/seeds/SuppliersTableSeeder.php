<?php

use Illuminate\Database\Seeder;

class SuppliersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Supplier::create(['user_id' => 2, 'name' => 'Supplier One', 'description' => 'Supplier One', 'address' => 'Zamboanga City', 'contact_number' => '09051406827']);

        \App\Supplier::create(['user_id' => 3, 'name' => 'Supplier Two', 'description' => 'Supplier Two', 'address' => 'Zamboanga City', 'contact_number' => '09051406828']);
    }
}
