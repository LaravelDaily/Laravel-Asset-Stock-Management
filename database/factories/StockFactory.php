<?php

/** @var Factory $factory */

use App\Asset;
use App\Stock;
use App\Team;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Stock::class, function (Faker $faker) {
    return [
        'current_stock' => 0,
        'asset_id'      => Asset::inRandomOrder()->first()->id,
        'team_id'       => Team::inRandomOrder()->first()->id,
    ];
});
