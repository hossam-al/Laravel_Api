@extends('layouts.app')

@section('content')
<h2 style="margin-top:0;">تعديل علامة #{{ $brand->id }}</h2>
<form method="post" action="{{ route('web.brands.update', $brand) }}" class="row">
    @csrf
    <label>الاسم
        <input type="text" name="name" value="{{ old('name', $brand->name) }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </label>
    <label>الوصف
        <input type="text" name="description" value="{{ old('description', $brand->description) }}">
    </label>
    <label style="display:flex; align-items:center; gap:8px;">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $brand->is_active) ? 'checked' : '' }} style="width:auto;"> نشط
    </label>
    <button type="submit">حفظ التعديلات</button>
</form>
@endsection


