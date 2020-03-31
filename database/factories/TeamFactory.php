<?php

/** @var Factory $factory */

use App\Team;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Team::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
