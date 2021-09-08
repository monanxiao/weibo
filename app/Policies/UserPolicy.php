<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //用户更新时的权限验证 参数1：当前登录用户 参数2：即将授权的用户实例
    public function update(User $currentUser, User $user)
    {
        //当前登录用户 全等于 即将授权用户实例ID 授权通过，否则失败。
        return $currentUser->id === $user->id;
    }
}
