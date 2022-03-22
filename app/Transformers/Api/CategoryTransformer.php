<?php

namespace App\Transformers\Api;


use App\Models\Category;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform(Category $category): array
    {
        return [
            'name' => $category->name,
            'description' => $category->description,
        ];
    }
}


