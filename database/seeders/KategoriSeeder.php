<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Kategori::create(['nama' => 'Kategori 1']);
        Kategori::create(['nama' => 'Kategori 2']);
        Kategori::create(['nama' => 'Kategori 3']);
    }
}
