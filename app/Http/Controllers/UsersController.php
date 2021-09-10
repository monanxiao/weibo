<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    //使用构造方法过滤 用户编辑、更新 操作
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index','confirmEmail'] //未登录时可以访问的方法
        ]);

        $this->middleware('guest', [
            'only' => ['create'] //只让未登录时访问注册界面
        ]);

        // 注册限流 1小时内只能注册10次
        $this->middleware('throttle:10,60', [
            'only' => ['store']
        ]);
    }

    // 用户列表
    public function index()
    {
        // $users = User::all();
        $users = User::paginate(6);// 分页

        return view('users.index', compact('users'));
    }

    // 注册
    public function create()
    {

        return view('users.create');
    }

    // 获取用户信息
    public function show(User $user)
    {

        // 取出当前用户的所有微博
        $statuses = $user->statuses()
                        ->orderBy('created_at','desc')
                        ->paginate(10);

        return view('users.show',compact('user','statuses'));
    }

    // 接收用户表单
    public function store(Request $request)
    {

        // 表单字段验证规则
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        // 保存用户数据
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // 注册成功后自动登录
        // Auth::login($user);
        // 闪存消息 提醒注册成功
        // session()->flash('success', '欢迎，您在这里将会开启一段新的启程~');
        // 注册完成后跳转到用户首页
        // return redirect()->route('users.show', [$user]);

        // 发送账号激活链接邮件
        $this->sendEmailConfirmationTo($user);

        // 闪存消息 提醒注册成功
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');

        return redirect('/');

    }

    // 编辑用户资料
    public function edit(User $user)
    {
        // 验证授权 调用用户更新授权方法
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    // 接收用户资料更新数据表单
    public function update(User $user, Request $request)
    {
        // 验证授权 调用用户更新授权方法
        $this->authorize('update', $user);

        // 验证提交数据
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6' //允许密码为空
        ]);

        // $user->update([
        //     'name' => $request->name,
        //     'password' => bcrypt($request->password)
        // ]);

        $data = [];// 声明空数组
        $data['name'] = $request->name;// 赋值新名称

        // 验证是否存在密码
        if($request->password){
            $data['password'] =  bcrypt($request->password); // 赋值新密码
        }

        // 执行数据更新
        $user->update($data);

        // 提醒消息
        session()->flash('success', '个人资料更新成功！');

        // return redirect()->route('users.show', [$user]);
        // return redirect()->route('users.show', $user->id); // 同上
        return redirect()->route('users.show', $user); // 同上
    }

    // 删除用户
    public function destroy(User $user)
    {
        // 验证是否拥有删除权限
        $this->authorize('destroy',$user);

        $user->delete();// 删除当前实例
        session()->flash('success', '成功删除用户！');
        return back();// 返回上一页
    }

    // 用户邮箱激活方法
    public function sendEmailConfirmationTo($user){

        $view = 'emails.confirm'; // 邮件模板
        $data = compact('user'); // 可用实例数据
        // $from = 'monanxiao@qq.com'; // 发送人邮箱
        // $name = 'MoNanXiao'; // 发送人
        $to = $user->email; // 接收人邮箱
        $subject = '感谢注册 Weibo 应用！请确认你的邮箱。'; // 邮箱主题

        // 调用Mail 发送邮件; 视图、实例、闭包；闭包包含：发送人邮箱、发送人姓名、接收人、主题
        // Mail::send($view,$data,function ($message) use ($from, $name, $to, $subject){
        Mail::send($view,$data,function ($message) use ( $to, $subject){

            // $message->from($from, $name)->to($to)->subject($subject);
            $message->to($to)->subject($subject);
        });
    }

    // 激活账户
    public function confirmEmail($token){

        // 通过token查出当前用户，假如不存在则返回404错误信息
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;// 状态改为激活
        $user->activation_token = null; // 清空激活链接 避免重复使用
        $user->save();// 保存数据

        Auth::login($user);// 登录当前账户
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);

    }
}
