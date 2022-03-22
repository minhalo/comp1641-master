<?php

namespace App\Http\Controllers\Api;

use App\Constants\StatusConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Category\StoreRequest;
use App\Models\Category;
use App\Transformers\Api\CategoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Jetstream;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
       return response()->json(fractal(Category::all(), new CategoryTransformer())->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        dd($request);
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:categories'],
            'description' => ['string','min:6','max:255'],
            'status' => ['required', 'string',  Rule::in([StatusConst::PUBLISHED, StatusConst::UNPUBLISHED , StatusConst::DRAFT])],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Category::create(
            [
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'is_featured' => false,
            ]
        );

        return response()->json(['message' => 'Category created successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $category = Category::find($id);

        if(!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json(fractal($category, new CategoryTransformer())->toArray());
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
        Category::destroy($id);

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
