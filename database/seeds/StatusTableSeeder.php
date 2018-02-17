<?php

use Illuminate\Database\Seeder;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            
            ['id' => 1, 'located' => 0, 'desc' => 'Die Sendung wurde angekündigt'],
            ['id' => 2, 'located' => 0, 'desc' => 'Die Sendung ist im Hermes PaketShop eingegangen'],
            ['id' => 3, 'located' => 0, 'desc' => 'Die Sendung ist am PaketShop eingegangen'],
            ['id' => 4, 'located' => 0, 'desc' => 'Die Sendung wurde am PaketShop abgeholt'],
            ['id' => 5, 'located' => 1, 'desc' => 'Die Seundung ist im Hermes Verteilzentrum [#] eingetroffen.'],
            ['id' => 6, 'located' => 1, 'desc' => 'Die Sendung wurde [in #] sortiert'],
            ['id' => 7, 'located' => 1, 'desc' => 'Die Sendung befindet sich im Hermes Verteilzentrum [#]'],
            ['id' => 8, 'located' => 0, 'desc' => 'Die Sendung wurde zugestellt'],
            ['id' => 9, 'located' => 1, 'desc' => 'Die Sendung ist in der Hermes Niederlassung [#] eingetroffen'],


        ];

        foreach ($items as $item) {
            \App\Status::create($item);
        }
    }
}
