<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
use App\Models\Status;

class StatusPolicy
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

    // 用户操作 删除微博 授权验证
    public function destroy(User $user,Status $status)
    {
        return $user->id === $status->user_id; // 验证当前用户登录ID和当前微博是否相同
    }
}
