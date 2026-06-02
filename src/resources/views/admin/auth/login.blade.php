@extends('layouts.admin')

@section('title', 'Login')

@section('content')
    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <input type="email" name="email" class="form-control" placeholder="Email">
        <input type="password" name="password" class="form-control mt-2" placeholder="Password">

        <button class="btn btn-primary w-100 mt-3">Login</button>
    </form>
@endsection
