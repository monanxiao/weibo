<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class FollowersController extends Controller
{
    // 检测授权
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 关注用户
    public function store(User $user)
    {
        $this->authorize('follow', $user);// 验证关注是否自己

        // 判断是否已关注，未关注的情况下，执行关注
        if( ! Auth::user()->isFollowing($user->id)) {
            Auth::user()->follow($user->id);
        }

        return redirect()->route('users.show', $user->id);


    }

    // 取消关注
    public function destroy(User $user)
    {
        $this->authorize('follow', $user);// 验证关注是否自己

        // 验证是否已关注，关注的情况下执行取消关注
        if(Auth::user()->isFollowing($user->id)) {
            Auth::user()->unfollow($user->id);
        }

        return redirect()->route('users.show', $user->id);

    }
}
