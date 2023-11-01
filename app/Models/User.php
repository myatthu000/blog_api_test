<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'uuid',
        'subscriber',
        'freeze_account',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function categories(){
        return $this->hasMany(Category::class);
    }

    // In User model
    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function post(){
        return $this->hasOne(Post::class);
    }

    public function isAdmin(){
        return $this->role === 'admin';
    }

    public function isEditor(){
        return $this->role === 'editor';
    }

    public function isAuthor(){
        return $this->role === 'author';
    }

    public function isUser(){
        return $this->role === 'user';
    }

    public function isBand(){
        return $this->freeze_action !== -1; //can access actions
    }

    public function isAuthenticated(){
        return Auth::check();
    }

}
