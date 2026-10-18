<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ment-ory - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        body { min-height: 100vh; background: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; }
        .auth-card { width: min(100%, 460px); border: 0; border-radius: 20px; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">
    <main class="auth-card card shadow-sm p-4 p-md-5">
        <a href="{{ route('login') }}" class="text-decoration-none text-center mb-4">
            <span class="fs-2 fw-bold text-primary">Ment-ory</span>
        </a>
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
