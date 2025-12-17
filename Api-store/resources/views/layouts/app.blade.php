<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'لوحة الويب' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <style>

    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <header>
            <div>
                <a href="/">الرئيسية</a>
                @auth
                    <a href="{{ route('web.products.index') }}" class="nav-link" style="margin-inline-start:12px;">المنتجات (Web)</a>
                    <a href="{{ route('web.categories.index') }}" class="nav-link" style="margin-inline-start:12px;">الأقسام (Web)</a>
                    <a href="{{ route('web.brands.index') }}" class="nav-link" style="margin-inline-start:12px;">العلامات (Web)</a>
                @endauth
            </div>
            <nav class="nav">
                @auth
                    <a href="{{ route('profile') }}">الملف الشخصي</a>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">تسجيل الخروج</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="post" style="display:none;">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}">تسجيل الدخول</a>
                    <a href="{{ route('register') }}">إنشاء حساب</a>
                @endauth
            </nav>
        </header>
        <div class="card">
            @yield('content')
        </div>
    </div>
</body>
</html>


