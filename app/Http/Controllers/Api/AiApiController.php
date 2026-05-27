<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\ActivityLog;
use App\Services\AiExamService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

class AiApiController extends Controller
{
    public function generate(Request $request)
    {
        try {
            $request->validate([
                'document_id' => 'required|exists:documents,id',
                'question_count' => 'required|integer|min:1|max:50',
                'exam_type' => 'nullable|string'
            ]);

            $document = Document::findOrFail($request->document_id);
            $aiService = new AiExamService();
            $fullPath = public_path($document->file_path); 
            
            if (!file_exists($fullPath)) return response()->json(['success' => false, 'message' => 'Lỗi: Không tìm thấy file tài liệu.'], 404);

            $textContent = $aiService->extractTextFromFile($fullPath);
            if (empty(trim($textContent))) return response()->json(['success' => false, 'message' => 'Lỗi: Không đọc được nội dung chữ.'], 400);

            $examType = $request->input('exam_type', 'multiple_choice');
            
            // Lấy cả mảng dữ liệu (bao gồm questions và tokens)
            $aiResult = $aiService->generateQuestionsFromText($textContent, $request->question_count, 'medium', $examType);
            
            // Tách dữ liệu ra
            $questions = $aiResult['questions'];
            $tokensUsed = $aiResult['tokens'];

            if (!$questions || count($questions) == 0) return response()->json(['success' => false, 'message' => 'Lỗi: AI không thể tạo câu hỏi.'], 500);

            // ==========================================
            // GHI LOG CHO CẢ DASHBOARD VÀ LỊCH SỬ AI
            // ==========================================
            if (Auth::check()) {
                $roleName = Auth::user()->role == 'student' ? 'Học viên' : 'Giảng viên';
                $examTypeName = $examType == 'essay' ? 'Tự luận' : 'Trắc nghiệm';
                $actionName = 'Tạo ' . $request->question_count . ' câu ' . $examTypeName;
                
                // 1. Ghi log ra Dashboard
                ActivityLog::create([
                    'type' => 'ai_generated',
                    'title' => 'Sử dụng AI tạo câu hỏi',
                    'description' => $roleName . ' <strong>' . Auth::user()->name . '</strong> vừa dùng AI tạo ' . $request->question_count . ' câu hỏi ' . $examTypeName . '.',
                    'icon_class' => 'bi-robot',
                    'color_theme' => 'warning' 
                ]);

                // 2. Ghi log số Token, Chi phí vào trang AI History
                DB::table('ai_logs')->insert([
                    'user_id' => Auth::id(),
                    'action' => $actionName,
                    'model' => 'gemini-pro',
                    'tokens' => $tokensUsed,
                    'status' => 'success',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['success' => true, 'data' => $questions]);

        } catch (\Exception $e) {
            // Ghi log thất bại nếu AI bị lỗi
            if (Auth::check()) {
                DB::table('ai_logs')->insert([
                    'user_id' => Auth::id(),
                    'action' => 'Lỗi tạo câu hỏi',
                    'model' => 'gemini-pro',
                    'tokens' => 0,
                    'status' => 'failed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'CRASH CODE: ' . $e->getMessage() . ' (Dòng ' . $e->getLine() . ')'
            ], 500);
        }
    }
}