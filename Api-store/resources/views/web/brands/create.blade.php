@extends('layouts.app')

@section('content')
<h2 style="margin-top:0;">إضافة علامة</h2>
<form method="post" action="{{ route('web.brands.store') }}" class="row">
    @csrf
    <label>الاسم
        <input type="text" name="name" value="{{ old('name') }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </label>
    <label>الوصف
        <input type="text" name="description" value="{{ old('description') }}">
    </label>
    <label style="display:flex; align-items:center; gap:8px;">
        <input type="checkbox" name="is_active" value="1" checked style="width:auto;"> نشط
    </label>
    <button type="submit">حفظ</button>
</form>
@endsection


