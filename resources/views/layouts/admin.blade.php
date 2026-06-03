<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Core Values System</title>
    
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
            overflow-x: hidden;
        }
        .sidebar {
            width: 280px;
            min-height: 100vh;
            background-color: #ffffff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            z-index: 1050;
            transition: all 0.3s ease;
            flex-shrink: 0;
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
            flex-grow: 1;
            padding: 30px;
            transition: all 0.3s ease;
            min-height: 100vh;
            background-color: #f8f9fa;
            width: 0; /* Penting agar flex child tidak melebar melampaui layar */
            min-width: 0;
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
            background-color: #f1f3f5;
        }
        .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Responsive Sidebar */
        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                left: -280px;
                width: 280px;
                height: 100%;
                box-shadow: 10px 0 30px rgba(0,0,0,0.1);
            }
            .sidebar.show {
                left: 0;
            }
            .main-content {
                padding: 15px;
                margin-left: 0 !important;
            }
            .topbar {
                padding: 12px 15px;
                border-radius: 12px;
                margin-bottom: 20px;
            }
            .page-title {
                font-size: 1rem;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(4px);
                z-index: 1040;
            }
            .sidebar-overlay.show {
                display: block;
            }
            .container-fluid {
                padding-left: 10px;
                padding-right: 10px;
            }
        }
        
        /* Tablet & Mobile Grid Adjustments */
        @media (max-width: 767.98px) {
            .premium-card .card-body {
                padding: 1.25rem !important;
            }
            .btn-sm {
                padding: 0.4rem 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="d-flex min-vh-100">
            <!-- Sidebar -->
            <div id="sidebar" class="sidebar d-flex flex-column">
                <div class="p-4 flex-grow-1">
                    <div class="text-center mb-5">
                        <h4 class="fw-bold text-primary">Core<span class="text-dark">Values</span></h4>
                    </div>
                    <ul class="nav nav-pills flex-column mb-auto">
                    @if(in_array(auth()->user()->role, ['admin', 'hr', 'direktur']))
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    @endif

                    @if(in_array(auth()->user()->role, ['admin', 'hr']))
                    <li class="nav-item">
                        <a href="{{ route('admin.branches.index') }}" class="nav-link {{ request()->routeIs('admin.branches.*') ? 'active' : '' }}">
                            <i class="fas fa-building"></i> Data Cabang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.divisions.index') }}" class="nav-link {{ request()->routeIs('admin.divisions.*') ? 'active' : '' }}">
                            <i class="fas fa-sitemap"></i> Data Divisi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.positions.index') }}" class="nav-link {{ request()->routeIs('admin.positions.*') ? 'active' : '' }}">
                            <i class="fas fa-briefcase"></i> Data Jabatan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Data Karyawan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.indicators.index') }}" class="nav-link {{ request()->routeIs('admin.indicators.*') ? 'active' : '' }}">
                            <i class="fas fa-list-check"></i> Data Indikator
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.interaction-matrices.index') }}" class="nav-link {{ request()->routeIs('admin.interaction-matrices.*') ? 'active' : '' }}">
                            <i class="fas fa-network-wired"></i> Matriks Penilai
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.generate-assessments.index') }}" class="nav-link {{ request()->routeIs('admin.generate-assessments.*') ? 'active' : '' }}">
                            <i class="fas fa-magic"></i> Generate Matriks
                        </a>
                    </li>
                    @endif

                    @if(in_array(auth()->user()->role, ['karyawan', 'direktur']))
                    <li class="nav-item">
                        <a href="{{ route('karyawan.dashboard') }}" class="nav-link {{ request()->routeIs('karyawan.*') ? 'active' : '' }}">
                            <i class="fas fa-pen-to-square"></i> Input Penilaian
                        </a>
                    </li>
                    @endif

                    @if(in_array(auth()->user()->role, ['admin', 'hr', 'direktur']))
                    <li class="nav-item">
                        <a href="{{ route('admin.results.index') }}" class="nav-link {{ request()->routeIs('admin.results.*') && !request()->routeIs('admin.results.clustering') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i> Hasil Penilaian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.results.clustering') }}" class="nav-link {{ request()->routeIs('admin.results.clustering') ? 'active' : '' }}">
                            <i class="fas fa-layer-group"></i> Kelompok Karyawan
                        </a>
                    </li>
                    @endif
                    </ul>
                </div>
                <hr>
                <div class="p-4 pt-0">
                    <div class="dropup position-relative">
                        <a href="javascript:void(0)" class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle p-2 rounded hover-bg" id="dropdownUserToggle" onclick="toggleUserDropdown(event)">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=ec4899&color=fff" alt="" width="32" height="32" class="rounded-circle me-2">
                            <strong class="text-dark">{{ auth()->user()->name }}</strong>
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
            </div>

            <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Topbar -->
                <div class="topbar d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-link text-dark d-lg-none me-3 p-0" onclick="toggleSidebar()">
                            <i class="fas fa-bars fs-4"></i>
                        </button>
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
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

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

        function confirmDelete(formId, message = "Data yang dihapus tidak dapat dikembalikan!") {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            })
        }
    </script>
    @stack('scripts')
</body>
</html>
