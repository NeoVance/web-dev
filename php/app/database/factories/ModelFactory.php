<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => $faker->password,
        'auth_token' => $faker->numerify('token######')
    ];
});

$factory->define(App\Game::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence(),
        'turn' => $faker->randomDigit
    ];
});

$factory->define(App\State::class, function (Faker\Generator $faker) {
    return [
        'type' => $faker->randomElement([
            'pawn',
            'horse',
            'rook',
            'bishop',
            'queen',
            'king'
        ]),
        'color' => $faker->randomElement([
            'white',
            'black'
        ]),
        'xposition' => $faker->numberBetween(0, 7),
        'yposition' => $faker->numberBetween(0, 7),
        'active' => $faker->randomElement([
            true,
            false
        ])
    ];
})
