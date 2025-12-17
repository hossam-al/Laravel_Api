@extends('layouts.app')

@section('content')
<div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
    <h2 style="margin:0;">العلامات التجارية</h2>
    <a href="{{ route('web.brands.create') }}" class="btn" style="background:#2563eb;color:#fff;padding:10px 14px;border-radius:8px;">+ إضافة علامة</a>
    @if(session('ok'))
    <div style="background:#ecfdf5;color:#065f46;padding:8px 12px;border-radius:8px;">{{ session('ok') }}</div>
    @endif
</div>

<table style="width:100%;border-collapse:collapse;margin-top:12px;">
    <thead>
        <tr>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">#</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">الاسم</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">نشط؟</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">إجراءات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($brands as $b)
        <tr>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ $b->id }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ $b->name }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ $b->is_active ? 'نعم' : 'لا' }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;display:flex;gap:8px;">
                <a href="{{ route('web.brands.edit', $b) }}">تعديل</a>
                <form action="{{ route('web.brands.destroy', $b) }}" method="post" onsubmit="return confirm('حذف العلامة؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background:#b91c1c; color:#fff; padding:6px 10px; border-radius:6px;">حذف</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" style="padding:12px;">لا توجد علامات.</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top:12px;">
    {{ $brands->links() }}
    </div>
@endsection


