<?php

use App\Models\Article;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ArticlesTableSeeder extends Seeder
{
    public function run () {
        $faker = Factory::create();
        foreach (range(1, 100) as $item) {
            $article = new Article();
            $article->user_id = rand(1, 20);
            $article->slug = str_slug(implode(' ', $faker->unique()->words(6)));
            $article->title = $faker->realText(rand(20, 25), 5);
            $article->content = $faker->realText(rand(1000, 5000), 5);
            $article->created_at = $faker->dateTime();
            $article->updated_at = $article->created_at;
            $article->save();
            $tagIds = range(1, 30);
            array_shift($tagIds);
            $shuffled = array_splice($tagIds, 0, 5);
            $article->tags()->attach($shuffled);
        }
    }
}
