<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ment-ory - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@800&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap');

        body { 
            background-color: #f0f2f5; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
        
        /* Area Kolom Sidebar Menyatu dengan Warna Abu-abu Body */
        .sidebar-wrapper {
            background-color: #f0f2f5;
            min-height: 100vh;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        /* Brand Ment-ory di Area Abu-abu */
        .sidebar-brand-title { 
            font-family: 'Playfair Display', serif;
            color: #1e3a8a; 
            font-weight: 800; 
            font-size: 32px; 
            padding: 35px 20px 25px 25px; 
            margin: 0;
            letter-spacing: -0.5px;
            background-color: #f0f2f5;
        }

        /* Kontainer Biru Sidebar dengan Lengkungan Atas */
        .sidebar-blue-box { 
            background-color: #557cb8; 
            flex-grow: 1;
            border-top-right-radius: 65px;
            padding-top: 40px;
            padding-bottom: 30px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Menu Navigasi */
        .sidebar-menu a { 
            color: rgba(255, 255, 255, 0.85); 
            text-decoration: none; 
            padding: 16px 35px; 
            display: block; 
            font-weight: 600; 
            font-size: 16px; 
            transition: all 0.2s ease-in-out;
        }

        .sidebar-menu a:hover {
            color: #ffffff;
            padding-left: 40px;
        }

        .sidebar-menu a.active { 
            color: #ffffff; 
            font-weight: 700;
            background-color: rgba(255, 255, 255, 0.12);
        }

        /* Tombol Trash Bin Bulat Melayang di Bawah */
        .trash-bin-btn { 
            width: 48px; 
            height: 48px; 
            background-color: rgba(255, 255, 255, 0.25); 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            font-size: 20px; 
            text-decoration: none; 
            margin-left: 35px;
            margin-bottom: 10px;
            transition: all 0.2s;
        }

        .trash-bin-btn:hover { 
            background-color: #ffffff; 
            color: #557cb8; 
            transform: scale(1.05);
        }

        /* Banner Header Biru Utama */
        .main-banner { 
            background-color: #557cb8; 
            border-radius: 20px; 
            padding: 20px 30px; 
            color: white; 
            margin-bottom: 25px; 
        }

        .user-profile-btn { 
            background-color: rgba(255, 255, 255, 0.25); 
            color: white; 
            padding: 6px 18px; 
            border-radius: 20px; 
            font-weight: 600; 
            font-size: 13px; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            text-decoration: none;
            transition: all 0.2s;
        }

        .user-profile-btn:hover { 
            background-color: white; 
            color: #2b4c7e; 
        }

        /* Card Container Umum */
        .content-card { 
            background-color: white; 
            border-radius: 20px; 
            padding: 30px; 
        }

        .auto-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1080;
            min-width: 280px;
            max-width: 420px;
            border-radius: 16px;
            padding: 14px 18px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
            animation: slideInToast 0.25s ease-out;
        }

        @keyframes slideInToast {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation Wrapper -->
        <div class="col-md-2 sidebar-wrapper">
            <!-- Title Ment-ory Atas dengan Background Abu-abu Menyatu -->
            <h1 class="sidebar-brand-title">Ment-ory</h1>

            <!-- Kontainer Biru Melengkung -->
            <div class="sidebar-blue-box">
                <div class="sidebar-menu">
                    <a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.index') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('inventory.data') }}" class="{{ request()->routeIs('inventory.data') ? 'active' : '' }}">Data Inventaris</a>
                    <a href="{{ route('inventory.laporan') }}" class="{{ request()->routeIs('inventory.laporan') ? 'active' : '' }}">Laporan</a>
                    <a href="{{ route('inventory.jadwal') }}" class="{{ request()->routeIs('inventory.jadwal') ? 'active' : '' }}">Jadwal & Acara</a>
                </div>

                <!-- Tombol Trash Bin Bulat Di Bawah -->
                <div>
                    <a href="{{ route('inventory.trash') }}" class="trash-bin-btn" title="Riwayat Dihapus">
                        <i class="bi bi-trash3-fill"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-10 p-4">
            @if (session('success'))
                <div class="auto-toast alert-success fade show" role="alert" style="background-color: #dcfce7; color: #166534; border-left: 5px solid #16a34a;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i>
                        <span class="fw-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="auto-toast alert-danger fade show" role="alert" style="background-color: #fee2e2; color: #991b1b; border-left: 5px solid #dc2626;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span class="fw-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.auto-toast').forEach(function (toast) {
            setTimeout(function () {
                toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-8px)';
                setTimeout(function () {
                    toast.remove();
                }, 300);
            }, 2200);
        });
    });
</script>
</body>
</html>