@extends('layouts.default')
@section('title', '注册')

@section('content')
  <div class="offset-md-2 col-md-8">
    <div class="card">
      <div class="card-header">
        <h5>注册</h5>
      </div>
    </div>
    <div class="card-body">

      @include('shared._errors')

      <form method="POST" action="{{ route('users.store') }}">
        @csrf
        {{-- <div class="form-group">
          <label for="test">测试:</label>
          <input type="text" name="test" class="form-control" value="{{ old('namewe') }}">
        </div> --}}
        <div class="form-group">
          <label for="name">名称:</label>
          <input type="text" name="name" class="form-control" value="{{ old('name') }}">
        </div>
        <div class="form-group">
          <label for="email">邮箱:</label>
        <input type="text" name="email" class="form-control" value="{{ old('email') }}">
        </div>
        <div class="form-group">
          <label for="password">密码:</label>   
        <input type="password" name="password" class="form-control" value="{{ old('password') }}">
      </div>
      <div class="form-group">
        <label for="password_confirmation">确认:</label>   
        <input type="password" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}">
      </div>
      <button type="submit" class="btn btn-primary">注册</button>
      </form>
    </div>
  </div>
@endsection