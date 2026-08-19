<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <title>{{ env('APP_NAME', '') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="{{ env('APP_NAME') }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2d2072">
    <link rel="manifest" href="/manifest.json">

    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.0/css/boxicons.min.css" rel="stylesheet"
        type="text/css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <link href="{{ asset('assets/vendor/flaticonn/flaticon-uicons/css/all/all.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/yaireoo/tagify/dist/tagify.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/metismenu/dist/metisMenu.min.css') }}" rel="stylesheet">
    <link class="main-switcher" href="{{ asset('assets/css/switcher.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/jsvectormap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}">

    <link class="main-plugins" href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet">
    <link class="main-css" href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    @yield('head')

    <style>
        #main-wrapper {
            visibility: hidden;
        }

        body.loaded #main-wrapper {
            visibility: visible;
        }

        .btn-sm {
            padding: 2px 4px !important;
        }
        input {
            color: rgba(0, 0, 0, 1);
        }
        input::placeholder {
          color: rgba(94, 94, 94, 0.2);
        }

        #items_table_body input {
            padding: 5px 8px;
        }

        #items_table_body select {
            padding: 5px 18px 5px 8px;
        }

        #items_table_body .form-select {
            background-position: right 0.3rem center;
        }

        @media (max-width: 768px) {
            #footer-hide-mobile {
                display: none;
            }
        }

        svg {
            color: var(--bs-primary);
        }

        /* Date fields ko ek dusre ke upar aane se prevent kare */
        .date-fields-row {
            position: relative;
            width: 100%;
            margin-left: -10px;
            margin-right: 0;
        }

        .date-fields-row .date-field {
            position: relative;
            z-index: 1;
        }

        .date-fields-row .date-field input[type="date"] {
            position: relative;
            z-index: 10;
            display: block;
            width: 100%;
            pointer-events: auto;
        }

        #start_date,
        #end_date,
        #due_date {
            position: relative;
            z-index: 20;
            pointer-events: auto;
        }

        @media (max-width: 767.98px) {
            .date-fields-row .date-field {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- Start - Preloader -->
    <div class="ic_preloader" id="ic_preloader">
        <div class="spinner">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <!-- End - Preloader -->

    <div id="main-wrapper">
        <div class="nav-header">
            <a href="{{ route('dashboard') }}" class="brand-logo" aria-label="Brand Logo">
                <img src="{{ asset('dark_favicon.png') }}" class="logo-abbr" style="width: 40px;"
                    alt="{{ env('COMPANY_NAME') }}" srcset="">
                <span class="brand-title" style="font-size: 1.3rem;">{{ env('COMPANY_SHORT_NAME') }}</span>
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span>
                    <span class="line"></span>
                </div>
            </div>
        </div>

        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">

                        @isset($branchContextBranches)
                            @if ($branchContextBranches->isNotEmpty())
                                <form action="{{ route('branches.set-active') }}" method="POST"
                                    class="topbar-item d-flex align-items-center me-2" style="min-width:220px;">
                                    @csrf
                                    <select name="branch_id" class="form-select form-select-sm"
                                        onchange="this.form.submit()">
                                        @foreach ($branchContextBranches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ $branch->id === $branchContextActiveBranchId ? 'selected' : '' }}>
                                                {{ $branch->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif
                        @endisset

                        <ul class="navbar-nav header-right">

                            <li class="nav-item notification_dropdown d-none d-sm-flex">
                                <a class="nav-link ic-fullscreen" href="javascript:void(0);" aria-label="Fullscreen">
                                    <i id="icon-full" class="fi fi-rr-expand"></i>
                                    <i id="icon-minimize" class="fi fi-rr-compress"></i>
                                </a>
                            </li>
                            <li class="nav-item dropdown header-profile-dropdown">
                                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <div class="profile-head">
                                        <div class="profile-media">
                                            @php
                                                if (
                                                    auth()->user()->kyc?->photograph_path &&
                                                    Storage::disk('public')->exists(
                                                        auth()->user()->kyc->photograph_path,
                                                    )
                                                ) {
                                                    $photo = Storage::url(auth()->user()->kyc->photograph_path);
                                                }
                                            @endphp

                                            <img src="{{ asset('assets/images/avatar/small/avatar5.webp') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="py-2 d-flex px-3">
                                            <div class="ms-2">
                                                <h4 class="mb-0">Welcome!</h4>

                                                <strong class="text-primary">{{ auth()->user()->name }}</strong>
                                                <small
                                                    class="text-muted d-block">{{ auth()->user()->employee_id }}</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('change-password') }}">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M11.9848 15.3462C8.11714 15.3462 4.81429 15.931 4.81429 18.2729C4.81429 20.6148 8.09619 21.2205 11.9848 21.2205C15.8524 21.2205 19.1543 20.6348 19.1543 18.2938C19.1543 15.9529 15.8733 15.3462 11.9848 15.3462Z"
                                                    stroke="var(--bs-primary)" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M11.9848 12.0059C14.5229 12.0059 16.58 9.94779 16.58 7.40969C16.58 4.8716 14.5229 2.81445 11.9848 2.81445C9.44667 2.81445 7.38857 4.8716 7.38857 7.40969C7.38 9.93922 9.42381 11.9973 11.9524 12.0059H11.9848Z"
                                                    stroke="var(--bs-primary)" stroke-width="1.42857"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <span class="ms-2">Change Password</span>
                                        </a>
                                    </li>
                                    <li>
                                        @auth
                                            @if (auth()->user()->isEmployee())
                                                <a class="dropdown-item"
                                                    href="{{ route('user-kyc.show', Crypt::encrypt(auth()->user()->id)) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-pie-chart">
                                                        <path stroke="var(--bs-primary)"
                                                            d="M21.21 15.89A10 10 0 1 1 8 2.83">
                                                        </path>
                                                        <path stroke="var(--bs-primary)" d="M22 12A10 10 0 0 0 12 2v10z">
                                                        </path>
                                                    </svg>
                                                    <span class="ms-2">KYC</span>

                                                </a>
                                            @elseif(auth()->user()->isClient() || auth()->user()->isAssociate())
                                                {{-- <a class="dropdown-item"
                                                    href="{{ route('client-kyc.show', Crypt::encrypt(auth()->user()->id)) }}">
                                                    <span class="align-middle">Kyc</span>
                                                </a> --}}
                                                <a class="dropdown-item"
                                                    href="{{ route('client-kyc.show', Crypt::encrypt(auth()->user()->id)) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-pie-chart">
                                                        <path stroke="var(--bs-primary)"
                                                            d="M21.21 15.89A10 10 0 1 1 8 2.83">
                                                        </path>
                                                        <path stroke="var(--bs-primary)" d="M22 12A10 10 0 0 0 12 2v10z">
                                                        </path>
                                                    </svg>
                                                    <span class="ms-2">KYC</span>

                                                </a>
                                            @endif
                                            <div class="dropdown-divider my-1"></div>
                                        @endauth
                                    </li>

                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a href="#" onclick="document.getElementById('logout-form').submit();"
                                            class="dropdown-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 24 24" fill="none" stroke="var(--bs-danger)"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="var(--bs-danger)"
                                                    d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                                <polyline stroke="var(--bs-danger)" points="16 17 21 12 16 7">
                                                </polyline>
                                                <line x1="21" y1="12" x2="9" y2="12">
                                                </line>
                                            </svg>
                                            <span class="ms-2 text-danger">Logout </span>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                            </form>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <div class="icnav">
            <div class="icnav-scroll">
                <ul class="metismenu" id="menu">
                    <li>
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <i class="fi fi-rr-home text-primary"></i>
                            <span class="nav-text" data-i18n="Dashboard">Dashboard</span>
                        </a>
                    </li>

                    @auth

                        {{-- ADMIN / BRANCH MANAGER / ACCOUNTS / ASSOCIATE ROLE ITEMS --}}
                        @if (auth()->user()->isAssociate() ||
                                auth()->user()->isAdmin() ||
                                auth()->user()->isBranchManager() ||
                                auth()->user()->hasAnyRole(['admin', 'accounts', 'branch manager']))

                            {{-- OPERATIONS & CRM --}}

                            <li>
                                <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                                    <div class="menu-icon">
                                        <i class="nav-icon fas fa-briefcase"></i>
                                    </div>
                                    <span class="nav-text" data-i18n="Apps">Operations & CRM</span>
                                </a>
                                <ul aria-expanded="false">
                                    @if (auth()->user()->isAssociate() || auth()->user()->isAdmin() || auth()->user()->isBranchManager())
                                        <li><a href="{{ route('clients.index') }}" data-i18n="Salary Slip">
                                                <i class="fas fa-user-tie"></i> Clients</a>
                                        </li>
                                    @endif
                                    <li><a href="{{ route('projects.index') }}" data-i18n="Projects"><i
                                                class="fas fa-tasks"></i> Projects</a></li>

                                    @if (auth()->user()->isAdmin())
                                        <li><a href="{{ route('meetings.index') }}" data-i18n="Google Meetings"><i
                                                    class="fas fa-video"></i> Google Meetings</a></li>
                                    @endif

                                    @if (auth()->user()->hasAnyDept(['admin', 'digital marketing', 'sales', 'intern']))
                                        <li><a href="{{ route('inquiries.index') }}" data-i18n="Inquiries"><i
                                                    class="fas fa-question-circle"></i> Inquiries</a></li>
                                        @if (auth()->user()->hasAnyDept(['admin', 'digital marketing', 'sales']))
                                            <li><a href="{{ route('internship-interests.index') }}"
                                                    data-i18n="Internship Interests"><i class="fas fa-user-graduate"></i>
                                                    Internship Interests</a></li>
                                        @endif
                                    @endif

                                    <li><a href="{{ route('password-vaults.index') }}" data-i18n="Password Vault"><i
                                                class="fas fa-key"></i> Password Vault</a></li>

                                </ul>
                            </li>

                            {{-- ACCOUNTS & FINANCE --}}
                            @if (auth()->user()->hasAnyRole(['admin', 'accounts', 'branch manager']))
                                <li>
                                    <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                                        <div class="menu-icon">
                                            <i class="nav-icon fas fa-rupee-sign"></i>
                                        </div>
                                        <span class="nav-text" data-i18n="Apps">Accounts & Finance</span>
                                    </a>
                                    <ul aria-expanded="false">

                                        <li><a href="{{ route('invoices.index') }}" data-i18n="Invoices"><i
                                                    class="fas fa-file-invoice-dollar"></i> Invoices</a></li>
                                        <li><a href="{{ route('estimates.index') }}" data-i18n="Estimates"><i
                                                    class="fas fa-calculator"></i> Estimates</a></li>
                                        <li><a href="{{ route('quotations.index') }}" data-i18n="Quotations"><i
                                                    class="fas fa-file-signature"></i> Quotations</a></li>
                                        <li><a href="{{ route('expenses.index') }}" data-i18n="Expenses"><i
                                                    class="fas fa-receipt"></i> Expenses</a></li>
                                        <li><a href="{{ route('products.index') }}" data-i18n="Products"><i
                                                    class="fas fa-box-open"></i> Products</a></li>
                                        <li><a href="{{ route('wallet.index') }}" data-i18n="Wallet"><i
                                                    class="fas fa-wallet"></i> Wallet</a></li>
                                        <li><a href="{{ route('wallet.transactions') }}"
                                                data-i18n="Wallet Transactions"><i class="fas fa-exchange-alt"></i> Wallet
                                                Transactions</a></li>

                                        @if (auth()->user()->hasAnyRole(['admin', 'accounts']))
                                            <li><a href="{{ route('user-monthly-salaries.index') }}"
                                                    data-i18n="Employee Salaries"><i class="fas fa-money-check-alt"></i>
                                                    Employee Salaries</a></li>
                                            <li><a href="{{ route('advance-salaries.index') }}"
                                                    data-i18n="Advance Salaries"><i class="fas fa-hand-holding-usd"></i>
                                                    Advance Salaries</a></li>
                                            <li><a href="{{ route('expense-heads.index') }}"
                                                    data-i18n="Advance Salaries"><i class="fas fa-tags"></i> Expense
                                                    Heads</a></li>
                                            <li><a href="{{ route('states.index') }}" data-i18n="Advance Salaries"><i
                                                        class="fas fa-map-marker-alt"></i> States</a></li>
                                        @endif

                                    </ul>
                                </li>
                            @endif

                            {{-- HUMAN RESOURCES --}}
                            @if (auth()->user()->isAdmin())
                                <li>
                                    <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                                        <div class="menu-icon">
                                            <i class="nav-icon fas fa-users-cog"></i>
                                        </div>
                                        <span class="nav-text" data-i18n="Apps">Human Resources</span>
                                    </a>
                                    <ul aria-expanded="false">
                                        <li><a href="{{ route('users.index') }}" data-i18n="Users Management"><i
                                                    class="fas fa-users-cog"></i> Users Management</a></li>
                                        <li><a href="{{ route('branches.index') }}" data-i18n="Branches"><i
                                                    class="fas fa-code-branch"></i> Branches</a></li>
                                        <li><a href="{{ route('offer-letters.index') }}" data-i18n="Offer Letters"><i
                                                    class="fas fa-file-contract"></i> Offer Letters</a></li>
                                        <li><a href="{{ route('experience-letters.index') }}"
                                                data-i18n="Experience Letters"><i class="fas fa-award"></i> Experience
                                                Letters</a></li>
                                        <li><a href="{{ route('attendances.index') }}" data-i18n="Attendance Report"><i
                                                    class="fas fa-clipboard-check"></i> Attendance Report</a></li>
                                        <li><a href="{{ route('attendances.report') }}" data-i18n="Attendance Summary"><i
                                                    class="fas fa-chart-bar"></i> Attendance Summary</a></li>
                                        <li><a href="{{ route('leaves.admin.index') }}" data-i18n="Leaves"><i
                                                    class="fas fa-calendar-minus"></i> Leaves</a></li>
                                        <li><a href="{{ route('holidays.index') }}" data-i18n="Holidays"><i
                                                    class="fas fa-umbrella-beach"></i> Holidays</a></li>
                                        <li><a href="{{ route('career.index') }}" data-i18n="Career / Recruitment"><i
                                                    class="fas fa-briefcase"></i> Career / Recruitment</a></li>

                                        @if (config('modules.loan_inquiry'))
                                            <li><a href="{{ route('loan-inquiries.index') }}" data-i18n="Loans"><i
                                                        class="fas fa-university"></i> Loans</a></li>
                                        @endif
                                    </ul>
                                </li>

                                {{-- SYSTEM SETTINGS --}}

                                <li>
                                    <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                                        <div class="menu-icon">
                                            <i class="nav-icon fas fa-cog"></i>
                                        </div>
                                        <span class="nav-text" data-i18n="Apps">Settings</span>
                                    </a>
                                    <ul aria-expanded="false">
                                        <li><a href="{{ route('settings.index') }}" data-i18n="General Settings"><i
                                                    class="fas fa-sliders-h"></i> General Settings</a></li>
                                        <li><a href="{{ route('whatsapp.index') }}" data-i18n="WhatsApp Config"><i
                                                    class="fab fa-whatsapp"></i> WhatsApp Config</a></li>
                                        <li><a href="{{ route('google.connect') }}" data-i18n="Google Calendar"><i
                                                    class="fab fa-google"></i> Google Calendar</a></li>
                                    </ul>
                                </li>
                            @endif

                        @endif

                        {{-- EMPLOYEE ROLE MULTI-GROUPS --}}
                        @if (auth()->user()->isEmployee())
                            {{-- Workspace & Projects --}}

                            <li>
                                <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                                    <div class="menu-icon">
                                        <i class="nav-icon fas fa-folder"></i>
                                    </div>
                                    <span class="nav-text" data-i18n="Apps">Workspace & Projects</span>
                                </a>
                                <ul aria-expanded="false">
                                    <li><a href="{{ route('projects.index') }}" data-i18n="Projects"><i
                                                class="fas fa-tasks"></i> Projects </a></li>
                                    <li><a href="{{ route('projects_{slug}', ['slug' => 'tasks']) }}"
                                            data-i18n="Tasks"><i class="fas fa-tasks"></i> Tasks </a></li>
                                    <li><a href="{{ route('projects_{slug}', ['slug' => 'milestones']) }}"
                                            data-i18n="Milestones"><i class="fas fa-trophy"></i> Milestones </a></li>
                                </ul>
                            </li>

                            {{-- Time & Attendance --}}

                            <li>
                                <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                                    <div class="menu-icon">
                                        <i class="nav-icon fas fa-clock"></i>
                                    </div>
                                    <span class="nav-text" data-i18n="Apps">Time & Attendance</span>
                                </a>
                                <ul aria-expanded="false">
                                    <li><a href="{{ route('attendances.employee.index') }}"
                                            data-i18n="Attendance Report"><i class="fas fa-clipboard-check"></i>
                                            Attendance Report </a></li>
                                    <li><a href="{{ route('leaves.index') }}" data-i18n="My Leaves"><i
                                                class="fas fa-calendar-minus"></i> My Leaves </a></li>
                                    <li><a href="{{ route('holidays.index') }}" data-i18n="Holidays"><i
                                                class="fas fa-umbrella-beach"></i> Holidays </a></li>
                                </ul>
                            </li>

                            {{-- Payroll & Expenses --}}
                            <li>
                                <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                                    <div class="menu-icon">
                                        <i class="nav-icon fas fa-rupee-sign"></i>
                                    </div>
                                    <span class="nav-text" data-i18n="Apps">Payroll & Expenses</span>
                                </a>
                                <ul aria-expanded="false">
                                    <li><a href="{{ route('salaries.show') }}" data-i18n="Salary Slip">
                                            <i class="fas fa-file-invoice"></i> Salary Slip </a>
                                    </li>
                                    <li><a href="{{ route('advance-salaries.index') }}" data-i18n="My Leaves"><i
                                                class="fas fa-hand-holding-usd"></i> Advance Salaries</a></li>
                                    <li><a href="{{ route('expenses.index') }}" data-i18n="My Expenses"><i
                                                class="fas fa-receipt"></i> My Expenses</a></li>
                                </ul>
                            </li>

                            {{-- Documents & Resources --}}

                            <li>
                                <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                                    <div class="menu-icon">
                                        <i class="nav-icon fas fa-file-alt"></i>
                                    </div>
                                    <span class="nav-text" data-i18n="Apps">Documents & Resources</span>
                                </a>
                                <ul aria-expanded="false">
                                    <li><a href="{{ route('docs.employee') }}" data-i18n="Employee Documents">
                                            <i class="fas fa-file-pdf"></i> Employee Documents</a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        {{-- CLIENT ROLE MULTI-GROUPS --}}
                        @if (auth()->user()->isClient())
                            {{-- Projects & Meetings --}}

                            <li>
                                <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                                    <div class="menu-icon">
                                        <i class="nav-icon fas fa-folder-open"></i>
                                    </div>
                                    <span class="nav-text" data-i18n="Apps">Projects & Meetings</span>
                                </a>
                                <ul aria-expanded="false">
                                    <li><a href="{{ route('projects.index') }}" data-i18n="E My Projects & Tasks">
                                            <i class="fas fa-folder"></i> My Projects & Tasks</a>
                                    </li>
                                    <li><a href="{{ route('meetings.index') }}" data-i18n=" Google Meetings">
                                            <i class="fas fa-video"></i> Google Meetings</a>
                                    </li>
                                </ul>
                            </li>

                            {{-- Billing & Accounts --}}

                            <li>
                                <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                                    <div class="menu-icon">
                                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <span class="nav-text" data-i18n="Apps">Billing & Accounts</span>
                                </a>
                                <ul aria-expanded="false">
                                    <li><a href="{{ route('invoices.index') }}" data-i18n="My Invoices">
                                            <i class="fas fa-file-invoice-dollar"></i> My Invoices</a>
                                    </li>
                                    <li><a href="{{ route('estimates.index') }}" data-i18n="My Estimates">
                                            <i class="fas fa-calculator"></i> My Estimates</a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                    @endauth


                </ul>
            </div>

        </div>

        <main class="content-body">

            <div class="container-fluid">

                <div class="row">

                    @foreach (['success', 'error', 'info', 'warning', 'message'] as $type)
                        @if (session($type))
                            <div class="alert alert-{{ $type == 'error' ? 'danger' : $type }} alert-dismissible fade show"
                                role="alert">
                                {{ session($type) }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                    @endforeach
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-5"> @yield('content')</div>

                </div>

                <footer class="footer fixed-bottom main-footer mt-5">
                    <div class="container-fluid" id="footer-hide-mobile">
                        <div class="row new-footer ">
                            <div class="col-6 text-start">
                                &copy; {{ date('Y') }} {{ env('APP_NAME') }} | <span
                                    id="ct">{{ now()->format('d/M/Y, H:i:s') }}</span>
                            </div>
                            <div class="col-6 text-end">
                                <b><i class="fas fa-user"></i></b>

                                {{ auth()->user()->name ?? '' }}
                                (Dept: {{ auth()->user()->department ?? 'N/A' }})
                            </div>
                        </div>
                    </div>

                </footer>
            </div>
        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/js/all.min.js"></script>

    <style>
        .quill-editor {
            /*min-height: 120px;*/
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            background: #fff;
        }
    </style>

    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.wysiwyg').forEach(textarea => {
                textarea.style.display = 'none';
                const container = document.createElement('div');
                container.className = 'quill-editor';
                textarea.parentNode.insertBefore(container, textarea);

                const toolbarOptions = [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{
                        header: [1, 2, 3, false]
                    }],
                    [{
                        list: 'ordered'
                    }, {
                        list: 'bullet'
                    }],
                    [{
                        indent: '-1'
                    }, {
                        indent: '+1'
                    }],
                    [{
                        align: []
                    }],
                    ['link', 'image'],
                    ['clean']
                ];

                const quill = new Quill(container, {
                    theme: 'snow',
                    modules: {
                        toolbar: toolbarOptions
                    }
                });

                if (textarea.value) {
                    quill.root.innerHTML = textarea.value;
                }

                textarea.__quill = quill;

                const sync = () => {
                    textarea.value = quill.root.innerHTML.trim() === '<p><br></p>' ? '' : quill.root
                        .innerHTML;
                };

                quill.on('text-change', sync);
                textarea.closest('form')?.addEventListener('submit', sync);
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @yield('scripts')

    <!-- PWA Service Worker Registration -->
    {{-- <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('Service Worker registered successfully.', reg))
                    .catch((err) => console.error('Service Worker registration failed.', err));
            });
        }
    </script> --}}

    <!-- Prevent Double Submission -->
    <script>
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form && (!form.checkValidity || form.checkValidity())) {
                const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                setTimeout(() => {
                    buttons.forEach(button => {
                        button.disabled = true;
                        if (button.tagName === 'INPUT') {
                            button.value = 'Processing...';
                        } else {
                            button.innerHTML =
                                '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
                        }
                    });
                }, 10);
            }
        });
    </script>

    {{-- <script src="{{ asset('assets/vendor/jquery/dist/jquery.min.js') }}"></script> --}}
    <script src="{{ asset('assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/metismenu/dist/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/yaireoo/tagify/dist/tagify.js') }}"></script>
    <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

    @if (request()->routeIs('dashboard'))
        <script src="{{ asset('assets/js/dashboard/dashboard.js') }}"></script> @endif

        <script src=" {{ asset('assets/vendor/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/js/translator.js') }}"></script>

    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/icnav-init.js') }}"></script>

    <script src="{{ asset('assets/js/switcher/styleSwitcher.js') }}"></script>
    <script src="{{ asset('assets/js/switcher/demo.js') }}"></script>

    <script>
        window.onload = function() {
            document.body.classList.add("loaded");
        }
    </script>

</body>

</html>
