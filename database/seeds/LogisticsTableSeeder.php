<?php

use Illuminate\Database\Seeder;

class LogisticsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Logistic::create(['user_id' => 4, 'supplier_id' => 1, 'name' => 'Supplier One Logistic One', 'contact_number' => '09051406827']);

        \App\Logistic::create(['user_id' => 5, 'supplier_id' => 2, 'name' => 'Supplier Two Logistic One', 'contact_number' => '09051406828']);
    }
}
