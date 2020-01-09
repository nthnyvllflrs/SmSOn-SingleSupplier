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
        \App\Supplier::create(['user_id' => 2, 'name' => 'Supplier One', 'description' => 'Supplier One', 'address' => 'Zamboanga City', 'contact_number' => '09051406827', 'image_url' => 'https://res.cloudinary.com/nthnyvllflrs/image/upload/v1550622836/samples/stock-placeholder.jpg']);

        \App\Supplier::create(['user_id' => 3, 'name' => 'Supplier Two', 'description' => 'Supplier Two', 'address' => 'Zamboanga City', 'contact_number' => '09051406828', 'image_url' => 'https://res.cloudinary.com/nthnyvllflrs/image/upload/v1550622836/samples/stock-placeholder.jpg']);
    }
}
