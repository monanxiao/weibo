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
        $this->validate($request,[
            'name' => 'required|unique:users|max:50',
            'email' => 'required|emai|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        return ;
    }
}
