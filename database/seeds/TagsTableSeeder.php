<?php

use App\Models\Tag;
use Faker\Factory;
use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    public function run () {
        $faker = Factory::create();

        foreach (range(1, 30) as $item) {
            $tag = new Tag();
            $tag->user_id = rand(5, 20);
            $tag->content = $faker->unique()->word;
            $tag->save();
        }
    }
}
