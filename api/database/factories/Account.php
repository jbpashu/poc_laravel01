<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(Model::class, function (Faker $faker) {
    return [
        'account_type_id' => $faker->account_type_id,
        'industry_type_id' => $faker->industry_type_id,
        'company_name'  => $faker->company_name;
    ];
});
