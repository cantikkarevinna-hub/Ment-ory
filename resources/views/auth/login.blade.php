@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<h1 class="h4 fw-bold text-center mb-1">Masuk ke akun</h1>
<p class="text-secondary text-center mb-4">Masuk untuk melanjutkan ke Ment-ory.</p>
@if(session('success'))
    <div class="alert alert-success" role="status">{{ session('success') }}</div>
@endif
<form action="{{ route('login.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold">Email</label>
        <input id="email" type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" autocomplete="username" required autofocus>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label fw-semibold">Kata sandi</label>
        <input id="password" type="password" name="password" class="form-control form-control-lg" autocomplete="current-password" required>
    </div>
    <div class="form-check mb-4">
        <input id="remember" type="checkbox" name="remember" value="1" class="form-check-input">
        <label for="remember" class="form-check-label">Ingat saya</label>
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
</form>
<p class="text-secondary text-center mt-4 mb-0">
    Anggota baru? <a href="{{ route('register') }}">Buat akun anggota</a>
</p>
@endsection
