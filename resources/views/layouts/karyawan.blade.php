<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Karyawan Portal - Core Values System</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Bootstrap CSS via Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #ffffff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            z-index: 100;
        }
        .nav-pills .nav-link {
            color: #495057;
            border-radius: 8px;
            margin-bottom: 5px;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .nav-pills .nav-link i {
            width: 24px;
        }
        .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
            color: #fff;
            background-color: #ec4899;
            box-shadow: 0 4px 10px rgba(236, 72, 153, 0.3);
        }
        .nav-pills .nav-link:hover:not(.active) {
            background-color: #fdf2f8;
            color: #ec4899;
        }
        .main-content {
            padding: 30px;
        }
        .topbar {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 15px 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.03);
            margin-bottom: 30px;
        }
        .premium-card {
            background: #fff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .premium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .btn-primary {
            background-color: #ec4899;
            border-color: #ec4899;
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(236, 72, 153, 0.2);
        }
        .btn-primary:hover {
            background-color: #db2777;
            border-color: #db2777;
            box-shadow: 0 6px 15px rgba(236, 72, 153, 0.3);
        }
        h2.page-title {
            font-weight: 700;
            color: #2b2d42;
            margin-bottom: 0;
        }
        .text-primary {
            color: #ec4899 !important;
        }
        .bg-primary {
            background-color: #ec4899 !important;
        }
        .hover-bg:hover {
            background-color: #fdf2f8;
        }
        .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar d-flex flex-column p-4">
                <div class="text-center mb-5">
                    <h4 class="fw-bold text-primary">Core<span class="text-dark">Values</span></h4>
                </div>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="{{ route('karyawan.dashboard') }}" class="nav-link {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tasks"></i> Tugas Penilaian
                        </a>
                    </li>
                    @if(auth()->user()->role === 'direktur')
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <i class="fas fa-arrow-left"></i> Dashboard Hasil
                        </a>
                    </li>
                    @endif
                </ul>
                <hr>
                <div class="dropup position-relative">
                    <a href="javascript:void(0)" class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle p-2 rounded hover-bg" id="dropdownUserToggle" onclick="toggleUserDropdown(event)">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=ec4899&color=fff" alt="" width="32" height="32" class="rounded-circle me-2">
                        <strong class="d-none d-sm-inline">{{ auth()->user()->name }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow position-absolute" id="userDropdownMenu" style="bottom: 100%; left: 0; margin-bottom: 10px;">
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            <a class="dropdown-item" href="javascript:void(0)"
                               onclick="confirmLogout()">
                                <i class="fas fa-sign-out-alt me-2"></i> Sign out
                            </a>
                        </li>
                    </ul>
                </div>
                
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content bg-light">
                <!-- Topbar -->
                <div class="topbar d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title h4">@yield('title')</h2>
                    </div>
                    <div>
                    </div>
                </div>

                <!-- Page Content -->
                @yield('content')
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleUserDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('userDropdownMenu');
            menu.classList.toggle('show');
        }

        // Close dropdown when clicking elsewhere
        window.addEventListener('click', function(e) {
            const menu = document.getElementById('userDropdownMenu');
            const toggle = document.getElementById('dropdownUserToggle');
            if (menu && !toggle.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('show');
            }
        });

        // Manual dropdown initialization for other Bootstrap dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'))
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                if (window.bootstrap) {
                    return new bootstrap.Dropdown(dropdownToggleEl);
                }
            });
        });
        function confirmLogout() {
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: "Apakah Anda yakin ingin keluar dari sistem?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ec4899',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                background: '#fff',
                borderRadius: '15px'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            })
        }
    </script>
    @stack('scripts')
</body>
</html>
