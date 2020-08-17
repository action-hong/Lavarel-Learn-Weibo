<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    public function __construct() {
      $this->middleware('auth', [
        'except' => ['show', 'store', 'create', 'index', 'confirmEmail']
      ]);

      // 
      $this->middleware('guest', [
        // 我也不懂为毛之前写成 'only' > ['create'] 导致所有方法都只能是游客下访问
        'only' => ['create']
      ]);
    }

    public function index()
    {
      $users = User::paginate(10);
      return view('users.index', compact('users'));
    }

    //
    public function create()
    {
      return view('users.create');
    }

    public function show(User $user)
    {
      return view('users.show', compact('user'));
    }

    public function store(Request $request)
    {
      $this->validate($request, [
        // 'test' => 'required',
        'name' => 'required|unique:users|max:50',
        'email' => 'required|email|unique:users|max:50',
        'password' => 'required|confirmed|min:6'
      ]);

      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password)
      ]);

      $this->sendEmailConfirmationTo($user);
      session()->flash('success', '注册成功, 请去邮箱内激活');
      return redirect('/');
    }

    public function edit(User $user)
    {
      $this->authorize('update', $user);
      return view('users.edit', compact('user'));
    }
    
    public function update(User $user, Request $request)
    {
      $this->authorize('update', $user);
      $this->validate($request, [
        'name' => 'required|unique:users|max:50',
        'password' => 'nullable|confirmed|min:6'
      ]);

      $data = [];
      $data['name'] = $request->name;

      if ($request->password) {
        $data['password'] = bcrypt($$request->password);
      }

      $user->update($data);

      session()->flash('success', '个人资料更成功');

      return redirect()->route('users.show', $user->id);
    }

    public function destroy(User $user)
    {
      $this->authorize('destroy', $user);
      $user->delete();
      session()->flash('success', '成功删除用户!');
      return back();
    }

    public function confirmEmail($token)
    {
      # code...
      $user = User::where('activation_token', $token)->firstOrFail();

      $user->activated = true;
      $user->activation_token = null;
      $user->save();

      Auth::login($user);
      session()->flash('success', '恭喜您, 激活成功!');
      return redirect()->route('users.show', [$user]);
    }

    protected function sendEmailConfirmationTo($user)
    {
      $view = 'emails.confirm';
      $data = compact('user');
      $name = 'kkopite';
      $to = $user->email;
      $subject = '感谢注册 Weibo 应该, 请确认您的邮箱';

      Mail::send($view, $data, function ($message) use ($to, $subject) {
        $message->to($to)->subject($subject);
      });
    }
}
