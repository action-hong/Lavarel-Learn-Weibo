<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{

    public function __construct() {
      $this->middleware('guest', [
        'only' => ['create']
      ]);
    }

    //
    public function create()
    {
      return view('sessions.create');
    }

    public function store(Request $request)
    { 
      $credentials = $this->validate($request, [
        'email' => 'required|email|max:255',
        'password' => 'required'
      ]);

      if (Auth::attempt($credentials, $request->has('remeber'))) {
        // 检查是否激活
        if (Auth::user()->activated) {
          session()->flash('success', '欢迎回来!');
          $fallback = route('users.show', [Auth::user()]);
          // intended 导向上一次请求尝试访问的页面, 接受一个默认页面
          return redirect()->intended($fallback);
        } else {
          Auth::logout();
          session()->flash('warning', '您的账号未激活, 请检查邮箱中的注册邮件进行激活.');
          return redirect('/');
        }
      } else {
        session()->flash('danger', '很抱歉, 您的邮箱和密码不匹配!');
        return redirect()->back()->withInput();
      }
    }

    public function destory()
    {
      Auth::logout();
      session()->flash('success', '您已成功退出!');
      return redirect('login');
    }
}
