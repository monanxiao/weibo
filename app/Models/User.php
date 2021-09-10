<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 创建前生成激活Key
    public static function boot(){

        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = Str::random(10);
        });


    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //生成用户头像
    public function gravatar($size = 100)
    {

        $hash = md5(strtolower(trim($this->attributes['email'])));

        return "http://www.gravatar.com/avatar/$hash?s=$size";

    }

    // 一个用户拥有多条微博
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    // 倒序显示微博数据
    public function feed()
    {

        // 取出当前关注用户的ID并写入数组
        $user_ids = $this->followings->pluck('id')->toArray();
        // 当前id 插入尾部
        array_push($user_ids, $this->id);

        // 取出微博内 包含在数组内的内容
        return Status::whereIn('user_id', $user_ids)
                        ->with('user')
                        ->orderBy('created_at','desc');
    }

    // 一个用户拥有多个粉丝
    public function followers()
    {
        // return $this->belongsToMany(User::class); // 默认表名 user_user
        // return $this->belongsToMany(User::class, 'followers');// 自定义表名 followers
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');// 自定义表名，自定义字段名称
    }

    // 一个粉丝关注的多个用户
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    // 关注用户
    public function follow($user_ids)
    {
        // 检测是否数组，不是就通过
        if( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }

        // 调用关注方法，并使用sync去重关注
        $this->followings()->sync($user_ids, false);

    }

    // 取消关注
    public function unfollow($user_ids)
    {
        // 检测是否数组，不是就通过
        if( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }

        // 取消关注用户
        $this->followings()->detach($user_ids);
    }

    // 验证是否已关注当前用户
    public function isFollowing($user_id){

        return $this->followings->contains($user_id);// 验证是否已经包含
    }
}
