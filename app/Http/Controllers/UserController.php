<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\Band;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profile(Request $request){
        $request->validate([
            'avatar' => 'nullable|file|mimes:jpg,png,jpeg',
        ]);

        $file = $request->file('avatar');
        $newName = uniqid().'_avatar.'.$file->getClientOriginalExtension();
        $auth_id = Auth::id();
        Storage::putFileAs('public/'.$auth_id.'/new_avatar',$file,$newName);
        $avatar = $newName;

        $user = User::query()->updateOrCreate([
            'avatar' => $avatar,
        ]);

        return response()->json([
            'message' => 'Profile image updated.'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $current_user
     * @param $key string
     * @param $type
     * @return string
     */
    protected function freeze($current_user,$key,$type){
        $current_user->freeze_action = $key;
        $message = "User name : ".$current_user->name."is ". $type." by ".Auth::user()->role;
        $edited = $current_user->update();
        return $message;
    }

    public function accessUser(Request $request){

        $request->validate([
            'id' => 'required',
            'key' => ['required','lowercase',new Band()]
        ]);

        global $message;

        $key = strtolower($request->get('key'));
        $currentUser = User::query()->findOrFail($request->id);
        if (!($currentUser->isAdmin() || $currentUser->isEditor()) )
        {
            if($key === 'band')
            {
                $message = $this->freeze($currentUser,'-1',"banded");
            }
            elseif($key === 'unband')
            {
                $message = $this->freeze($currentUser,'1',"unbanded");

            }else{
                $message = $this->freeze($currentUser,'-1',"banded");
            }
        }else{
            $message = 'You cannot band yourself.';
        }

        return response()->json([
            "status" => $message,
            "user" => $currentUser,
        ]);
    }


    public function toAdmin(Request $request){
        $current_user = $this->currentUser($request->id);
        if ($current_user->isEditor() || $current_user->isAuthor() || $current_user->isUser()){
            $current_user->role = 'admin';
            $current_user->update();
        }

        return response()->json([
            'message' => 'Administrators: Updated user role to admin.',
            'user' => $current_user,
        ]);
    }

    public function toEditor(Request $request){
        $current_user = $this->currentUser($request->id);
        if ($current_user->isAuthor() || $current_user->isUser() || $current_user->isEditor()){
            $current_user->role = 'editor';
            $current_user->update();
        }

        return response()->json([
            'message' => 'Administrators: Updated user role to editor.',
            'user' => $current_user,
        ]);
    }

    public function toAuthor(Request $request){
        $current_user = $this->currentUser($request->id);
        if ($current_user->isAuthor() || $current_user->isUser() || !($current_user->isEditor())){
            $current_user->role = 'author';
            $current_user->update();
        }

        return response()->json([
            'message' => 'Administrators: Updated user role to author.',
            'user' => $current_user,
        ]);
    }

    public function toUser(Request $request){
        $current_user = $this->currentUser($request->id);
        if ($current_user->isEditor() || $current_user->isAuthor() || $current_user->isUser()){
            $current_user->role = 'user';
            $current_user->update();
        }

        return response()->json([
            'message' => 'Administrators: Updated user role to user.',
            'user' => $current_user,
        ]);
    }

    protected function currentUser($id){
        return User::findOrFail($id);
    }


}
