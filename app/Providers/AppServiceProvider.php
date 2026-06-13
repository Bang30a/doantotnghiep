<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View; 
use Illuminate\Support\Facades\Crypt;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            // Không chạy khi đang ở màn hình Console (VD: php artisan migrate)
            if (!app()->runningInConsole() && Schema::hasTable('settings')) {
                
                $settings = DB::table('settings')->pluck('value', 'key')->toArray();
                
                // 1. Chia sẻ biến global cho toàn bộ views
                View::share('globalSettings', $settings);

                // 2. Ghi đè thời gian sống của Session
                if (isset($settings['session_timeout'])) {
                    config(['session.lifetime' => $settings['session_timeout']]);
                }

                $systemTimezone = $settings['system_timezone'] ?? 'Asia/Ho_Chi_Minh';
                if (in_array($systemTimezone, \DateTimeZone::listIdentifiers(), true)) {
                    config(['app.timezone' => $systemTimezone]);
                    date_default_timezone_set($systemTimezone);
                }

                // 3. Ghi đè cấu hình SMTP
                if (!empty($settings['smtp_host']) && !empty($settings['smtp_username'])) {
                    config([
                        'mail.mailers.smtp.host' => $settings['smtp_host'],
                        'mail.mailers.smtp.port' => $settings['smtp_port'],
                        'mail.mailers.smtp.username' => $settings['smtp_username'],
                        // Giải mã password (Nếu lỗi thì fallback về .env)
                        'mail.mailers.smtp.password' => !empty($settings['smtp_password']) ? Crypt::decryptString($settings['smtp_password']) : config('mail.mailers.smtp.password'),
                        'mail.from.address' => $settings['smtp_username'],
                        'mail.from.name' => $settings['site_name'] ?? 'EduQuiz AI',
                    ]);
                }

                // 4. Ghi đè cấu hình Google Login (Socialite)
                if (!empty($settings['google_client_id'])) {
                    config([
                        'services.google.client_id' => $settings['google_client_id'],
                        // Giải mã secret (Nếu lỗi thì fallback về .env)
                        'services.google.client_secret' => !empty($settings['google_client_secret']) ? Crypt::decryptString($settings['google_client_secret']) : config('services.google.client_secret'),
                        'services.google.redirect' => url('/auth/google/callback'),
                    ]);
                }
                
            }
        } catch (\Exception $e) {
            // Nếu có lỗi giải mã hoặc kết nối DB ở bước boot này thì im lặng bỏ qua, 
            // hệ thống sẽ tự fallback về cấu hình mặc định trong .env
        }
    }
}
