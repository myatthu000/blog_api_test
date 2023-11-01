<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribedController extends Controller
{
    public function userSub($type){
        $user = auth()->user();
        if ($user){
            $user->subscriber = $type;
            $user->save();
            return $user;
        }
        return response()->json(['message' => 'User not found'], 404);
    }

    public function subscribed(){
        return $this->userSub("subscribed");
    }

    public function unsubscribed(){
        return $this->userSub("unsubscribed");
    }

    public function option(Request $request){
        $user_choice = $request->get('key');
        if ($user_choice === 'subscribed'){
            return $this->subscribed();
        }elseif ($user_choice === 'unsubscribed'){
            return $this->unsubscribed();
        }else{
            return $this->subscribed();
        }

    }
}
