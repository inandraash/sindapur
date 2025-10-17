<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            ['nama_menu' => 'Nasi Rames', 'harga' => 10000],
            ['nama_menu' => 'Nasi Rames Telur', 'harga' => 12000],
            ['nama_menu' => 'Nasi Rames Daging', 'harga' => 15000],
            ['nama_menu' => 'Nasi Pecel', 'harga' => 10000],
            ['nama_menu' => 'Soto Ayam', 'harga' => 10000],
            ['nama_menu' => 'Asem-Asem Daging', 'harga' => 18000],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
