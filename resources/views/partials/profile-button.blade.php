<a href="{{ route('inventory.profile') }}" class="btn rounded-pill px-3 py-2 text-white d-flex align-items-center gap-2 border-0 shadow-sm" style="background-color: rgba(255, 255, 255, 0.25);">
    <span class="rounded-circle bg-white d-flex align-items-center justify-content-center text-primary fw-bold overflow-hidden" style="width: 30px; height: 30px; font-size: 14px;">
        @if(auth()->user()->photo_path)
            <img src="{{ asset('storage/'.auth()->user()->photo_path) }}" alt="Foto profil {{ auth()->user()->name }}" class="w-100 h-100" style="object-fit: cover;">
        @else
            <i class="bi bi-person-fill"></i>
        @endif
    </span>
    <span class="fw-semibold small">{{ auth()->user()->name }}</span>
</a>
