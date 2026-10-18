@extends('layouts.auth')

@section('title', 'Daftar Anggota')

@section('content')
<h1 class="h4 fw-bold text-center mb-1">Daftar akun anggota</h1>
<p class="text-secondary text-center mb-4">Akun yang dibuat melalui halaman ini memiliki peran anggota.</p>
<form action="{{ route('register.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label fw-semibold">Nama</label>
        <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" autocomplete="name" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold">Email login</label>
        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" autocomplete="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label fw-semibold">Kata sandi</label>
        <input id="password" type="password" name="password" class="form-control" autocomplete="new-password" minlength="10" required>
        <div class="form-text">Minimal 10 karakter.</div>
    </div>
    <div class="mb-4">
        <label for="password_confirmation" class="form-label fw-semibold">Ulangi kata sandi</label>
        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Buat akun anggota</button>
</form>
<p class="text-secondary text-center mt-4 mb-0">
    Sudah punya akun? <a href="{{ route('login') }}">Login</a>
</p>
@endsection
