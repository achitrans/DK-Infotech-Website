<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> @yield('title', env('APP_NAME'))</title>

    <meta name="theme-color" content="#2d2072">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Office Mgmt">
    <link rel="apple-touch-icon" href="/images/pwa-192.png">
    <link rel="manifest" href="/manifest.json">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    @yield('head')

</head>

<body class="hold-transition sidebar-mini" onload="display_ct()">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/" class="nav-link">Home</a>
                </li>
            </ul>
            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                @isset($branchContextBranches)
                    @if($branchContextBranches->isNotEmpty())
                        <li class="nav-item d-flex align-items-center">
                            <form action="{{ route('branches.set-active') }}" method="POST" class="d-flex align-items-center">
                                @csrf
                                <select name="branch_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                    @foreach($branchContextBranches as $branch)
                                        <option value="{{ $branch->id }}" {{ $branch->id === $branchContextActiveBranchId ? 'selected' : '' }}>
                                            {{ $branch->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </li>
                    @endif
                @endisset
                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-user"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">User Menu</span>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('change-password-form') }}" class="dropdown-item">Change Password</a>
                        <div class="dropdown-divider"></div>
                        @auth
                        @if (auth()->user()->isEmployee())
                            <a href="{{ route('user-kyc.show', Crypt::encrypt(auth()->user()->id)) }}"
                            class="dropdown-item">Kyc</a>
                        @elseif(auth()->user()->isClient())
                            <a href="{{ route('client-kyc.show', Crypt::encrypt(auth()->user()->id)) }}"
                            class="dropdown-item">Kyc</a>
                        @endif
                        @endauth
                        <div class="dropdown-divider"></div>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
                            @csrf
                        </form>
                        <a href="#" class="dropdown-item"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/" class="brand-link">
                <span class="brand-text font-weight-light">{{ config('app.name', 'Laravel') }}</span>
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        @auth
                        @if (auth()->user()->isAssociate() || auth()->user()->isAdmin() || auth()->user()->isBranchManager())
                            <li class="nav-item">
                                <a href="{{ route('clients.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-user-tie"></i>
                                    <p>Clients</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->isAdmin() || auth()->user()->isBranchManager())
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Users</p>
                                </a>
                            </li>
                        @endif
                            <li class="nav-item">
                                <a href="{{ route('attendances.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>Attendance Report</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('attendances.report') }}" class="nav-link">
                                    <i class="nav-icon fas fa-chart-bar"></i>
                                    <p>Attendance Summery</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('leaves.admin.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-calendar-alt"></i>
                                    <p>Leaves</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('settings.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-server"></i>
                                    <p>Setting</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('whatsapp.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-envelope"></i>
                                    <p>Whatsapp</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('inquiries.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-question-circle"></i>
                                    <p>Inquiries</p>
                                </a>
                            </li>
                        @endif
                        @endauth

                        <li class="nav-item">
                            <a href="{{ route('projects.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Projects</p>
                            </a>
                        </li>

                        @auth
                        @if (auth()->user()->isEmployee())
                            <li class="nav-item">
                                <a href="{{ route('inquiries.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-question-circle"></i>
                                    <p>Inquiries</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('leaves.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-calendar-alt"></i>
                                    <p>Leaves</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('attendances.employee.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>Attendance Report</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('docs.employee') }}" class="nav-link">
                                    <i class="nav-icon fas fa-book"></i>
                                    <p>Documents</p>
                                </a>
                            </li>
                        @endif
                        @endauth

                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper p-2">
            @yield('body')

        </div>

        <footer class="main-footer">
            Copyright © {{ date('Y') }} {{ env('COMPANY_NAME') }}. | <i class="far fa-clock"></i>
            <span id="ct">{{ now()->format('d/M/Y, H:i:s') }}</span>

            @auth
            <div class="float-right d-none d-sm-inline-block">
                <b><i class="fas fa-user"></i></b> {{ auth()->user()->name }} (Dept:
                {{ auth()->user()->department ?? 'N/A' }})
            </div>
            @endauth

        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/js/all.min.js"></script>

    <script>
        function display_c() {
            var refresh = 1000;
            mytime = setTimeout('display_ct()', refresh)
        }

        function display_ct() {
            document.getElementById('ct').innerHTML = new Date().toLocaleString();
            display_c();
        }
    </script>
        @yield('scripts')

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('Service Worker registered successfully.', reg))
                    .catch((err) => console.error('Service Worker registration failed.', err));
            });
        }
    </script>

    <!-- Prevent Double Submission -->
    <script>
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form && (!form.checkValidity || form.checkValidity())) {
                const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                setTimeout(() => {
                    buttons.forEach(button => {
                        button.disabled = true;
                        if (button.tagName === 'INPUT') {
                            button.value = 'Processing...';
                        } else {
                            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
                        }
                    });
                }, 10);
            }
        });
    </script>
</body>

</html>
