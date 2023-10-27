<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name' => 'required|min:3|max:10',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:4|max:15',
            'device_name' => 'nullable',
        ]);


        $user = User::query()->create([
            'name' => $request->name,
            'uuid' => Str::uuid(),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

            if (Auth::attempt(\request()->only(['email','password'])))
            {
                $expiresAt = Date::now()->addDay();
                $token = Auth::user()->createToken($request->userAgent(),["*"],$expiresAt)->plainTextToken;
                return response()->json([
                    'token' => $token,
                    'user' => $user,
                ]);
            }

    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'email' => ['The provided credentials are incorrect.'],
            ],401);
        }
        $expiresAt =  Date::now()->addDay();
        $token = $user->createToken($request->userAgent(),['*'],$expiresAt)->plainTextToken;

        return response()->json([
            "token" => $token,
            "user" => $user,
        ]);
    }

    public function tokens(){
        $tokens = auth()->user()->tokens;

        return response()->json([
            "tokens" => $tokens,
        ]);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "message" => ["Logout success."],
        ]);
    }

    public function logoutAll(Request $request){

        $user = Auth::user()->tokens();
        $user->delete();
        return response()->json([
            "message" => "User logout successfully.",
            "token_info" => $user,
        ],204);
    }

    public function changePassword(Request $request){

        $user = Auth::user();
        $request->validate([
            'name' => 'nullable|min:3|max:10',

            'current_password' => 'required',
            'password' => 'required|confirmed|min:4|max:15',

            'avatar' => 'required|file|mimes:jpg,png,jpeg,gif',
            'phone_number' => 'nullable|numeric|min:5',
        ]);


        if(!Hash::check($request->input('current_password'),$user->password)){
            throw response()->json([
                'message' => ['Current password is incorrect.'],
            ]);
        }

        if($request->filled('name')){
            $user->name = $request->input('name');
        }

        if($request->filled('phone_number')){
            $user->name = $request->input('phone_number');
        }

        //avatar
        if ($request->hasFile('avatar')){
            $auth_id = $user->id;
            $path = '/public/'.$auth_id.'/avatar/'.$user->avatar;
            Storage::delete($path);

            $file = $request->file('avatar');
            $newName = uniqid().'_avatar.'.$file->getClientOriginalExtension();
            $auth_id = $user->id;
            Storage::putFileAs('public/'.$auth_id.'/avatar',$file,$newName);
            $feature_image_name = $newName;

            $user->avatar = $feature_image_name;
        }

        $user->password = Hash::make($request->input('password'));

        $user->save();

        return response()->json([
            'message' => ['User information changed successfully.'],
        ]);

    }
}
