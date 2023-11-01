<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewPostNotification;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Symfony\Component\HttpFoundation\Session\Storage\Handler\commit;

class PostController extends Controller
{

    public function __construct()
    {
        return $this->middleware(['auth:sanctum']);
    }

    public function onePost(){

        $data = [];
//        $post = Post::query()->find(1)->get();
//        foreach ($post->users() as $user){
//            $data[] = $user;
//        }
        return $data;
    }
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection | JsonResponse
     */


    public function index()
    {

        $posts = Post::query()->latest('id')
            ->when(Auth::user()->isAuthor() || Auth::user()->isUser(),function ($query){
                $query->where('user_id',Auth::id());
            })
            ->when(request('trash'),function ($q){
                $q->onlyTrashed();
            })
            ->search()
            ->with(['user','category'])
            ->paginate(7)
            ->withQueryString()
        ;

        return PostResource::collection($posts);
    }

    /**
     *
     * @return JsonResponse
     */

    public function trashes()
    {
        $deletedPostCount = Post::onlyTrashed()->count();
        $posts = Post::latest("id")
            ->when(Auth::user()->isAuthor(),function ($q){
                $q->where('user_id',Auth::id());
            })
            ->onlyTrashed()
            ->with(['category','user'])
            ->paginate(7)
            ->withQueryString();

        return response()->json([
            'data' => $posts,
            'deletePostCount' => $deletedPostCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
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
                $auth_id = Auth::id();
                Storage::putFileAs('public/'.$auth_id.'/feature_image',$file,$newName);
                $feature_image_name = $newName;
            }

            $post = Post::query()->create([
                'title' => $request->title,
                'description' => $request->description,
                'uuid' => Str::uuid(),
                'slug' => Str::slug($request->title),
                'excerpt' => Str::words($request->description,7,' >>>'),
                'user_id' =>  Auth::id(),
                'category_id' => $request->category_id,
                'feature_image' => $feature_image_name,
            ]);

            $roleToFilter = 'user';
            $subscribers = User::query()->where('role',"like", $roleToFilter)->get();
//            $subscribers = $post->users()->where('role', 'like', $roleToFilter)->get();
            foreach ($subscribers as $subscriber) {
//                Log::debug($subscriber);
                auth()->user()->notify(new NewPostNotification($subscriber,$post));
            }

            DB::commit();
            return response()->json([
                'message' => 'New Post is recorded',
                'data' => new PostResource($post),
            ],201);
        }catch (Exception $exception){
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
     * @param Post $post
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
     * @param Request $request
     * @param Post $post
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
                $auth_id = $post->user_id;
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
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Post $post)
    {

        Gate::authorize('delete',$post);
        if ($post->feature_image) {
            $path = '/public/' . $post->user_id . '/feature_image/' . $post->feature_image;
            Storage::delete($path);
        }
        $post->delete();

        return response()->json([
            'message' => 'Post is deleted',
        ]);

    }

    public function restore($post)
    {
        $post = Post::onlyTrashed()->findOrFail($post);
        Gate::authorize('restore',$post);
        $title=$post->title;
        $post->restore();

        return response()->json([
            "status"=>$title." is restore successfully"
        ]);
    }

    public function forceDelete($post)
    {
        $post = Post::onlyTrashed()->findOrFail($post);
        Gate::authorize('forceDelete',$post);
        try {
            DB::beginTransaction();
            $title = $post->title;

            if ($post->feature_image) {
                $path = '/public/' . $post->user_id . '/feature_image/' . $post->feature_image;
                Storage::delete($path);
            }

            $post->forceDelete();
            DB::commit();

        }catch (\Exception $error){
            DB::rollBack();
            return response()->json([
                "error"=> "-->".$error->getMessage()
            ]);
        }
//        $paginator = Post::paginate($this->defaultPaginateCount())->withQueryString();
//        $redirectToPage = (request("page") <= $paginator->lastPage()) ? request('page') : $paginator->lastPage();

        return response()->json([
            "message"=>$title." is deleted successfully"
        ]);
    }
}
