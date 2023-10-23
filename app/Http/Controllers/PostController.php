<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Symfony\Component\HttpFoundation\Session\Storage\Handler\commit;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection | JsonResponse
     */
    public function index()
    {

        $posts = Post::query()->latest('id')
//            ->when(Auth::user()->isAuthor(),function ($query){
//                $query->where('user_id',Auth::id());
//            })
            ->search()
            ->with(['user','category'])
            ->paginate(3)
            ->withQueryString()
        ;

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePostRequest $request)
    {
//        Gate::authorize('create',Post::class);
        try {
            DB::beginTransaction();
            $feature_image_name = '';
            if ($request->hasFile('feature_image')){
                $file = $request->file('feature_image');
                $newName = uniqid().'_feature_image_.'.$file->getClientOriginalExtension();
                $auth_id = '3';
                Storage::putFileAs('public/'.$auth_id.'/feature_image',$file,$newName);
                $feature_image_name = $newName;
            }


            $post = Post::query()->create([
                'title' => $request->title,
                'description' => $request->description,
                'uuid' => Str::uuid(),
                'slug' => Str::slug($request->title),
                'excerpt' => Str::words($request->description,7,' >>>'),
                'user_id' =>  3,
                'category_id' => $request->category_id,
                'feature_image' => $feature_image_name,
            ]);
            DB:commit();
            return response()->json([
                'message' => 'New Post is recorded',
                'data' => new PostResource($post),
            ],201);
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json([
                'error' => 'Fail to create New Post',
                'message' => $exception,
            ],404);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return JsonResponse
     */
    public function show(Post $post){

//        Gate::authorize('view',$post);
        return response()->json([
            'data' => new PostResource($post),
        ],201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return JsonResponse
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
//        Gate::authorize('update',$post);
//        try {
//            DB::beginTransaction();
            $feature_image_name = null;
            if ($request->hasFile('feature_image')){
                $path = '/public/'.$post->user_id.'/feature_image/'.$post->feature_image;
                Storage::delete($path);

                $file = $request->file('feature_image');
                $newName = uniqid().'_feature_image_.'.$file->getClientOriginalExtension();
                $auth_id = '3';
                Storage::putFileAs('public/'.$auth_id.'/feature_image',$file,$newName);
                $feature_image_name = $newName;
            }

            $post->update([
                'title' => $request->title,
                'description' => $request->description,
                'uuid' => $post->uuid,
                'slug' => Str::slug($request->title),
                'excerpt' => Str::words($request->description,7,' >>>'),
                'user_id' =>  $post->user_id,
                'category_id' => $request->category_id,
                'feature_image' => $feature_image_name == null ? $post->feature_image : $feature_image_name,
            ]);
//            DB:commit();
            return response()->json([
                'message' => 'New Post is recorded',
                'data' => new PostResource($post),
            ],201);
//        }catch (\Exception $exception){
//            DB::rollBack();
//            return response()->json([
//                'error' => 'Fail to create New Post',
//                'message' => $exception,
//            ],404);
//        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return JsonResponse
     */
    public function destroy(Post $post)
    {

//        Gate::authorize('delete',$post);
        if ($post->feature_image) {
            $path = '/public/' . $post->user_id . '/feature_image/' . $post->feature_image;
            Storage::delete($path);
        }
        $post->delete();

        return response()->json([
            'message' => 'Post is deleted',
        ]);

    }
}
