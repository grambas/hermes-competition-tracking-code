<?php

$factory->define(App\Parcel::class, function (Faker\Generator $faker) {
    return [
        "tracking" => $faker->name,
        "s_fname" => $faker->name,
        "s_lname" => $faker->name,
        "s_street" => $faker->name,
        "r_fname" => $faker->name,
        "r_lname" => $faker->name,
        "r_street" => $faker->name,
    ];
});
