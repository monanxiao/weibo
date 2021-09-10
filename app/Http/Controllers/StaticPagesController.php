<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Auth;


class StaticPagesController extends Controller
{
    //首页
    public function home()
    {
        // return '首页';
        // return view('static_pages/home');

        $feed_items = [];// 声明空数组

        // 验证用户是否登录
        if(Auth::check()) {

            // 用户登录后赋值当前用户发布的微博列表 并分页显示
            $feed_items = Auth::user()->feed()->paginate(10);
            // $feed_items = Auth::user()->feed()->paginate(30);
        }

        return view('static_pages/home', compact('feed_items'));

    }

    //帮助页
    public function help()
    {

        // return '帮助页';

        return view('static_pages/help');
    }

    //关于我们
    public function about()
    {

        // return '关于我们';

        return view('static_pages/about');
    }
}
