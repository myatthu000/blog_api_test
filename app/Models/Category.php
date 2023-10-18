<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['title','slug','user_id'];

    public function user(){
        return $this->belongsTo(User::class,"user_id");
    }

//    public function setSlugAttribute()
//    {
//        return $this->attributes['slug'] = Str::slug($this->title);
//    }
}
