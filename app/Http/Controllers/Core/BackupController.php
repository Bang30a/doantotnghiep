<?php

namespace App\Http\Controllers\Core;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;

// Nếu file của bác đang lỗi Controller gốc thì đổi thành class BackupController extends \Illuminate\Routing\Controller nhé
class BackupController extends Controller
{
    public function downloadDatabase()
    {
        try {
            $date = now()->format('Y_m_d_H_i_s');
            $fileName = "EduQuiz_Backup_{$date}.sql";
            
            $storagePath = storage_path("app/backups");
            if (!File::exists($storagePath)) {
                File::makeDirectory($storagePath, 0755, true);
            }
            $filePath = $storagePath . '/' . $fileName;

            // ==========================================
            // LOGIC SAO LƯU BẰNG PHP THUẦN (100% CHẠY ĐƯỢC)
            // ==========================================
            $tables = DB::select('SHOW TABLES');
            $sql = "-- BẢN SAO LƯU CƠ SỞ DỮ LIỆU EDUQUIZ AI\n";
            $sql .= "-- Thời gian: " . now()->format('d/m/Y H:i:s') . "\n\n";

            foreach ($tables as $table) {
                // Lấy tên bảng động
                $tableName = array_values((array)$table)[0];
                
                // 1. Lấy cấu trúc tạo bảng (CREATE TABLE)
                $createTable = DB::select("SHOW CREATE TABLE `$tableName`")[0]->{'Create Table'};
                $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
                $sql .= $createTable . ";\n\n";

                // 2. Lấy toàn bộ dữ liệu trong bảng (INSERT INTO)
                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $sql .= "INSERT INTO `$tableName` VALUES(";
                    $values = [];
                    foreach ($row as $val) {
                        if (is_null($val)) {
                            $values[] = "NULL";
                        } else {
                            // Xử lý các ký tự đặc biệt để không bị lỗi SQL
                            $val = addslashes($val);
                            $val = str_replace("\n", "\\n", $val);
                            $values[] = "'" . $val . "'";
                        }
                    }
                    $sql .= implode(",", $values) . ");\n";
                }
                $sql .= "\n\n";
            }

            // Ghi nội dung vào file
            File::put($filePath, $sql);

            // ==========================================
            // GHI LOG: ADMIN SAO LƯU DỮ LIỆU
            // ==========================================
            ActivityLog::create([
                'type' => 'system_backup',
                'title' => 'Sao lưu dữ liệu hệ thống',
                'description' => 'Quản trị viên <strong>' . Auth::user()->name . '</strong> đã tải xuống bản sao lưu toàn bộ Database.',
                'icon_class' => 'bi-cloud-arrow-down-fill',
                'color_theme' => 'success'
            ]);

            // Trả file về cho trình duyệt tải xuống và tự động xóa file gốc trên server
            return Response::download($filePath, $fileName, [
                'Content-Type' => 'application/sql',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}