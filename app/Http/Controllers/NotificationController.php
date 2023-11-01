<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function optionTypes(Request $request){
        $type =  $request->get('type');

        if($type === 'unread'){
            return $this->unreadNotificaions();
        }elseif ($type === 'read'){
            return $this->readNotifications();
        }else{
            return $this->notifications();
        }
    }

    public function notifications(){

        $notifications = auth()->user()->notifications;

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function readNotifications(){

        $notifications = auth()->user()->readNotifications;

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function unreadNotificaions(){
        $unreadNotifications = auth()->user()->unreadNotifications;

        return response()->json([
            'notifications' => $unreadNotifications,
        ]);
    }

    public function delete(){

        $notifications = auth()->user()->notifications;
        $notifications->delete();
        return response()->json([
            'message' => ["notifications delete successfully."],
        ]);
    }

    public function markAsRead($id){

        $data = null;
        if ($id)
        {
            $notifications = auth()->user()->notifications->where('id',$id)->first();
            $data = $notifications->data['post'];
            $notifications->markAsRead();
        }
        return response()->json([
            'data' => $data,
        ]);
    }

}
