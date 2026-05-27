<?php

namespace App\Services; 

use Spatie\PdfToText\Pdf;
use PhpOffice\PhpWord\IOFactory; 
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 

class AiExamService
{
    /**
     * Hàm 1: Đọc nội dung file từ nhiều định dạng (PDF, DOCX, DOC, TXT)
     */
    public function extractTextFromFile($filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $text = "";

        try {
            switch ($extension) {
                case 'pdf':
                    $text = Pdf::getText($filePath, '/usr/bin/pdftotext');
                    break;

                case 'docx':
                    $zip = new \ZipArchive;
                    if ($zip->open($filePath) === true) {
                        if (($index = $zip->locateName('word/document.xml')) !== false) {
                            $data = $zip->getFromIndex($index);
                            $zip->close();
                            $data = str_replace('</w:p>', "\n", $data);
                            $text = strip_tags($data);
                        } else {
                            $zip->close();
                        }
                    } else {
                        throw new \Exception("Không thể giải nén file DOCX. File có thể bị hỏng.");
                    }
                    break;

                case 'doc':
                    // --- DÙNG CỖ MÁY XÚC PHPWORD ĐỂ ĐỌC FILE DOC ---
                    try {
                        $phpWord = IOFactory::load($filePath, 'MsDoc');
                        foreach ($phpWord->getSections() as $section) {
                            foreach ($section->getElements() as $element) {
                                if (method_exists($element, 'getText')) {
                                    $text .= $element->getText() . " \n";
                                } elseif (method_exists($element, 'getElements')) {
                                    foreach ($element->getElements() as $child) {
                                        if (method_exists($child, 'getText')) {
                                            $text .= $child->getText() . " ";
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error("Lỗi PhpWord khi đọc DOC: " . $e->getMessage());
                        // Cứu cánh cuối cùng nếu PhpWord cũng gục: Ép đọc bỏ rác
                        $content = file_get_contents($filePath);
                        $text = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', ' ', $content);
                        $text = strip_tags($text);
                    }
                    break;

                case 'txt':
                    $text = file_get_contents($filePath);
                    break;
                
                default:
                    throw new \Exception("Định dạng file .{$extension} không được hỗ trợ.");
            }
        } catch (\Exception $e) {
            Log::error("Lỗi tổng đọc file: " . $e->getMessage());
            return "";
        }

        return trim($text);
    }

    /**
     * Hàm 2: Gửi văn bản lên Gemini AI và yêu cầu trả về JSON
     */
   /**
 * Hàm 2: Gửi văn bản lên Gemini AI và yêu cầu trả về JSON
 */
public function generateQuestionsFromText($text, $questionCount = 5, $difficulty = 'medium', $examType = 'multiple_choice')
{
    $apiKey = env('GEMINI_API_KEY');

    if (!$apiKey) {
        throw new \Exception('Chua cau hinh GEMINI_API_KEY trong file .env.');
    }

    $shortText = mb_substr($text, 0, 15000);

   // ==========================================
// BUOC 1: LAY PROMPT TU DATABASE
// ==========================================
$activePrompt = DB::table('prompts')->where('status', 1)->latest()->first();

if ($activePrompt) {
    $customPrompt = $activePrompt->prompt_text;

    $customPrompt = str_replace('[TOTAL_QUESTIONS]', $questionCount, $customPrompt);
    $customPrompt = str_replace('[TOPIC]', 'Nội dung tài liệu đính kèm', $customPrompt);

    // Them luat viet cau hoi tu nhien, tranh cau "Trong tai lieu..."
    $naturalQuestionRule = "

QUY TẮC VIẾT CÂU HỎI TỰ NHIÊN BẮT BUỘC:
- Câu hỏi phải giống câu hỏi trong đề kiểm tra thật, ngắn gọn, rõ ý.
- KHÔNG mở đầu câu hỏi bằng các cụm: \"Trong tài liệu\", \"Theo tài liệu\", \"Dựa vào tài liệu\", \"Văn bản cho biết\", \"Tài liệu đề cập\".
- KHÔNG viết câu hỏi kiểu máy móc như: \"Tài liệu nói gì về...\", \"Nội dung nào được nêu trong tài liệu...\".
- Hãy hỏi trực tiếp vào kiến thức, khái niệm, sự kiện, đặc điểm, ý nghĩa, nguyên nhân, kết quả.
- Phần giải thích AI cũng KHÔNG được bắt đầu bằng: \"Trong tài liệu\", \"Theo tài liệu\", \"Dựa vào tài liệu\".
- Giải thích chỉ cần nói trực tiếp vì sao đáp án đúng, không cần nhắc lại rằng thông tin lấy từ tài liệu.
";

    if ($examType === 'essay') {
        $jsonRule = "\n\nBẮT BUỘC TRẢ VỀ DUY NHẤT MỘT MẢNG JSON.
BẮT BUỘC tạo đúng {$questionCount} câu hỏi TỰ LUẬN.
KHÔNG markdown, KHÔNG ```json, KHÔNG chữ ngoài JSON.

QUY TẮC RIÊNG CHO CÂU HỎI TỰ LUẬN:
- Câu hỏi phải tự nhiên, rõ yêu cầu trả lời.
- Không bắt đầu bằng \"Trong tài liệu\", \"Theo tài liệu\", \"Dựa vào tài liệu\".
- Gợi ý đáp án phải có các ý chính để chấm điểm.

Cấu trúc JSON:
[
  {
    \"content\": \"Nội dung câu hỏi tự luận tự nhiên?\",
    \"answers\": [
      {
        \"content\": \"Gợi ý đáp án / bareme chấm điểm\",
        \"is_correct\": true
      }
    ],
    \"ai_explanation\": \"Giải thích ngắn gọn, không nhắc 'trong tài liệu'.\"
  }
]";
    } else {
        $jsonRule = "\n\nBẮT BUỘC TRẢ VỀ DUY NHẤT MỘT MẢNG JSON.
BẮT BUỘC tạo đúng {$questionCount} câu hỏi TRẮC NGHIỆM.
Mỗi câu phải có đúng 4 đáp án A/B/C/D.
Chỉ có 1 đáp án đúng is_correct = true.
KHÔNG markdown, KHÔNG ```json, KHÔNG chữ ngoài JSON.

QUY TẮC RIÊNG CHO TRẮC NGHIỆM:
- Câu hỏi phải tự nhiên, không nhắc \"trong tài liệu\".
- 4 đáp án phải ngắn gọn, độ dài tương đương nhau.
- Mỗi đáp án tối đa khoảng 15 - 30 từ.
- Không dùng đáp án kiểu \"Tất cả các ý trên đều đúng\", \"Cả A và B\", \"Cả ba đáp án trên\".
- Đáp án nhiễu phải hợp lý, dễ gây nhầm lẫn nhưng không được vô lý.
- Không dùng ký tự gạch đầu dòng hoặc xuống dòng trong nội dung đáp án.

Cấu trúc JSON:
[
  {
    \"content\": \"Nội dung câu hỏi tự nhiên, không nhắc 'trong tài liệu'\",
    \"answers\": [
      { \"content\": \"Đáp án A\", \"is_correct\": true },
      { \"content\": \"Đáp án B\", \"is_correct\": false },
      { \"content\": \"Đáp án C\", \"is_correct\": false },
      { \"content\": \"Đáp án D\", \"is_correct\": false }
    ],
    \"ai_explanation\": \"Giải thích ngắn gọn vì sao đáp án đúng, không nhắc 'trong tài liệu'.\"
  }
]";
    }

    $prompt = $customPrompt
        . $naturalQuestionRule
        . $jsonRule
        . "\n\n--- NỘI DUNG THAM KHẢO ---\n"
        . $shortText;

} else {
    if ($examType === 'essay') {
        $prompt = "Bạn là một giáo viên chấm thi và biên soạn đề tự luận.

Hãy đọc nội dung tham khảo bên dưới và tạo đúng {$questionCount} câu hỏi TỰ LUẬN mức độ {$difficulty}.

YÊU CẦU:
- Trả về duy nhất một mảng JSON.
- Không markdown.
- Không ```json.
- Không thêm chữ nào ngoài JSON.
- Mỗi câu hỏi phải có content, answers, ai_explanation.
- answers chỉ cần 1 phần tử chứa gợi ý đáp án / bareme.
- Câu hỏi phải tự nhiên, rõ ý, giống câu hỏi trong đề kiểm tra thật.
- KHÔNG mở đầu câu hỏi bằng: \"Trong tài liệu\", \"Theo tài liệu\", \"Dựa vào tài liệu\", \"Văn bản cho biết\", \"Tài liệu đề cập\".
- Gợi ý đáp án phải có các ý chính để chấm điểm.
- Giải thích AI ngắn gọn, không nhắc \"trong tài liệu\".

Cấu trúc JSON:
[
  {
    \"content\": \"Nội dung câu hỏi tự luận tự nhiên?\",
    \"answers\": [
      {
        \"content\": \"Gợi ý đáp án hoặc bareme chấm điểm\",
        \"is_correct\": true
      }
    ],
    \"ai_explanation\": \"Giải thích ngắn gọn, không nhắc 'trong tài liệu'.\"
  }
]

--- NỘI DUNG THAM KHẢO ---
" . $shortText;

    } else {
        $prompt = "Bạn là giáo viên và chuyên gia biên soạn đề thi trắc nghiệm.

Hãy đọc nội dung tham khảo bên dưới và tạo đúng {$questionCount} câu hỏi TRẮC NGHIỆM mức độ {$difficulty}.

YÊU CẦU:
- Trả về duy nhất một mảng JSON.
- Không markdown.
- Không ```json.
- Không thêm chữ nào ngoài JSON.
- Mỗi câu có đúng 4 đáp án.
- Chỉ có 1 đáp án đúng.
- Câu hỏi phải tự nhiên, ngắn gọn, rõ ý, giống câu hỏi trong đề kiểm tra thật.
- KHÔNG mở đầu câu hỏi bằng: \"Trong tài liệu\", \"Theo tài liệu\", \"Dựa vào tài liệu\", \"Văn bản cho biết\", \"Tài liệu đề cập\".
- KHÔNG viết câu hỏi kiểu: \"Tài liệu nói gì về...\", \"Nội dung nào được nêu trong tài liệu...\".
- Hãy hỏi trực tiếp vào kiến thức, khái niệm, sự kiện, đặc điểm, ý nghĩa, nguyên nhân, kết quả.
- 4 đáp án phải ngắn gọn, độ dài tương đương nhau.
- Mỗi đáp án tối đa khoảng 15 - 30 từ.
- Không dùng đáp án kiểu \"Tất cả các ý trên đều đúng\", \"Cả A và B\", \"Cả ba đáp án trên\".
- Đáp án nhiễu phải hợp lý, dễ gây nhầm lẫn nhưng không được vô lý.
- Không dùng ký tự gạch đầu dòng hoặc xuống dòng trong nội dung đáp án.
- Giải thích AI chỉ 1 - 2 câu, nói trực tiếp vì sao đáp án đúng.
- Giải thích AI KHÔNG được bắt đầu bằng: \"Trong tài liệu\", \"Theo tài liệu\", \"Dựa vào tài liệu\".

Cấu trúc JSON:
[
  {
    \"content\": \"Nội dung câu hỏi tự nhiên, không nhắc 'trong tài liệu'\",
    \"answers\": [
      { \"content\": \"Đáp án A\", \"is_correct\": true },
      { \"content\": \"Đáp án B\", \"is_correct\": false },
      { \"content\": \"Đáp án C\", \"is_correct\": false },
      { \"content\": \"Đáp án D\", \"is_correct\": false }
    ],
    \"ai_explanation\": \"Giải thích ngắn gọn vì sao đáp án đúng, không nhắc 'trong tài liệu'.\"
  }
]

--- NỘI DUNG THAM KHẢO ---
" . $shortText;
    }
}

    // ==========================================
    // BUOC 2: GOI GEMINI CO RETRY + FALLBACK MODEL
    // ==========================================
    $models = [
        'gemini-2.5-flash',
        'gemini-2.5-flash-lite',
        'gemini-2.0-flash',
        'gemini-2.0-flash-lite',
        'gemini-flash-latest',
        'gemini-flash-lite-latest',
    ];

    $response = null;
    $lastError = null;
    $usedModel = null;

    foreach ($models as $model) {
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->timeout(120)
                ->post('https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'response_mime_type' => 'application/json'
                    ]
                ]);

                if ($response->successful()) {
                    $usedModel = $model;
                    break 2;
                }

                $lastError = 'Model ' . $model . ' loi HTTP ' . $response->status() . ': ' . $response->body();

                Log::warning('Gemini generate failed', [
                    'model' => $model,
                    'attempt' => $attempt,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                // Loi tam thoi thi thu lai cung model
                if (in_array($response->status(), [429, 500, 502, 503, 504])) {
                    sleep($attempt * 2);
                    continue;
                }

                break;
            } catch (\Exception $e) {
                $lastError = 'Model ' . $model . ' exception: ' . $e->getMessage();

                Log::warning('Gemini generate exception', [
                    'model' => $model,
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                ]);

                sleep($attempt * 2);
            }
        }
    }

    // ==========================================
    // BUOC 3: XU LY NEU TAT CA MODEL DEU LOI
    // ==========================================
    if (!$response || !$response->successful()) {
        if (
            str_contains($lastError ?? '', '503') ||
            str_contains($lastError ?? '', 'UNAVAILABLE') ||
            str_contains($lastError ?? '', 'high demand')
        ) {
            throw new \Exception('Gemini dang qua tai, vui long thu lai sau vai giay.');
        }

        if (
            str_contains($lastError ?? '', '429') ||
            str_contains($lastError ?? '', 'quota')
        ) {
            throw new \Exception('Ban da vuot gioi han goi AI, vui long thu lai sau it phut.');
        }

        throw new \Exception($lastError ?: 'Khong the goi Gemini API.');
    }

    // ==========================================
    // BUOC 4: LAY NOI DUNG AI TRA VE
    // DAY LA DOAN BAC DANG BI THIEU
    // ==========================================
    $result = $response->json();

    $jsonString = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (empty($jsonString)) {
        Log::error('Gemini returned empty content', [
            'model' => $usedModel,
            'response' => $result,
        ]);

        throw new \Exception('AI khong tra ve noi dung cau hoi.');
    }

    $jsonString = trim($jsonString);
    $jsonString = str_replace(['```json', '```'], '', $jsonString);
    $jsonString = trim($jsonString);

    // ==========================================
    // BUOC 5: CAT LAY MANG JSON
    // ==========================================
    $start = strpos($jsonString, '[');
    $end = strrpos($jsonString, ']');

    if ($start === false || $end === false) {
        throw new \Exception("AI khong tra ve mang JSON. Ket qua: " . $jsonString);
    }

    $cleanJson = substr($jsonString, $start, $end - $start + 1);
    $data = json_decode($cleanJson, true);

    // ==========================================
    // BUOC 6: KIEM TRA JSON
    // ==========================================
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception(
            "Loi giai ma JSON: " . json_last_error_msg()
            . "<br><br><b>DU LIEU LOI TU AI:</b><br>"
            . "<div style='text-align:left; background:#fff; padding:10px; border:1px solid #ccc; font-size:12px; font-family:monospace;'>"
            . htmlspecialchars($cleanJson)
            . "</div>"
        );
    }

    if (!is_array($data)) {
        throw new \Exception('Du lieu AI tra ve khong phai mang cau hoi.');
    }

    // ==========================================
    // BUOC 7: CHUAN HOA DU LIEU DE KHONG SAP VIEW
    // ==========================================
    $data = array_values(array_filter($data, function ($q) {
        return is_array($q) && !empty($q['content']);
    }));

    foreach ($data as &$q) {
        $q['type'] = $examType;
        $q['content'] = $q['content'] ?? '';
        $q['ai_explanation'] = $q['ai_explanation'] ?? '';

        if (!isset($q['answers']) || !is_array($q['answers'])) {
            $q['answers'] = [];
        }

        if ($examType === 'essay') {
            if (count($q['answers']) === 0) {
                $q['answers'][] = [
                    'content' => 'Goi y dap an dang trong.',
                    'is_correct' => true,
                ];
            }

            $q['answers'][0]['is_correct'] = true;
        } else {
            // Trac nghiem: dam bao co 4 dap an
            for ($i = count($q['answers']); $i < 4; $i++) {
                $q['answers'][] = [
                    'content' => 'Dap an ' . chr(65 + $i),
                    'is_correct' => false,
                ];
            }

            $q['answers'] = array_slice($q['answers'], 0, 4);

            $hasCorrect = false;

            foreach ($q['answers'] as &$ans) {
                $ans['content'] = $ans['content'] ?? '';
                $ans['is_correct'] = filter_var($ans['is_correct'] ?? false, FILTER_VALIDATE_BOOLEAN);

                if ($ans['is_correct']) {
                    $hasCorrect = true;
                }
            }
            unset($ans);

            if (!$hasCorrect) {
                $q['answers'][0]['is_correct'] = true;
            }
        }
    }
    unset($q);

    if (count($data) === 0) {
        throw new \Exception('AI khong tao duoc cau hoi hop le.');
    }

    // ==========================================
    // BUOC 8: TOKEN
    // ==========================================
    $tokens = $result['usageMetadata']['totalTokenCount'] ?? (strlen($prompt . $cleanJson) / 3.5);

    return [
        'questions' => $data,
        'tokens' => (int) $tokens
    ];
}
}