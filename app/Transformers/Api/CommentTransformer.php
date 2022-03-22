<?php

namespace App\Transformers\Api;


use App\Models\Comment;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    public function transform(Comment $comment): array
    {
        return [
            'content' => $comment->content,
            'like' => $comment->like,
            'dislike' => $comment->dislike,
        ];
    }
}


