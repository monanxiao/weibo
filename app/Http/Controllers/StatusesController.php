<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Auth;

class StatusesController extends Controller
{
    // 过滤创建删除操作
    public function __construct()
    {
        $this->middleware('auth');// 用户登录后才可以操作
    }

    // 接受发布微博表单
    public function store(Request $request)
    {
        // 验证表单内容
        $this->validate($request, [
            'content' => 'required|max:140'
        ]);

        // 表单数据入库 当前登录用户 调用一对多关联方法，创建新数据
        Auth::user()->statuses()->create([
            'content' => $request->content
        ]);

        session()->flash('success', '发布成功！');
        return redirect()->back();

    }
}
