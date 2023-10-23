<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $fillable = ['title','description','uuid','user_id','category_id','slug','excerpt','feature_image'];

//    protected $with = ['users','category'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    public function categories(){
        return $this->hasMany(Category::class);
    }

    public function scopeSearch($query){
        return $query->when(\request("key"),function ($query,$key){
            $query->orwhere('title','like',"%{$key}%")
                ->orWhere('description','like',"%{$key}%");
        });
    }
}
