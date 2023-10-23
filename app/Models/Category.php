<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['title','slug','user_id','uuid'];

    public function showErrorScope($method_type, $status, $exception = null){

        return response()->json([
            'error' => "Failed to ${method_type} the category",
            'data' => $exception,
        ],$status);
    }
//    public function getRouteKeyName()
//    {
//        return 'uuid';
//    }


    public function user(){
        return $this->belongsTo(User::class,"user_id");
    }

}
