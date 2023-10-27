<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(){

        $categories = Category::query()->latest('id')
            ->with(['user'])
            ->paginate(3)
            ->withQueryString()
        ;

        return CategoryResource::collection($categories);
    }

    public function store(CategoryStoreRequest $request){
//        Gate::authorize('create',Category::class);
        try {
            DB::beginTransaction();
            $category = Category::query()->create([
                'title' => $request->title,
                'uuid' => (string) Str::uuid(),
                'slug' => Str::slug($request->title),
                'user_id' => Auth::id(),
            ]);
            DB::commit();
            return response()->json([
                'message' => 'New Category is recorded',
                'data' => new CategoryResource($category),
            ],201);
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create the category',
                'data' => $exception->getMessage(),
            ],500);
        }
    }

    public function show(Category $category){
//        Gate::authorize('view',$category);

        return response()->json([
            'data' => new CategoryResource($category),
        ],201);
    }

    public function update(CategoryUpdateRequest $request, Category $category){
        Gate::authorize('update',$category);
        try {
            DB::beginTransaction();
            $category->update([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'user_id' => $category->user_id,
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Category is updated',
                'data' => new CategoryResource($category),
            ],201);
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update the category',
                'data' => $exception->getMessage(),
            ],500);
        }
    }

    public function destroy(Category $category){

//        Gate::authorize('delete',$category);
        $category->delete();

        return response()->json([
            'message' => 'Category is deleted'
        ]);
    }


}
