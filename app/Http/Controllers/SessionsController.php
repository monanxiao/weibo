<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    //构造方法 过滤已登录 禁止访问登录界面和注册界面
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create'] //已登录时禁止访问 只允许未登录用户访问方法
        ]);

    }

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
        if(Auth::attempt($credentials,$request->has('remember'))) { // 第一个参数匹配账号密码，第二个参数保持登录状态

            // 验证用户是否激活
            if(Auth::user()->activated) {

                // 登录成功
                session()->flash('success', '欢迎回来~');
                // 默认跳转地址，重定向到个人首页，并提示欢迎回来
                $fallback = route('users.show', Auth::user());
                // 重定向到访问地址，假如为空，则跳转到默认地址
                return redirect()->intended($fallback);

            }else{

                Auth::logout();// 未激活则提醒 并重定向
                session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }


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
