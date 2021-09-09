<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use DB;
use Hash;
use Carbon\Carbon;
use Mail;

class PasswordController extends Controller
{
    // 忘记密码页面
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // 接收重置密码邮箱地址
    public function sendResetLinkEmail(Request $request)
    {
        // 验证邮箱是否合法
        $request->validate(['email' => 'required|email']);

        $email = $request->email;
        // 获取用户 查看是否存在此邮箱
        $user = User::where('email',$email)->first();

        // 如果不存在
        if(is_null($user)) {

            session()->flash('danger', '邮箱未注册');
            // 跳转上一页，并携带上次填入数据
            return redirect()->back()->withInput();
        }

        // 生成token 在视图拼接链接
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));

        // 数据入库，使用updateOrInsert 查看是邮箱否存在，不存在则新增 保持Email唯一
        DB::table('password_resets')->updateOrInsert(['email' => $email], [
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => new Carbon,
        ]);

        // 将生成的 Token 链接发送给用户
        Mail::send('emails.reset_link', compact('token'), function ($message) use ($email){
            $message->to($email)->subject('忘记密码');
        });

        session()->flash('success', '重置邮件发送成功，请查收');
        return redirect()->back();
    }
}
