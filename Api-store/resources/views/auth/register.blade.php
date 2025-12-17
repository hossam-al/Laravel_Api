@extends('layouts.app')

@section('content')
<h2 style="margin-top:0;">إنشاء حساب</h2>
<form method="post" action="{{ route('register.post') }}" class="row">
    @csrf
    <label>الاسم
        <input type="text" name="name" value="{{ old('name') }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>البريد الإلكتروني
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>الهاتف
        <input type="text" name="phone" value="{{ old('phone') }}">
        @error('phone') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>كلمة المرور
        <input type="password" name="password" required>
        @error('password') <div class="error">{{ $message }}</div> @enderror
    </label>

    <label>تأكيد كلمة المرور
        <input type="password" name="password_confirmation" required>
    </label>

    <button type="submit">إنشاء الحساب</button>
  </form>
@endsection


