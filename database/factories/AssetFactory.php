<?php

/** @var Factory $factory */

use App\Asset;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Asset::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
