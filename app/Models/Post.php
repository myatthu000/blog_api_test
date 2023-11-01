<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Post extends Model
{
    use HasFactory, softDeletes;

    protected $table = 'posts';
    protected $fillable = ['title','description','uuid','user_id','category_id','slug','excerpt','feature_image'];

//    protected $with = ['users','category'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function users(){
        return $this->belongsToMany(User::class);
    }


    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function scopeSearch($query){
        return $query->when(\request("key"),function ($query,$key){
            $query->orwhere('title','like',"%{$key}%")
                ->orWhere('description','like',"%{$key}%");
        });
    }
}
