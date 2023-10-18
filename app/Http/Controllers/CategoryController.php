<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(){

        $categories = Category::latest('id')
            ->with(['user'])
            ->paginate(3)
            ->withQueryString()
        ;
//        return response()->json([
//            'data' => CategoryResource::collection($categories),
//            'data' => new CategoryResource($categories),
//        ],200);
        return CategoryResource::collection($categories);

    }

    public function store(CategoryStoreRequest $request){

        try {
            DB::beginTransaction();
            $category = Category::query()->create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'user_id' => 1,
            ]);
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
        }

        return $category;
    }


    public function show(Category $category){

        return $category;
    }

    public function update(CategoryUpdateRequest $request){
        //
    }

    public function destroy(Category $category){
        //
    }
}
