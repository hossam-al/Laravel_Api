@extends('layouts.app')

@section('content')
<h2 style="margin-top:0;">تعديل منتج #{{ $product->id }}</h2>
<form method="post" action="{{ route('web.products.update', $product) }}" class="row">
    @csrf
    <label>الاسم
        <input type="text" name="name" value="{{ old('name', $product->name) }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>الوصف
        <input type="text" name="description" value="{{ old('description', $product->description) }}" required>
        @error('description') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>السعر
        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required>
        @error('price') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>SKU
        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}">
        @error('sku') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>المخزون
        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required>
        @error('stock') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>القسم
        <select name="category_id">
            <option value="">—</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ (old('category_id', $product->category_id)==$c->id)?'selected':'' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        @error('category_id') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>العلامة التجارية
        <select name="brand_id">
            <option value="">—</option>
            @foreach($brands as $b)
            <option value="{{ $b->id }}" {{ (old('brand_id', $product->brand_id)==$b->id)?'selected':'' }}>{{ $b->name }}</option>
            @endforeach
        </select>
        @error('brand_id') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label style="display:flex; align-items:center; gap:8px;">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} style="width:auto;"> نشط
    </label>

    <button type="submit">حفظ التعديلات</button>
</form>
@endsection


