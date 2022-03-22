<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'like',
        'dislike',
        'post_id',
        'user_id'
    ];
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

