<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('storage/images/favicon.png') }}">
    <title>{{ config('app.name', 'CodeQuest') }}</title>

    <!-- Bootstrap CSS (RTL Version) & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.rtl.min.css">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Custom CSS Styles -->
    <style>
        .logo-img {
            mix-blend-mode: multiply;
        }

        /* تحسينات الشاشات الصغيرة */
        @media (max-width: 575.98px) {
            .navbar-brand span {
                font-size: 1rem !important;
            }

            .btn-responsive-text span {
                display: none;
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('css')
</head>

<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="app" class="d-flex flex-column min-vh-100">
        <!-- الهيدر والشريط العلوي -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-2">
            <div class="container px-3">

                <!-- الشعار -->
                <a class="navbar-brand d-flex align-items-center gap-2 text-primary fw-bold m-0 me-2"
                    href="{{ url('/') }}">
                    <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" class="logo-img"
                        style="max-height: 38px; width: auto;">
                    <span class="fs-5">{{ config('app.name', 'CodeQuest') }}</span>
                </a>

                <!-- زر القائمة للشاشات الصغيرة -->
                <button class="navbar-toggler border-0 shadow-none p-1" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- عناصر القائمة المنهارة -->
                <div class="collapse navbar-collapse mt-2 mt-lg-0" id="navbarSupportedContent">

                    <!-- أزرار الإجراءات السريعة -->
                    <div class="d-flex align-items-center gap-2 my-2 my-lg-0">
                        <a class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1 btn-responsive-text"
                            href="{{ route('ask') }}">
                            <i class="bi bi-plus-lg"></i>
                            <span>إسأل سؤالاً</span>
                        </a>

                        <a class="btn btn-primary btn-sm rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1 btn-responsive-text"
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

                    <!-- التنبيهات وحساب المستخدم (تتحاذى لليسار في الشاشات الكبيرة) -->
                    <div
                        class="d-flex align-items-center gap-3 ms-lg-auto mt-2 mt-lg-0 pt-2 pt-lg-0 border-top border-lg-0">

                        @auth
                            @if (auth()->user()->role === 'admin' || auth()->user()->isStaff())
                                <a class="nav-link text-secondary fw-semibold px-2" href="{{ route('dashboard') }}">
                                    لوحة التحكم
                                </a>
                            @endif
                        @endauth

                        @guest
                            @if (Route::has('login'))
                                <a class="nav-link px-2" href="{{ route('login') }}">تسجيل الدخول</a>
                            @endif

                            @if (Route::has('register'))
                                <a class="btn btn-primary btn-sm px-3 rounded-pill" href="{{ route('register') }}">إنشاء
                                    حساب</a>
                            @endif
                        @else
                            <!-- قائمة الإشعارات -->
                            <div class="dropdown me-auto me-lg-0">
                                <a class="nav-link position-relative p-2 rounded-circle hover-bg-light" href="#"
                                    id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                    onclick="loadNotifications()">
                                    <i class="bi bi-bell-fill text-secondary fs-5"></i>
                                    <span class="badge rounded-pill bg-danger position-absolute top-0 start-0 d-none"
                                        id="unread-badge"
                                        style="font-size: 0.65rem; transform: translate(-10%, 10%);">0</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0 mt-2"
                                    aria-labelledby="notificationDropdown" style="width: min(320px, 90vw);">
                                    <div
                                        class="dropdown-header d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
                                        <a href="{{ url('/notifications') }}"
                                            class="fw-bold text-decoration-none text-dark d-flex align-items-center gap-1">
                                            <span>الإشعارات</span>
                                            <i class="bi bi-box-arrow-up-left small"></i>
                                        </a>
                                        <span class="badge bg-primary rounded-pill" id="total-count">0</span>
                                    </div>
                                    <ul id="notifications-list" class="list-unstyled mb-0 style-scroll"
                                        style="max-height: 350px; overflow-y: auto;"></ul>
                                </div>
                            </div>

                            <!-- قائمة المستخدم -->
                            <div class="dropdown">
                                <button id="navbarDropdown"
                                    class="nav-link dropdown-toggle d-flex align-items-center gap-2 border-0 bg-transparent p-0"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://api.dicebear.com/7.x/identicon/svg?seed=' . Auth::user()->name }}"
                                        alt="{{ Auth::user()->name }}" class="rounded-circle border object-fit-cover"
                                        width="34" height="34">
                                    <span
                                        class="fw-semibold text-dark fs-6 d-none d-sm-inline">{{ Auth::user()->name }}</span>
                                </button>

                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2"
                                    aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                        href="{{ route('profile') }}">
                                        <i class="bi bi-person text-primary fs-5"></i> الملف الشخصي
                                    </a>
                                    <div class="dropdown-divider my-1"></div>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger"
                                        href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right fs-5"></i> تسجيل الخروج
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        @endguest

                    </div>
                </div>
            </div>
        </nav>

        <!-- المحتوى الرئيسي -->
        <main class="flex-grow-1 py-3">
            @yield('content')
        </main>

        <!-- التذييل (Footer) -->
        <footer class="mt-auto bg-white py-3 border-top">
            <div class="container text-center">
                <span class="text-muted small">حقوق النشر &copy; {{ config('app.name', 'CodeQuest') }}
                    {{ date('Y') }}</span>
                <br>
                <span class="text-muted small">للدعم الفني، يرجى التواصل عبر البريد الإلكتروني:</span>
                <a href="mailto:{{ $supportEmail ?? config('mail.from.address') }}"
                    class="small text-decoration-none">
                    {{ $supportEmail ?? config('mail.from.address') }}
                </a>
            </div>
        </footer>
    </div>

    @include('partials.flash-modal')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @stack('scripts')
    @auth
        <script>
            window.APP_URL = "{{ url('/') }}";
        </script>
        <script src="{{ asset('js/Notification.js') }}"></script>
    @endauth
</body>

</html>
