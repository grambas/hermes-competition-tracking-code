<?php

$factory->define(App\Status::class, function (Faker\Generator $faker) {
    return [
        "desc" => $faker->name,
    ];
});
