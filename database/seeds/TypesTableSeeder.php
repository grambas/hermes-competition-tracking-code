<?php

use Illuminate\Database\Seeder;

class TypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	$items = [
            
            ['id' => 1, 'desc' => 'Hermes Päckchen'],
            ['id' => 2, 'desc' => 'S-Paket'],
            ['id' => 3, 'desc' => 'M-Paket'],
            ['id' => 4, 'desc' => 'L-Paket'],
            ['id' => 5, 'desc' => 'XL-Paket'],
            ['id' => 6, 'desc' => 'XXL-Paket'],
            ['id' => 7, 'desc' => 'S-Paketabholung'],
            ['id' => 8, 'desc' => 'M-Paketabholung'],
            ['id' => 9, 'desc' => 'L-Paketabholung'],
            ['id' => 10, 'desc' => 'XL-Paketabholung'],
            ['id' => 11, 'desc' => 'XXL-Paketabholung'],
            ['id' => 12, 'desc' => 'Reisegepäck'],
            ['id' => 13, 'desc' => 'XSport- und Sondergepäck'],
            ['id' => 14, 'desc' => 'Fahrrad'],
            ['id' => 15, 'desc' => 'XS-Paket International'],
            ['id' => 16, 'desc' => 'S-Paket International'],
            ['id' => 17, 'desc' => 'M-Paket International'],
            ['id' => 18, 'desc' => 'L-Paket International'],
            ['id' => 19, 'desc' => 'XL-Paket International'],
            ['id' => 20, 'desc' => 'XXL-Paket International'],

        ];

        foreach ($items as $item) {
            \App\Type::create($item);
        }
    }
}

