<?php

use App\Models\Article;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ArticlesTableSeeder extends Seeder
{
    public function run () {
        $faker = Factory::create();
        foreach (range(1, 105) as $item) {
            $article = new Article();
            $article->user_id = rand(1, 20);
            $article->slug = str_slug(implode(' ', $faker->unique()->words(6)));
            $article->title = $faker->realText(rand(20, 25), 5);
            $article->content = $faker->realText(rand(1000, 5000), 5);
            $article->created_at = $faker->dateTimeThisDecade();
            $article->updated_at = $article->created_at;
            $article->save();
            $tagIds = $this->getTagIds(rand(3, 7));
            $article->tags()->attach($tagIds);
        }
    }

    /*
     * Makes sure the tags are unique
     **/
    private function getTagIds ($count = 5) {
        $tagIds = range(1, 30);
        shuffle($tagIds);
        $shuffled = array_splice($tagIds, 0, $count);
        $shuffled = array_unique($shuffled);

        while (count($shuffled) < $count) {
            $missing = count($shuffled) - $count;
            $shuffled = array_merge($shuffled, $this->getTagIds($missing));
            $shuffled = array_unique($shuffled);
        }

        return $shuffled;
    }
}
