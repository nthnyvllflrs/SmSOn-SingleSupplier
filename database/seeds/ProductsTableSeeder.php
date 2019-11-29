<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Supplier One
        \App\Product::create(['supplier_id' => 1, 'code' => 'SP10001' , 'name' => 'SO Product One', 'description' => 'Supplier One Product One', 'unit' => 'Box', 'price' => 1000.00]);

        \App\Product::create(['supplier_id' => 1, 'code' => 'SP10002' , 'name' => 'SO Product Two', 'description' => 'Supplier One Product Two', 'unit' => 'Pack', 'price' => 2000.00]);

        \App\Product::create(['supplier_id' => 1, 'code' => 'SP10003' , 'name' => 'SO Product Three', 'description' => 'Supplier One Product Three', 'unit' => 'Bundle', 'price' => 3000.00]);

        \App\Product::create(['supplier_id' => 1, 'code' => 'SP10004' , 'name' => 'SO Product Four', 'description' => 'Supplier One Product Four', 'unit' => 'Dozen', 'price' => 4000.00]);

        \App\Product::create(['supplier_id' => 1, 'code' => 'SP10005' , 'name' => 'SO Product Five', 'description' => 'Supplier One Product Five', 'unit' => 'Piece', 'price' => 5000.00]);

        // Supplier Two
        \App\Product::create(['supplier_id' => 2, 'code' => 'SP20001' , 'name' => 'SO Product One', 'description' => 'Supplier Two Product One', 'unit' => 'Box', 'price' => 1000.00]);

        \App\Product::create(['supplier_id' => 2, 'code' => 'SP20002' , 'name' => 'SO Product Two', 'description' => 'Supplier Two Product Two', 'unit' => 'Pack', 'price' => 2000.00]);

        \App\Product::create(['supplier_id' => 2, 'code' => 'SP20003' , 'name' => 'SO Product Three', 'description' => 'Supplier Two Product Three', 'unit' => 'Bundle', 'price' => 3000.00]);

        \App\Product::create(['supplier_id' => 2, 'code' => 'SP20004' , 'name' => 'SO Product Four', 'description' => 'Supplier Two Product Four', 'unit' => 'Dozen', 'price' => 4000.00]);

        \App\Product::create(['supplier_id' => 2, 'code' => 'SP20005' , 'name' => 'SO Product Five', 'description' => 'Supplier Two Product Five', 'unit' => 'Piece', 'price' => 5000.00]);
    }
}
