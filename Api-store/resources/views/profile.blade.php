@extends('layouts.app')

@section('content')
<h2 >الملف الشخصي</h2>
<div class="row">
    <label>الاسم
        <input type="text" value="{{ $user->name }}" disabled>
    </label>
    <label>البريد الإلكتروني
        <input type="text" value="{{ $user->email }}" disabled>
    </label>
    @if($user->phone)
    <label>الهاتف
        <input type="text" value="{{ $user->phone }}" disabled>
    </label>
    @endif
</div>
@endsection


