<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPagesController extends Controller
{
    //首页
    public function home()
    {

        // return '首页';

        return view('static_pages/home');
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
