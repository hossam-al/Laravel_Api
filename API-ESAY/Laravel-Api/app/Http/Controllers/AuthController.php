<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    // ✅ Register
    public function register(Request $request)
    {
        $mega = 2 * 1024 * 1024; // 2 MB

        // تحقق من وجود الأدوار المطلوبة
        $customerRole = Role::where('slug', 'customer')->first()->id ?? 3; // افتراضي للعميل
        $adminRole = Role::where('slug', 'admin')->first()->id ?? 2; // افتراضي للأدمن

        $request->validate([
            'image_path' => "file|max:$mega|nullable",
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|min:10|max:20|regex:/^[0-9+\-\s]+$/|unique:users,phone',
            'password' => 'required|confirmed',
            'role_id'  => 'sometimes|exists:roles,id'
        ]);

        if ($request->hasFile('image_path')) {
            $image_data = $request->file('image_path');
            $image_name = time() . $image_data->getClientOriginalName();
            $location = public_path('upload'); // Corrected variable name
            $image_data->move($location, $image_name);
            $image_path = url('upload/' . $image_name); // Generate the full URL for the image
        } else {
            $image_path = null;
        }

        // التحقق إذا كان المستخدم الحالي Owner يمكنه إنشاء Admin
        $currentUser = Auth::user();
        $requestedRole = $request->role_id;

        // إذا كان المستخدم غير مسجل دخول أو المستخدم الحالي ليس Owner
        if (!$currentUser || ($currentUser && ($currentUser->role_id != 1))) {
            // تعيين الدور افتراضيًا إلى Customer
            $userRole = $customerRole;
        } else {
            // المستخدم الحالي هو Owner ويمكنه تعيين أدوار
            $userRole = $requestedRole ?? $customerRole;
        }

        $user = User::create([
            'name' => $request->name,
            'role_id' => $userRole,
            'image_url' => $image_path,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('mytoken')->plainTextToken;

        return response([
            'status'  => true,
            'message' => 'User registered successfully',
            'data'    => $user->makeHidden(['role_id']),
            'token'   => $token,
        ], 201);
    }

    // ✅ Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'status'  => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // جلب معلومات الدور
        $user->load('role:id,name');

        $token = $user->createToken('mytoken')->plainTextToken;

        return response([
            'status'  => true,
            'message' => 'Login successful',
            'data'    => $user->makeHidden(['role_id']),
            'token'   => $token,
        ], 200);
    }

    public function update(Request $request)
    {
        $id = Auth::id();
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        $request->validate([
            'image_path' => 'file|max:2048|nullable', // 2MB
            'name'       => 'sometimes|string',
            'email'      => 'sometimes|email|unique:users,email,' . $id,
            'phone'      => 'sometimes|string|min:10|max:20|regex:/^[0-9+\-\s]+$/',
            'password'   => 'sometimes|confirmed',
            'role_id'    => 'sometimes|exists:roles,id',
        ]);

        if ($request->hasFile('image_path')) {
            $image_data = $request->file('image_path');
            $image_name = time() . '_' . $image_data->getClientOriginalName();
            $location = public_path('upload');
            $image_data->move($location, $image_name);
            $image_path = url('upload/' . $image_name);
        } else {
            $image_path = $user->image_url; // احتفظ بالصورة القديمة
        }

        $updateData = [
            'name'       => $request->name ?? $user->name,
            'image_url'  => $image_path,
            'email'      => $request->email ?? $user->email,
            'phone'      => $request->phone ?? $user->phone,
            'password'   => $request->password ? bcrypt($request->password) : $user->password,
        ];

        // فقط Owner يمكنه تغيير الأدوار
        if ($request->has('role_id') && $currentUser && $currentUser->role_id == 1) {
            $updateData['role_id'] = $request->role_id;
        }

        $user->update($updateData);

        // تحميل معلومات الدور
        $user->load('role:id,name');

        $token = $user->createToken('mytoken')->plainTextToken;

        return response([
            'status'  => true,
            'message' => 'User updated successfully',
            'data'    => $user,
            'token'   => $token,
        ], 200);
    }

    // ✅ Logout
    public function logout(Request $request)
    {
        // تسجيل الخروج باستخدام طريقة مباشرة
        $request->user()->currentAccessToken()->delete();

        return response([
            'status'  => true,
            'message' => 'Logout successful',
        ], 200);
    }

    // ✅ Delete User
    public function deleteUser()
    {
        $user = Auth::user();

        if (!$user) {
            return response([
                'status'  => false,
                'message' => 'User not found',
            ], 404);
        }

        User::destroy($user->id); // استخدام طريقة بديلة للحذف

        return response([
            'status'  => true,
            'message' => 'User deleted successfully',
        ], 200);
    }
}
