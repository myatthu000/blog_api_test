<?php

namespace App\Http\Controllers;

use App\Models\User;
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

//     password change
//     phone number added and change


}
