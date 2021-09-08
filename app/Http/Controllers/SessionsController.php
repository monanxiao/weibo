<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    //会话界面
    public function create()
    {

        return view('sessions.create');
    }

    //接收会话数据
    public function store(Request $request)
    {
        //验证用户输入的表单数据
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        //验证用户信息
        if(Auth::attempt($credentials,$request->has('remember'))) { //第一个参数匹配账号密码，第二个参数保持登录状态
            //登录成功
            session()->flash('success', '欢迎回来~');
            //重定向到个人首页，并提示欢迎回来
            return redirect()->route('users.show', [Auth::user()]);

        }else{
            //登录失败
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            //重定向登陆界面，并提示错误
            return redirect()->back()->withInput();
        }
    }

    //销毁会话
    public function destroy(){

        //退出登录
        Auth::logout();
        //消息提醒
        session()->flash('success', '您已成功退出！');
        //重定向
        return redirect('login');
    }
}
