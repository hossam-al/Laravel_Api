@extends('layouts.app')

@section('content')
<div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
    <h2 style="margin:0;">الأقسام</h2>
    <a href="{{ route('web.categories.create') }}" class="btn" style="background:#2563eb;color:#fff;padding:10px 14px;border-radius:8px;">+ إضافة قسم</a>
    @if(session('ok'))
    <div style="background:#ecfdf5;color:#065f46;padding:8px 12px;border-radius:8px;">{{ session('ok') }}</div>
    @endif
</div>

<table style="width:100%;border-collapse:collapse;margin-top:12px;">
    <thead>
        <tr>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">#</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">الاسم</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">Slug</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">نشط؟</th>
            <th style="text-align:right;padding:8px;border-bottom:1px solid #eee;">إجراءات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($categories as $c)
        <tr>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ $c->id }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ $c->name }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ $c->slug }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;">{{ $c->is_active ? 'نعم' : 'لا' }}</td>
            <td style="padding:8px;border-bottom:1px solid #f3f4f6;display:flex;gap:8px;">
                <a href="{{ route('web.categories.edit', $c) }}">تعديل</a>
                <form action="{{ route('web.categories.destroy', $c) }}" method="post" onsubmit="return confirm('حذف القسم؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background:#b91c1c; color:#fff; padding:6px 10px; border-radius:6px;">حذف</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" style="padding:12px;">لا توجد أقسام.</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top:12px;">
    {{ $categories->links() }}
    </div>
@endsection


