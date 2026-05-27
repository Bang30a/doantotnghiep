<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // ==========================================
    // PHẦN 1: XỬ LÝ ĐĂNG NHẬP / ĐĂNG XUẤT
    // ==========================================

    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Tạo lại CSRF token mới khi mở trang login
        $request->session()->regenerateToken();

        return response()
            ->view('auth.auth', ['mode' => 'login'])
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $request->session()->regenerateToken();

            $user = Auth::user();

            $roleName = match ($user->role) {
                'student' => 'Học viên',
                'teacher' => 'Giảng viên',
                'admin' => 'Quản trị viên',
                default => 'Người dùng',
            };

            ActivityLog::create([
                'type' => 'user_login',
                'title' => 'Đăng nhập hệ thống',
                'description' => $roleName . ' <strong>' . $user->name . '</strong> vừa đăng nhập.',
                'icon_class' => 'bi-box-arrow-in-right',
                'color_theme' => 'success'
            ]);

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors([
                'email' => 'Thông tin đăng nhập không chính xác.',
            ])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Đăng xuất thành công.')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }

    // ==========================================
    // PHẦN 2: XỬ LÝ ĐĂNG KÝ TÀI KHOẢN
    // ==========================================

    public function showRegister(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $request->session()->regenerateToken();

        return response()
            ->view('auth.auth', ['mode' => 'register'])
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }

    public function register(Request $request)
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();

        $passwordRule = PasswordRule::min($settings['min_password_length'] ?? 8);

        if (($settings['require_uppercase'] ?? '1') == '1') {
            $passwordRule->mixedCase();
        }

        if (($settings['require_number'] ?? '1') == '1') {
            $passwordRule->numbers();
        }

        if (($settings['require_special_char'] ?? '1') == '1') {
            $passwordRule->symbols();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', $passwordRule],
            'role' => 'required|in:student,teacher',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'student',
            'status' => 'active',
        ]);

        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->regenerateToken();

        $roleName = $user->role == 'student' ? 'Học viên' : 'Giảng viên';

        ActivityLog::create([
            'type' => 'user_registered',
            'title' => 'Người dùng mới đăng ký',
            'description' => $roleName . ' <strong>' . $user->name . '</strong> vừa tạo tài khoản thành công.',
            'icon_class' => 'bi-person-plus-fill',
            'color_theme' => 'indigo'
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Đăng ký tài khoản thành công!');
    }

    // ==========================================
    // PHẦN 3: XỬ LÝ QUÊN MẬT KHẨU
    // ==========================================

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Bổ sung Headers chống Cache cho trang Quên mật khẩu
        return response()
            ->view('auth.forgot-password')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $status = PasswordFacade::sendResetLink($request->only('email'));

        return $status === PasswordFacade::RESET_LINK_SENT
            ? back()->with('success', 'Chúng tôi đã gửi link đặt lại mật khẩu vào email của bạn!')
            : back()->withErrors(['email' => 'Không thể gửi email lúc này. Vui lòng thử lại.']);
    }

    public function showResetForm($token)
    {
        // Bổ sung Headers chống Cache cho trang Đổi mật khẩu
        return response()
            ->view('auth.reset-password', ['token' => $token])
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = PasswordFacade::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        // ĐÃ SỬA: Thêm Headers ép trình duyệt tải lại trang Đăng nhập sau khi đổi pass thành công
        if ($status === PasswordFacade::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Mật khẩu của bạn đã được đặt lại thành công!')
                ->withHeaders([
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]);
        }

        return back()->withErrors(['email' => 'Token không hợp lệ hoặc đã hết hạn.']);
    }

    // ==========================================
    // PHẦN 4: ĐĂNG NHẬP GOOGLE
    // ==========================================

    public function redirectToProvider($provider)
    {
        if ($provider !== 'google') {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Hệ thống hiện chỉ hỗ trợ đăng nhập qua Google.']);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $user->avatar ?? $socialUser->getAvatar(),
                ]);
            } else {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'role' => 'student',
                    'status' => 'active',
                ]);
            }

            Auth::login($user);

            request()->session()->regenerate();
            request()->session()->regenerateToken();

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Lỗi xác thực từ ' . ucfirst($provider) . '. Vui lòng thử lại!']);
        }
    }
}