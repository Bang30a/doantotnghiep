<?php
use App\Services\AiExamService;
use Illuminate\Http\Request;
Route::post('/test-ai', function (Request $request, AiExamService $aiService) {
    $request->validate(['document' => 'required|mimes:pdf|max:5120']);
    $filePath = $request->file('document')->path();
    $text = $aiService->extractTextFromPdf($filePath);
    
    if (empty(trim($text))) {
        return "Không đọc được chữ nào từ file PDF này. File có thể là ảnh scan.";
    }

    $questions = $aiService->generateQuestionsFromText($text, 3, 'Trung bình');
    return response()->json([
        'trang_thai' => 'Thành công',
        'so_luong' => count($questions ?? []),
        'du_lieu_tu_ai' => $questions
    ], 200, ['Content-Type' => 'application/json;charset=UTF-8'], JSON_UNESCAPED_UNICODE);
})->name('test.ai.submit');