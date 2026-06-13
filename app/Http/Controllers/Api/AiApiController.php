<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\ActivityLog;
use App\Services\AiExamService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AiApiController extends Controller
{
    public function generate(Request $request)
    {
        try {
            @set_time_limit(600);

            $request->validate([
                'document_id' => 'required|exists:documents,id',
                'question_count' => 'required|integer|min:1|max:50',
                'exam_type' => 'nullable|string',
                'exclude_questions' => 'nullable|array',
                'exclude_questions.*' => 'nullable|string|max:1000',
            ]);

            $document = Document::findOrFail($request->document_id);
            $aiService = new AiExamService();
            $fullPath = public_path($document->file_path); 
            
            if (!file_exists($fullPath)) return response()->json(['success' => false, 'message' => 'Lỗi: Không tìm thấy file tài liệu.'], 404);

            $textContent = $aiService->extractTextFromFile($fullPath);
            if (empty(trim($textContent))) return response()->json(['success' => false, 'message' => 'Lỗi: Không đọc được nội dung chữ.'], 400);

            $examType = $request->input('exam_type', 'multiple_choice');
            $questionCount = (int) $request->question_count;
            $chunkSize = $examType === 'essay' ? 5 : 10;
            $excludedQuestions = array_values(array_filter(array_map('trim', (array) $request->input('exclude_questions', []))));
            $seenQuestionKeys = [];

            foreach ($excludedQuestions as $excludedQuestion) {
                $key = $this->normalizeQuestionContent($excludedQuestion);

                if ($key !== '') {
                    $seenQuestionKeys[$key] = true;
                }
            }

            $questions = [];
            $tokensUsed = 0;
            $attempts = 0;
            $maxAttempts = (int) ceil($questionCount / $chunkSize) + 5;

            while (count($questions) < $questionCount && $attempts < $maxAttempts) {
                $attempts++;
                $currentBatchSize = min($chunkSize, $questionCount - count($questions));
                $excludedForPrompt = array_merge($excludedQuestions, array_column($questions, 'content'));

                $aiResult = $aiService->generateQuestionsFromText($textContent, $currentBatchSize, 'medium', $examType, $excludedForPrompt);

                $batchQuestions = $aiResult['questions'] ?? [];
                $tokensUsed += (int) ($aiResult['tokens'] ?? 0);

                foreach ($batchQuestions as $question) {
                    if (count($questions) >= $questionCount) {
                        break 2;
                    }

                    $key = $this->normalizeQuestionContent($question['content'] ?? '');

                    if ($key === '' || isset($seenQuestionKeys[$key])) {
                        continue;
                    }

                    $seenQuestionKeys[$key] = true;
                    $questions[] = $question;
                }
            }

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

        } catch (Throwable $e) {
            Log::error('AI question generation failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Ghi log thất bại nếu AI bị lỗi
            try {
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
            } catch (Throwable $logError) {
                Log::warning('Cannot write failed AI log', [
                    'message' => $logError->getMessage(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không thể sinh câu hỏi lúc này: ' . $e->getMessage()
            ], 500);
        }
    }

    private function normalizeQuestionContent($content): string
    {
        $content = html_entity_decode(strip_tags((string) $content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $content = Str::ascii($content);
        $content = mb_strtolower($content);
        $content = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $content);
        $content = preg_replace('/\s+/u', ' ', $content);

        return trim($content ?? '');
    }
}
