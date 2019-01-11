<?php namespace App\Transformers;

class ArticleTransformer extends BaseTransformer
{
    public function transform ($article) {
        $data = [
            'id'           => $article->id,
            'slug'         => $article->slug,
            'title'        => $article->title,
            'content'      => substr($article->content, 0, 750),
            'published_at' => $article->created_at->format('D jS M, Y h:i:s'),
        ];

        if ($article->relationLoaded('tags')) {
            $data = array_merge($data, [ 'tags' => $article->tags->pluck('content') ]);
        }

        if ($article->relationLoaded('user')) {
            $user = [
                'id'   => $article->user->id,
                'name' => $article->user->name,
            ];

            $data = array_merge($data, [ 'user' => $user ]);
        }

        return $data;
    }

}