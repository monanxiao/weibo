<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    //注册
    public function create(){

        return view('users.create');
    }

    //获取用户信息
    public function show(User $user){

        return view('users.show',compact('user'));
    }

    //接收用户表单
    public function store(Request $request){


        //表单字段验证规则
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        //保存用户数据
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //闪存消息 提醒注册成功
        session()->flash('success', '欢迎，您在这里将会开启一段新的启程~');

        //注册完成后跳转到用户首页
        return redirect()->route('users.show', [$user]);

    }
}
