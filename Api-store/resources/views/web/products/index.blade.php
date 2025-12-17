@extends('layouts.app')

@section('content')
<div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
    <h2 style="margin:0;">المنتجات</h2>
    <a href="{{ route('web.products.create') }}" class="btn" style="background:#2563eb;color:#fff;padding:10px 14px;border-radius:8px;">+ إضافة منتج</a>
    @if(session('ok'))
    <div style="background:#ecfdf5;color:#065f46;padding:8px 12px;border-radius:8px;">{{ session('ok') }}</div>
    @endif
    @if($errors->any())
    <div style="background:#fef2f2;color:#991b1b;padding:8px 12px;border-radius:8px;">{{ $errors->first() }}</div>
    @endif
</div>

<table style="width:100%;border-collapse:collapse;margin-top:12px;">
    <thead>
        <tr>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">#</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">الاسم</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">السعر</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">المخزون</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">القسم</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">العلامة</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">نشط؟</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">إجراءات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $p)
        <tr>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ $p->id }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ $p->name }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ number_format($p->price, 2) }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ $p->stock }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ optional($p->Category)->name ?? '-' }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ optional($p->brand)->name ?? '-' }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ $p->is_active ? 'نعم' : 'لا' }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;display:flex;gap:8px;">
                <a href="{{ route('web.products.edit', $p) }}">تعديل</a>
                <form action="{{ route('web.products.destroy', $p) }}" method="post" onsubmit="return confirm('حذف المنتج؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background:#b91c1c; color:#fff; padding:6px 10px; border-radius:6px;">حذف</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="padding:12px;">لا توجد منتجات.</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top:12px;">
    {{ $products->links() }}
    </div>
@endsection


