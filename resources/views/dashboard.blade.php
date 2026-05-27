@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body text-center p-5">
            <h2>Chào mừng, {{ Auth::user()->name }}!</h2>
            <p>Bạn đang đăng nhập với quyền: <strong>{{ strtoupper(Auth::user()->role) }}</strong></p>
            
            <form action="{{ route('logout') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-danger">Đăng Xuất</button>
            </form>
        </div>
    </div>
</div>
@endsection