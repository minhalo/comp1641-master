<?php

namespace App\Transformers\Api;


use App\Models\Post;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    public function transform(Post $post): array
    {
        return [
            'name' => $post->name,
            'description' => $post->description,
            'content' => $post->content,
            'image' => $post ->image,
            'start_date' => $post->start_date,
            'end_date' => $post->end_date,
            'views'=> $post->views
        ];
    }
}


