@extends('layouts.app')

@section('content')
<h2 style="margin-top:0;">إضافة منتج</h2>
<form method="post" action="{{ route('web.products.store') }}" class="row">
    @csrf
    <label>الاسم
        <input type="text" name="name" value="{{ old('name') }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>الوصف
        <input type="text" name="description" value="{{ old('description') }}" required>
        @error('description') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>السعر
        <input type="number" step="0.01" name="price" value="{{ old('price', 0) }}" required>
        @error('price') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>SKU
        <input type="text" name="sku" value="{{ old('sku') }}">
        @error('sku') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>المخزون
        <input type="number" name="stock" value="{{ old('stock', 0) }}" required>
        @error('stock') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>القسم
        <select name="category_id">
            <option value="">—</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
        @error('category_id') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>العلامة التجارية
        <select name="brand_id">
            <option value="">—</option>
            @foreach($brands as $b)
            <option value="{{ $b->id }}">{{ $b->name }}</option>
            @endforeach
        </select>
        @error('brand_id') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label style="display:flex; align-items:center; gap:8px;">
        <input type="checkbox" name="is_active" value="1" checked style="width:auto;"> نشط
    </label>

    <button type="submit">حفظ</button>
</form>
@endsection


