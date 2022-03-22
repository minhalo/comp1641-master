<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


// use App\Http\Controllers\Controller;
use App\Models\Comment;
// use Illuminate\Http\Request;
use App\Constants\StatusConst;
use Illuminate\Http\JsonResponse;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\Rule;
use Laravel\Jetstream\Jetstream;
use App\Transformers\Api\CommentTransformer;


class CommentController extends Controller
{
    //   * @return JsonResponse
     
    public function index()
    {
        $posts = Post::has('comments');
        return response()->json(fractal($post, new CommentTransformer())->toArray());
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        // 'content',
        // 'like',
        // 'dislike',
        // 'post_id',
        // 'user_id'
        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string', 'min:6', 'max:30'],
            'like' => ['integer'],
            'dislike' => ['integer'],
            'post_id' => ['integer'],
            'user_id' => ['integer']
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Comment::create(
            [
                // 'name' => $request->name,
                'content' => $request->content,
                'like' => $request->like,
                'dislike' => $request->dislike,
                'post_id' => $request->post_id,
                'user_id' => $request->user_id
            ]
        );
        return response()->json(['message' => 'Comment created successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        // $post = Comment::find($id);
        // $comment = Comment::where('post_id', $id)->first();
        $posts = Post::has('comments');

        if(!$posts) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json(fractal($posts, new CommentTransformer())->toArray());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        Comment::destroy($id);

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }
}
