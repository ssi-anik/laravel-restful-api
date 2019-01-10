<?php

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run () {
        $faker = Factory::create();
        foreach (range(1, 20) as $item) {
            $user = new User();
            $user->name = $faker->name;
            $user->email = $faker->unique()->safeEmail;
            $user->password = 123456;
            $user->profile_picture = $faker->imageUrl(175, 98, 'people');
            $user->save();
        }
    }
}
