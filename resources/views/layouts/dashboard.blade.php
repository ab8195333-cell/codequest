<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('storage/images/favicon.png') }}">

    <title>لوحة التحكم - {{ config('app.name', 'CodeQuest') }}</title>

    <!-- FontAwesome & Icons -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Template CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            text-align: right;
            overflow-x: hidden;
        }

        .logo-img {
            mix-blend-mode: multiply;
        }

        /* ضبط RTL للـ Sidebar */
        #wrapper #content-wrapper {
            margin-right: 0;
            width: 100%;
            overflow-x: hidden;
        }

        .sidebar {
            padding-right: 0 !important;
            z-index: 1030;
        }

        .sidebar .nav-item .nav-link {
            text-align: right;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-item .nav-link i {
            margin-left: 0.5rem;
            margin-right: 0;
        }

        .sidebar .nav-item .nav-link[data-toggle="collapse"]::after {
            float: left;
            transform: rotate(180deg);
            margin-left: auto;
        }

        .sidebar .nav-item .nav-link.collapsed[data-toggle="collapse"]::after {
            transform: rotate(0deg);
        }

        .sidebar .nav-item .collapse .collapse-inner {
            text-align: right;
        }

        /* إصلاح تموضع القوائم المنسدلة في الشريط العلوي لـ SB Admin 2 مع RTL */
        .topbar .dropdown-menu {
            right: auto !important;
            left: 0 !important;
        }

        /* تحسين استجابة الهواتف المحمولة */
        @media (max-width: 768px) {
            .sidebar {
                width: 100% !important;
            }

            .navbar-brand span {
                font-size: 0.9rem !important;
            }

            .top-btn-text span {
                display: none;
            }
        }
    </style>
    @stack('css')
</head>

<body id="page-top">

    <!-- غلاف الصفحة الرئيسي -->
    <div id="wrapper">

        <!-- القائمة الجانبية (Sidebar) -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled-md" id="accordionSidebar">

            <!-- الشعار -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center py-3"
                href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-code"></i>
                </div>
                <div class="sidebar-brand-text mx-2">لوحة التحكم</div>
            </a>

            <hr class="sidebar-divider my-0">

            <!-- 1. أجنحة الإدارة الخاصة بالـ ADMIN فقط -->
            @if (auth()->user()->role === 'admin')
                <div class="sidebar-heading mt-3 px-3">إدارة النظام (Admin)</div>

                <li class="nav-item {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.index') }}">
                        <i class="fas fa-fw fa-chart-line"></i>
                        <span>الإحصائيات التحليلية</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.settings.index') }}">
                        <i class="fas fa-fw fa-cogs"></i>
                        <span>الإعدادات والـ AI</span>
                    </a>
                </li>

                <li
                    class="nav-item {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.update-role') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.users.index') }}">
                        <i class="fas fa-fw fa-user-shield"></i>
                        <span>أدوار المستخدمين</span>
                    </a>
                </li>

                <hr class="sidebar-divider">
            @endif

            <!-- 2. أجنحة الإشراف (Admin + Moderator) -->
            <div class="sidebar-heading px-3">إدارة المحتوى (Moderation)</div>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('index') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>الرئيسية للمشرف</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reports.index') }}">
                    <i class="fas fa-fw fa-flag"></i>
                    <span>إدارة البلاغات</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="fas fa-fw fa-user-slash"></i>
                    <span>رقابة المستخدمين</span>
                </a>
            </li>

            <li
                class="nav-item {{ request()->routeIs('categories.*') || request()->routeIs('tags.*') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTaxonomy"
                    aria-expanded="true" aria-controls="collapseTaxonomy">
                    <i class="fas fa-fw fa-tags"></i>
                    <span>التصنيفات والوسوم</span>
                </a>
                <div id="collapseTaxonomy"
                    class="collapse {{ request()->routeIs('categories.*') || request()->routeIs('tags.*') ? 'show' : '' }}"
                    aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                            href="{{ route('categories.index') }}">التصنيفات</a>
                        <a class="collapse-item {{ request()->routeIs('tags.*') ? 'active' : '' }}"
                            href="{{ route('tags.index') }}">الوسوم</a>
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <!-- زر طي القائمة الجانبية -->
            <div class="text-center d-none d-md-inline my-2">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>

        <!-- غلاف المحتوى العرضي -->
        <div id="content-wrapper" class="d-flex flex-column min-vh-100">

            <!-- الشريط العلوي (Topbar) -->
            <nav
                class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm px-3 justify-content-between">

                <!-- زر التحكم بالقائمة الجانبية للشاشات الصغيرة -->
                <div class="d-flex align-items-center gap-2">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle me-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- الشعار وأزرار التنقل السريعة -->
                    <a class="navbar-brand d-flex align-items-center text-primary fw-bold m-0"
                        href="{{ url('/') }}">
                        <img src="{{ asset('storage/images/logo.png') }}" alt="Logo"
                            style="max-height: 35px; width: auto;" class="logo-img me-2">
                        <span class="h6 mb-0 d-none d-sm-inline">{{ config('app.name', 'CodeQuest') }}</span>
                    </a>

                    <a class="btn btn-outline-primary btn-sm rounded-pill px-2 px-md-3 py-1 ms-2 d-inline-flex align-items-center gap-1 top-btn-text"
                        href="{{ route('ask') }}">
                        <i class="bi bi-plus-lg"></i>
                        <span>إسأل سؤالاً</span>
                    </a>

                    <a class="btn btn-primary btn-sm rounded-pill px-2 px-md-3 py-1 d-inline-flex align-items-center gap-1 top-btn-text"
                        href="{{ route('ai.assistant.index') }}">
                        <i class="bi bi-robot"></i>
                        <span>مساعد AI</span>
                    </a>
                    <a class="btn btn-light btn-sm rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1 text-secondary border"
                        href="{{ route('tags&categories') }}">
                        <i class="bi bi-tags"></i>
                        <span>الوسوم</span>
                    </a>
                </div>

                <!-- العناصر جهة اليسار (الإشعارات والملف الشخصي) -->
                <ul class="navbar-nav ms-auto align-items-center flex-row gap-2">
                    @auth
                        <!-- الإشعارات -->
                        <li class="nav-item dropdown no-arrow ms-2">
                            <a class="nav-link position-relative p-2 rounded-circle hover-bg-light" href="#"
                                id="notificationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" onclick="loadNotifications()">
                                <i class="bi bi-bell-fill text-secondary fs-5"></i>
                                <span class="badge badge-danger badge-counter position-absolute d-none" id="unread-badge"
                                    style="font-size: 0.65rem; top: 2px; right: 2px;">0</span>
                            </a>

                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow border-0 animated--grow-in mt-2"
                                aria-labelledby="notificationDropdown" style="width: min(320px, 90vw);">
                                <div
                                    class="dropdown-header d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
                                    <a href="{{ url('/notifications') }}"
                                        class="font-weight-bold text-decoration-none text-dark d-flex align-items-center gap-1">
                                        <span>الإشعارات</span>
                                        <i class="bi bi-box-arrow-up-left small"></i>
                                    </a>
                                    <span class="badge badge-primary badge-pill" id="total-count">0</span>
                                </div>
                                <ul id="notifications-list" class="list-unstyled mb-0 style-scroll"
                                    style="max-height: 350px; overflow-y: auto;"></ul>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- ملف المستخدم -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 p-0" href="#"
                                id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://api.dicebear.com/7.x/identicon/svg?seed=' . Auth::user()->name }}"
                                    alt="{{ Auth::user()->name }}"
                                    class="img-profile rounded-circle border object-fit-cover" width="32"
                                    height="32">
                                <span
                                    class="ms-2 d-none d-lg-inline text-gray-600 small fw-bold">{{ Auth::user()->name }}</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right shadow border-0 animated--grow-in mt-2"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                    href="{{ route('profile') }}">
                                    <i class="bi bi-person text-primary fs-5"></i> الملف الشخصي
                                </a>
                                <div class="dropdown-divider my-1"></div>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="#"
                                    data-toggle="modal" data-target="#logoutModal">
                                    <i class="bi bi-box-arrow-right fs-5"></i> تسجيل الخروج
                                </a>
                            </div>
                        </li>
                    @endauth
                </ul>

            </nav>

            <!-- المحتوى الرئيسي -->
            <main id="content" class="px-2 px-md-4 flex-grow-1">
                @yield('content')
            </main>

            <!-- التذييل (Footer) -->
            <footer class="sticky-footer bg-white mt-auto py-3 border-top">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>حقوق النشر &copy; {{ config('app.name', 'CodeQuest') }} {{ date('Y') }}</span>
                        <br>
                        <span>للدعم الفني، يرجى التواصل عبر البريد الإلكتروني:</span>
                        <a href="mailto:{{ $supportEmail ?? config('mail.from.address') }}">
                            {{ $supportEmail ?? config('mail.from.address') }}
                        </a>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <!-- زر التمرير إلى الأعلى -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- نافذة تسجيل الخروج المنبثقة -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="logoutModalLabel">هل أنت جاهز للمغادرة؟</h5>
                    <button class="close border-0 bg-transparent" type="button" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">حدد "تسجيل الخروج" أدناه إذا كنت مستعدًا لإنهاء جلستك الحالية.</div>
                <div class="modal-footer border-0">
                    <button class="btn btn-secondary btn-sm rounded-pill px-3" type="button"
                        data-dismiss="modal">إلغاء</button>
                    <a class="btn btn-primary btn-sm rounded-pill px-3" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        تسجيل الخروج
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    @include('dashboard.flash-modal')
    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @auth
        <script>
            window.APP_URL = "{{ url('/') }}";
        </script>
        <script src="{{ asset('js/Notification.js') }}"></script>
    @endauth
</body>

</html>
