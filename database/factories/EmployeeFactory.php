<?php

use Faker\Generator as Faker;

$factory->define(App\Model\Employee::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'employee_id' => rand(10,100),
        'gender' => 'Male',
        'experience_number' => rand(10,100),
        'priority_gateway'=>'IGW',
        'created_by'=>'Tahmid'
    ];
});
