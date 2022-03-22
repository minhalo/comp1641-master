<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Constants\StatusConst;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Jetstream;
use App\Transformers\Api\PostTransformer;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(fractal(Post::all(), new PostTransformer())->toArray());
    }

    /**
     * Sh
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:6', 'max:30'],
            'description' => ['min:6','max:255'],
            'slug' => ['string'],
            'content' => ['required', 'min:1'],
            'status' => ['required', 'string',  Rule::in([StatusConst::PUBLISHED, StatusConst::UNPUBLISHED , StatusConst::DRAFT])],
            'image' => ['required'],
            'views' => ['integer'],
            'start_date' => ['date'],
            'end_date' => ['date'],
            'category_id' => ['integer'],
            'user_id' => ['integer']
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Post::create(
            [
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'is_featured' => false,
                'slug' => $request->name,
                'content' => $request->content,
                'image' => $request->image,
                'views' => $request->views,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'category_id' => $request->category_id,
                'user_id' => $request->user_id
            ]
        );
        return response()->json(['message' => 'Post created successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        if(!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json(fractal($post, new PostTransformer())->toArray());
    }

    /**
     * Show the form for editing the specified resource.




     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if($post) {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'min:6', 'max:30'],
                'description' => ['min:6','max:255'],
                'slug' => ['string'],
                'content' => ['required', 'min:1'],
                'status' => ['required', 'string',  Rule::in([StatusConst::PUBLISHED, StatusConst::UNPUBLISHED , StatusConst::DRAFT])],
                'image' => ['required'],
                'views' => ['integer'],
                'start_date' => ['date'],
                'end_date' => ['date'],
                'category_id' => ['integer'],
                'user_id' => ['integer']
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            Post::update(
                [
                    'name' => $request->name,
                    'description' => $request->description,
                    'status' => $request->status,
                    'is_featured' => false,
                    'slug' => $request->name,
                    'content' => $request->content,
                    'image' => $request->image,
                    'views' => $request->views,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'category_id' => $request->category_id,
                    'user_id' => $request->user_id
                ]
            );
            return response()->json(['message' => 'Post update successfully'], 200);
        }
        return response()->json(['message' => 'Post not found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Post::destroy($id);

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
