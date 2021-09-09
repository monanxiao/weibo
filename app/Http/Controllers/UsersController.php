<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    //使用构造方法过滤 用户编辑、更新 操作
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index'] //未登录时可以访问的方法
        ]);

        $this->middleware('guest', [
            'only' => ['create'] //只让未登录时访问注册界面
        ]);
    }

    //用户列表
    public function index()
    {
        // $users = User::all();
        $users = User::paginate(6);//分页

        return view('users.index', compact('users'));
    }

    //注册
    public function create()
    {

        return view('users.create');
    }

    //获取用户信息
    public function show(User $user)
    {

        return view('users.show',compact('user'));
    }

    //接收用户表单
    public function store(Request $request)
    {

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

        //注册成功后自动登录
        Auth::login($user);

        //闪存消息 提醒注册成功
        session()->flash('success', '欢迎，您在这里将会开启一段新的启程~');

        //注册完成后跳转到用户首页
        return redirect()->route('users.show', [$user]);

    }

    //编辑用户资料
    public function edit(User $user)
    {
        //验证授权 调用用户更新授权方法
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    //接收用户资料更新数据表单
    public function update(User $user, Request $request)
    {
        //验证授权 调用用户更新授权方法
        $this->authorize('update', $user);

        //验证提交数据
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6' //允许密码为空
        ]);

        // $user->update([
        //     'name' => $request->name,
        //     'password' => bcrypt($request->password)
        // ]);

        $data = [];//声明空数组
        $data['name'] = $request->name;//赋值新名称

        //验证是否存在密码
        if($request->password){
            $data['password'] =  bcrypt($request->password); //赋值新密码
        }

        //执行数据更新
        $user->update($data);

        //提醒消息
        session()->flash('success', '个人资料更新成功！');

        // return redirect()->route('users.show', [$user]);
        // return redirect()->route('users.show', $user->id); //同上
        return redirect()->route('users.show', $user); //同上
    }
}
