<?php

namespace App\Http\Controllers\Core;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog; 

class ProfileController extends Controller
{
    // ==========================================
    // KHU VỰC CỦA GIẢNG VIÊN (TEACHER)
    // ==========================================
    
    public function teacherEdit()
    {
        $user = Auth::user();
        return view('dashboards.teacher.teacher_settings', compact('user'));
    }

    public function teacherUpdateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $user->name = $request->name;
        $user->phone = $request->phone;

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '_teacher_' . $user->id . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/avatars');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            if ($user->avatar && File::exists(public_path($user->avatar))) {
                File::delete(public_path($user->avatar));
            }

            $file->move($destinationPath, $fileName);
            $user->avatar = 'uploads/avatars/' . $fileName;
        }

        $user->save();

        // ==========================================
        // GHI LOG: GIẢNG VIÊN CẬP NHẬT HỒ SƠ
        // ==========================================
        ActivityLog::create([
            'type' => 'profile_updated',
            'title' => 'Giảng viên cập nhật hồ sơ',
            'description' => 'Giảng viên <strong>' . $user->name . '</strong> vừa cập nhật thông tin cá nhân.',
            'icon_class' => 'bi-person-lines-fill',
            'color_theme' => 'info'
        ]);

        return back()->with('success_profile', 'Đã cập nhật thông tin cá nhân thành công!');
    }

    public function teacherUpdatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.'])->with('error_tab', 'password');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        // ==========================================
        // GHI LOG: GIẢNG VIÊN ĐỔI MẬT KHẨU
        // ==========================================
        ActivityLog::create([
            'type' => 'password_changed',
            'title' => 'Cảnh báo bảo mật',
            'description' => 'Giảng viên <strong>' . $user->name . '</strong> vừa thay đổi mật khẩu tài khoản.',
            'icon_class' => 'bi-shield-lock-fill',
            'color_theme' => 'warning'
        ]);

        return back()->with('success_password', 'Đã đổi mật khẩu thành công!');
    }


    // ==========================================
    // KHU VỰC CỦA HỌC VIÊN (STUDENT)
    // ==========================================
    
    public function studentEdit()
    {
        return view('dashboards.student.student_settings'); 
    }

    public function studentUpdateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Email đã bị disable ở giao diện nên không cần validate và cập nhật
        ]);

        $user->name = $request->name;
        $user->phone = $request->phone;

        // Xử lý Upload Avatar cho Student (Giống Teacher)
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '_student_' . $user->id . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/avatars');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            if ($user->avatar && File::exists(public_path($user->avatar))) {
                File::delete(public_path($user->avatar));
            }

            $file->move($destinationPath, $fileName);
            $user->avatar = 'uploads/avatars/' . $fileName;
        }

        $user->save();

        // ==========================================
        // GHI LOG: HỌC VIÊN CẬP NHẬT HỒ SƠ
        // ==========================================
        ActivityLog::create([
            'type' => 'profile_updated',
            'title' => 'Học viên cập nhật hồ sơ',
            'description' => 'Học viên <strong>' . $user->name . '</strong> vừa thay đổi thông tin/avatar.',
            'icon_class' => 'bi-person-badge',
            'color_theme' => 'info'
        ]);

        return back()->with('success_profile', 'Đã cập nhật thông tin và ảnh đại diện thành công!');
    }

    public function studentUpdatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        // ==========================================
        // GHI LOG: HỌC VIÊN ĐỔI MẬT KHẨU
        // ==========================================
        ActivityLog::create([
            'type' => 'password_changed',
            'title' => 'Cảnh báo bảo mật',
            'description' => 'Học viên <strong>' . $user->name . '</strong> vừa thay đổi mật khẩu.',
            'icon_class' => 'bi-shield-lock-fill',
            'color_theme' => 'warning'
        ]);

        return back()->with('success_password', 'Mật khẩu bảo mật đã được thay đổi!');
    }
}
