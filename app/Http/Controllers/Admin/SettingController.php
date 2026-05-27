<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Encryption\DecryptException; 

class SettingController extends Controller
{
    // ==========================================
    // 1. Hiển thị trang cài đặt
    // ==========================================
    public function index()
    {
        // Lấy dữ liệu từ bảng settings
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();

        // Giải mã mật khẩu trước khi đưa ra View
        foreach (['smtp_password', 'google_client_secret'] as $secretKey) {
            if (!empty($settings[$secretKey])) {
                try {
                    $settings[$secretKey] = Crypt::decryptString($settings[$secretKey]);
                } catch (DecryptException $e) {
                    $settings[$secretKey] = ''; 
                }
            }
        }

        // ----------------------------------------------------
        // Lấy số lượng thực tế từ Database
        // Nếu dùng SoftDeletes (xóa mềm), thêm ->whereNull('deleted_at')
        // ----------------------------------------------------
        $documentCount = DB::table('documents')->count();
        $examCount     = DB::table('exams')->count();
        $questionCount = DB::table('questions')->count();

        // Giả lập tính toán dung lượng (Bác có thể nâng cấp thành tính size file thật sau)
        $totalMB = 0.39; 
        $totalSpaceGB = 50; 
        $usedPercentage = ($totalMB / ($totalSpaceGB * 1024)) * 100;

        // Truyền TẤT CẢ các biến này ra View
        return view('dashboards.admin.admin_settings', compact(
            'settings', 
            'documentCount', 
            'examCount', 
            'questionCount',
            'totalMB',
            'totalSpaceGB',
            'usedPercentage'
        ));
    }

    // ==========================================
    // 2. Lưu cài đặt
    // ==========================================
    public function update(Request $request)
    {
        // Bỏ qua _token và _method của form
        $inputs = $request->except(['_token', '_method']);

        foreach ($inputs as $key => $value) {
            // FIX LỖI NULL: Đảm bảo value luôn có giá trị (ít nhất là chuỗi rỗng) để không chết DB
            $value = $value ?? '';

            // Nhóm cần mã hóa (Mật khẩu, Secret Key)
            if (in_array($key, ['smtp_password', 'google_client_secret'])) {
                if (!empty($value)) {
                    DB::table('settings')->updateOrInsert(
                        ['key' => $key], 
                        [
                            'value' => Crypt::encryptString($value), 
                            'updated_at' => now()
                        ]
                    );
                }
                continue; 
            }

            // Các ô input bình thường
            DB::table('settings')->updateOrInsert(
                ['key' => $key], 
                [
                    'value' => $value, 
                    'updated_at' => now()
                ]
            );
        }

        return redirect()->back()->with('success', 'Đã cập nhật cấu hình hệ thống thành công!');
    }

    // ==========================================
    // 3. Test Email SMTP
    // ==========================================
    public function testEmail(Request $request)
    {
        try {
            Config::set('mail.mailers.smtp.host', $request->host);
            Config::set('mail.mailers.smtp.port', $request->port);
            Config::set('mail.mailers.smtp.username', $request->username);
            Config::set('mail.mailers.smtp.password', $request->password);
            Config::set('mail.mailers.smtp.encryption', 'tls');
            
            app('mail.manager')->purge('smtp');

            $testEmailTo = auth()->user()->email ?? 'test@example.com';

            Mail::raw("Xin chào!\n\nNếu bạn nhận được email này, cấu hình SMTP của bạn đã hoạt động chính xác!", function ($message) use ($testEmailTo) {
                $message->to($testEmailTo)->subject('EduQuiz AI - Kiểm tra SMTP thành công');
            });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ==========================================
    // 4. Dọn dẹp Cache
    // ==========================================
    public function clearCache()
    {
        try {
            // Quét sạch sẽ toàn bộ Cache của Laravel
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return response()->json(['success' => true, 'message' => 'Đã dọn dẹp bộ nhớ đệm (Cache) sạch sẽ! Hệ thống sẽ chạy nhanh hơn.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi dọn dẹp: ' . $e->getMessage()]);
        }
    }
}