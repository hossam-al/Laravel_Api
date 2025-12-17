<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - API</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>

    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/favicon.ico">
    <meta name="robots" content="noindex">
    <meta name="turbolinks-cache-control" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#111827">
    <meta name="description" content="لوحة تحكم بسيطة لإدارة API ومتابعة الإحصائيات الأساسية.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">

    <script defer src="{{ asset('assets/js/dashboard.js') }}"></script>


</head>

<body>
    <div class="container">
        <header>
            <div>
                <h1>لوحة التحكم</h1>
                <span>إحصائيات سريعة للنظام</span>
            </div>
            <div class="dropdown">
                <button class="dropdown-btn" id="profileToggle">
                    <span class="avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span>{{ auth()->user()->name }}</span>
                    <i class="chev"></i>
                </button>
                <div class="menu" id="profileMenu">
                    <div class="hdr">
                        <div class="name">{{ auth()->user()->name }}</div>
                        <div class="email">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="item">
                        <a href="{{ route('profile') }}">فتح الملف الشخصي</a>
                    </div>
                    <div class="item">
                        <form method="post" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="width:100%;">تسجيل الخروج</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>


        <nav>
            <a href="{{ route('admin.dashboard') }}">الرئيسية</a>
            <a href="/products">المنتجات</a>
        </nav>
        <form method="get" action="{{ route('admin.dashboard') }}" style="margin-bottom:16px;">
            <label>المدى الزمني:
                <select name="range" onchange="this.form.submit()">
                    <option value="24h" {{ ($cartMetrics['range'] ?? '') === '24h' ? 'selected' : '' }}>آخر 24 ساعة
                    </option>
                    <option value="7d" {{ ($cartMetrics['range'] ?? '') === '7d' ? 'selected' : '' }}>آخر 7 أيام
                    </option>
                    <option value="30d" {{ ($cartMetrics['range'] ?? '') === '30d' ? 'selected' : '' }}>آخر 30 يوماً
                    </option>
                    <option value="all" {{ ($cartMetrics['range'] ?? '') === 'all' ? 'selected' : '' }}>الكل
                    </option>
                </select>
            </label>
        </form>

        <section class="grid">
            <div class="card">
                <div class="label">المستخدمون</div>
                <div class="value">{{ number_format($metrics['users']) }}</div>
            </div>
            <div class="card">
                <div class="label">المنتجات</div>
                <div class="value">{{ number_format($metrics['products']) }}</div>
            </div>
            <div class="card">
                <div class="label">الطلبات</div>
                <div class="value">{{ number_format($metrics['orders']) }}</div>
            </div>
            <div class="card">
                <div class="label">الأقسام</div>
                <div class="value">{{ number_format($metrics['categories']) }}</div>
            </div>
            <div class="card">
                <div class="label">العلامات التجارية</div>
                <div class="value">{{ number_format($metrics['brands']) }}</div>
            </div>
        </section>

        <section class="grid" style="margin-top:12px;">
            <div class="card">
                <div class="label">السلال</div>
                <div class="value">{{ number_format($cartMetrics['carts']) }}</div>
            </div>
            <div class="card">
                <div class="label">عناصر السلال</div>
                <div class="value">{{ number_format($cartMetrics['cart_items']) }}</div>
            </div>
            <div class="card">
                <div class="label">قائمة الرغبات</div>
                <div class="value">{{ number_format($cartMetrics['wishlists']) }}</div>
            </div>
            <div class="card">
                <div class="label">الإيرادات (المدى المحدد)</div>
                <div class="value">{{ number_format($cartMetrics['revenue'], 2) }}</div>
            </div>
            <div class="card">
                <div class="label">متوسط قيمة الطلب</div>
                <div class="value">{{ number_format($cartMetrics['avg_order_value'], 2) }}</div>
            </div>
        </section>

        <h2 class="section-title">أكثر المسارات استخداماً</h2>
        <table>
            <thead>
                <tr>
                    <th>الطريقة</th>
                    <th>المسار</th>
                    <th>عدد الطلبات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topEndpoints as $row)
                    <tr>
                        <td>{{ $row->method }}</td>
                        <td>{{ $row->path }}</td>
                        <td>{{ number_format($row->hits) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">لا توجد بيانات بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h2 class="section-title">أحدث 20 طلب</h2>
        <table>
            <thead>
                <tr>
                    <th>الوقت</th>
                    <th>الطريقة</th>
                    <th>المسار</th>
                    <th>الحالة</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $log->method }}</td>
                        <td>{{ $log->path }}</td>
                        <td>{{ $log->status_code }}</td>
                        <td>{{ $log->ip }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">لا توجد بيانات بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="cols">
            <div>
                <h2 class="section-title">أحدث الطلبات</h2>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستخدم</th>
                            <th>الحالة</th>
                            <th>العناصر</th>
                            <th>الإجمالي</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $o)
                            <tr>
                                <td>{{ $o->id }}</td>
                                <td>{{ optional($o->user)->name ?? '-' }}</td>
                                <td>{{ $o->status }}</td>
                                <td>{{ $o->items_count }}</td>
                                <td>{{ number_format($o->total_price, 2) }}</td>
                                <td>{{ $o->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">لا توجد طلبات.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                <h2 class="section-title">توزيع الحالات</h2>
                <table>
                    <thead>
                        <tr>
                            <th>الحالة</th>
                            <th>عدد الطلبات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orderStatusBreakdown as $row)
                            <tr>
                                <td>{{ $row->status }}</td>
                                <td>{{ number_format($row->count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">لا توجد بيانات.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="cols">
            <div>
                <h2 class="section-title">أكثر المنتجات إضافة للسلة</h2>
                <table>
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>مرات الإضافة</th>
                            <th>الكمية الإجمالية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCartProducts as $p)
                            <tr>
                                <td>#{{ $p->product_id }}</td>
                                <td>{{ number_format($p->times) }}</td>
                                <td>{{ number_format($p->qty) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">لا توجد بيانات.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                <h2 class="section-title">أكثر المنتجات في المفضلة</h2>
                <table>
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>مرات الإضافة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topWishlistProducts as $p)
                            <tr>
                                <td>#{{ $p->product_id }}</td>
                                <td>{{ number_format($p->times) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">لا توجد بيانات.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">© {{ date('Y') }} لوحة تحكم بسيطة - Laravel API</div>
    </div>
</body>

</html>
