<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function gravatar($size = '100')
    {
      $hash = md5(strtolower(trim($this->attributes['email'])));
      return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public static function boot()
    {
      parent::boot();

      // user创建后自动添加activation_token
      static::creating(function ($user) {
        $user->activation_token = Str::random(10);
      });
    }

    public function statuses()
    {
      // 默认外键就是 模型名加上_id, 本地键就是id了, 所以不用传
      // return $this->hasMany(Status::class, 'user_id', 'id');
      return $this->hasMany(Status::class);
    }

    public function feed()
    {
      return $this->statuses()->orderBy('created_at', 'desc');
    }

    // 获取粉丝
    public function followers()
    {
      // 不然默认表回事 user_user
      return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    // 获取关注的人
    public function followings()
    {
      return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    // 关注
    public function follow($user_ids)
    {
      if (!is_array($user_ids)) { 
        // 那不是变成['user_ids' => xx] 了?
        $user_ids = compact('user_ids');
      }
      $this->followings()->sync($user_ids, false);
    }

    public function unfollow($user_ids)
    {
      if (!is_array($user_ids)) {
        $user_ids = compact('user_ids');
      }
      $this->followings()->detach($user_ids);
    }

    public function isFollowing($user_id) 
    {
      // 定义了followings方法  直接访问返回Eloquent集合, 这是laravel Eloquent提供的动态属性功能
      return $this->followings->contains($user_id);
    }


}
