<?php

namespace App\Services; 

use Spatie\PdfToText\Pdf;
use PhpOffice\PhpWord\IOFactory; 
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Schema;

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
public function generateQuestionsFromText($text, $questionCount = 5, $difficulty = 'medium', $examType = 'multiple_choice', array $excludedQuestions = [])
{
    $apiKey = config('services.gemini.api_key');

    if (!$apiKey) {
        throw new \Exception('Chưa cấu hình GEMINI_API_KEY trong file .env.');
    }

    $shortText = mb_substr($text, 0, 15000);
    $duplicateAvoidanceRule = $this->buildDuplicateAvoidanceRule($excludedQuestions);

   // ==========================================
// BƯỚC 1: LẤY PROMPT TỪ DATABASE
// ==========================================
$activePrompt = $this->getActivePromptForExamType($examType);

if ($activePrompt) {
    $customPrompt = $activePrompt->prompt_text;

    $customPrompt = str_replace('[TOTAL_QUESTIONS]', $questionCount, $customPrompt);
    $customPrompt = str_replace('[TOPIC]', 'Nội dung tài liệu đính kèm', $customPrompt);

    // Thêm luật viết câu hỏi tự nhiên, tránh câu "Trong tài liệu..."
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
CÁC QUY TẮC DƯỚI ĐÂY ƯU TIÊN CAO NHẤT. Nếu prompt trong database yêu cầu bareme dài, bài mẫu hoặc nhiều dòng thì bỏ qua phần đó.

QUY TẮC RIÊNG CHO CÂU HỎI TỰ LUẬN:
- Câu hỏi phải tự nhiên, rõ yêu cầu trả lời.
- Không bắt đầu bằng \"Trong tài liệu\", \"Theo tài liệu\", \"Dựa vào tài liệu\".
- Gợi ý đáp án chỉ được là 3 - 5 từ khóa/ý chính ngắn gọn để định hướng.
- Không viết bài mẫu, không chia thang điểm, không giải thích dài trong gợi ý.
- Mỗi ý tối đa 8 - 12 từ, các ý cách nhau bằng dấu chấm phẩy.

Cấu trúc JSON:
[
  {
    \"content\": \"Nội dung câu hỏi tự luận tự nhiên?\",
    \"answers\": [
      {
        \"content\": \"Từ khóa/ý chính: ý chính 1; ý chính 2; ý chính 3.\",
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
- answers chỉ cần 1 phần tử chứa gợi ý đáp án ngắn.
- Câu hỏi phải tự nhiên, rõ ý, giống câu hỏi trong đề kiểm tra thật.
- KHÔNG mở đầu câu hỏi bằng: \"Trong tài liệu\", \"Theo tài liệu\", \"Dựa vào tài liệu\", \"Văn bản cho biết\", \"Tài liệu đề cập\".
- Gợi ý đáp án chỉ được là 3 - 5 từ khóa/ý chính ngắn gọn để định hướng.
- Không viết bài mẫu, không chia thang điểm, không giải thích dài trong gợi ý.
- Mỗi ý tối đa 8 - 12 từ, các ý cách nhau bằng dấu chấm phẩy.
- Giải thích AI ngắn gọn, không nhắc \"trong tài liệu\".

Cấu trúc JSON:
[
  {
    \"content\": \"Nội dung câu hỏi tự luận tự nhiên?\",
    \"answers\": [
      {
        \"content\": \"Từ khóa/ý chính: ý chính 1; ý chính 2; ý chính 3.\",
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

if ($duplicateAvoidanceRule !== '') {
    $prompt = str_replace(
        '--- NỘI DUNG THAM KHẢO ---',
        $duplicateAvoidanceRule . "\n\n--- NỘI DUNG THAM KHẢO ---",
        $prompt
    );
}

    // ==========================================
    // BƯỚC 2: GỌI GEMINI CÓ RETRY VÀ FALLBACK MODEL
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

                $lastError = 'Model ' . $model . ' lỗi HTTP ' . $response->status() . ': ' . $response->body();

                Log::warning('Gemini generate failed', [
                    'model' => $model,
                    'attempt' => $attempt,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                // Lỗi tạm thời thì thử lại cùng model
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
    // BƯỚC 3: XỬ LÝ NẾU TẤT CẢ MODEL ĐỀU LỖI
    // ==========================================
    if (!$response || !$response->successful()) {
        if (
            str_contains($lastError ?? '', '503') ||
            str_contains($lastError ?? '', 'UNAVAILABLE') ||
            str_contains($lastError ?? '', 'high demand')
        ) {
            throw new \Exception('Gemini đang quá tải, vui lòng thử lại sau vài giây.');
        }

        if (
            str_contains($lastError ?? '', '429') ||
            str_contains($lastError ?? '', 'quota')
        ) {
            throw new \Exception('Bạn đã vượt giới hạn gọi AI, vui lòng thử lại sau ít phút.');
        }

        throw new \Exception($lastError ?: 'Không thể gọi Gemini API.');
    }

    // ==========================================
    // BƯỚC 4: LẤY NỘI DUNG AI TRẢ VỀ
    // Đây là đoạn đang xử lý phần nội dung trả về từ Gemini
    // ==========================================
    $result = $response->json();

    $jsonString = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (empty($jsonString)) {
        Log::error('Gemini returned empty content', [
            'model' => $usedModel,
            'response' => $result,
        ]);

        throw new \Exception('AI không trả về nội dung câu hỏi.');
    }

    $jsonString = trim($jsonString);
    $jsonString = str_replace(['```json', '```'], '', $jsonString);
    $jsonString = trim($jsonString);

    // ==========================================
    // BƯỚC 5: CẮT LẤY MẢNG JSON
    // ==========================================
    $start = strpos($jsonString, '[');
    $end = strrpos($jsonString, ']');

    if ($start === false || $end === false) {
        throw new \Exception("AI không trả về mảng JSON. Kết quả: " . $jsonString);
    }

    $cleanJson = substr($jsonString, $start, $end - $start + 1);
    $data = json_decode($cleanJson, true);

    // ==========================================
    // BƯỚC 6: KIỂM TRA JSON
    // ==========================================
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception(
            "Lỗi giải mã JSON: " . json_last_error_msg()
            . "<br><br><b>DỮ LIỆU LỖI TỪ AI:</b><br>"
            . "<div style='text-align:left; background:#fff; padding:10px; border:1px solid #ccc; font-size:12px; font-family:monospace;'>"
            . htmlspecialchars($cleanJson)
            . "</div>"
        );
    }

    if (!is_array($data)) {
        throw new \Exception('Dữ liệu AI trả về không phải mảng câu hỏi.');
    }

    // ==========================================
    // BƯỚC 7: CHUẨN HÓA DỮ LIỆU ĐỂ KHÔNG SẬP VIEW
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
                    'content' => 'Gợi ý đáp án đang trống.',
                    'is_correct' => true,
                ];
            }

            $q['answers'][0]['is_correct'] = true;
            $q['answers'][0]['content'] = $this->normalizeEssayHint($q['answers'][0]['content'] ?? '');
            $q['ai_explanation'] = $this->normalizeShortPlainText($q['ai_explanation'], 180);
        } else {
            // Trắc nghiệm: đảm bảo có 4 đáp án
            for ($i = count($q['answers']); $i < 4; $i++) {
                $q['answers'][] = [
                    'content' => 'Đáp án ' . chr(65 + $i),
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
        throw new \Exception('AI không tạo được câu hỏi hợp lệ.');
    }

    // ==========================================
    // BƯỚC 8: TOKEN
    // ==========================================
    $tokens = $result['usageMetadata']['totalTokenCount'] ?? (strlen($prompt . $cleanJson) / 3.5);

    return [
        'questions' => $data,
        'tokens' => (int) $tokens
    ];
}

public function gradeEssayAnswer($questionContent, $bareme, $studentAnswer)
{
    $questionContent = trim((string) $questionContent);
    $bareme = trim((string) $bareme);
    $studentAnswer = trim((string) $studentAnswer);

    if ($studentAnswer === '') {
        return [
            'score' => null,
            'feedback' => 'Học viên chưa có bài làm nên AI chưa thể chấm.',
        ];
    }

    $apiKey = config('services.gemini.api_key');

    if (!$apiKey) {
        Log::warning('Gemini essay grading skipped because GEMINI_API_KEY is missing.');

        return [
            'score' => null,
            'feedback' => 'Chưa cấu hình GEMINI_API_KEY nên AI chưa thể gợi ý điểm. Giảng viên có thể chấm thủ công.',
        ];
    }

    $prompt = "Bạn là giảng viên chấm bài tự luận nghiêm túc và công bằng.
Hãy chấm bài dựa trên câu hỏi, bareme/gợi ý đáp án và bài làm của học viên.

QUY TẮC CHẤM:
- Điểm tối đa là 10, điểm tối thiểu là 0.
- Bareme/gợi ý đáp án có thể chỉ là các từ khóa hoặc ý chính ngắn, hãy chấm theo ý tương đương về mặt kiến thức.
- Chỉ cho điểm theo các ý đúng có trong câu hỏi, bareme hoặc nội dung tương đương.
- Nếu bài làm thiếu ý, sai trọng tâm hoặc trả lời lan man, hãy trừ điểm rõ ràng.
- Nhận xét ngắn gọn, chỉ ra ý đúng, ý thiếu và cách cải thiện.
- Không tự bịa kiến thức ngoài câu hỏi và bareme.

CHỈ TRẢ VỀ JSON OBJECT HỢP LỆ, KHÔNG markdown, KHÔNG giải thích ngoài JSON:
{
  \"score\": 7.5,
  \"feedback\": \"Nhận xét ngắn gọn cho giảng viên tham khảo.\"
}

CÂU HỎI:
" . mb_substr($questionContent, 0, 3000) . "

BAREME / GỢI Ý ĐÁP ÁN:
" . mb_substr($bareme, 0, 6000) . "

BÀI LÀM CỦA HỌC VIÊN:
" . mb_substr($studentAnswer, 0, 6000);

    $models = [
        'gemini-2.5-flash',
        'gemini-2.5-flash-lite',
        'gemini-2.0-flash',
        'gemini-2.0-flash-lite',
        'gemini-flash-latest',
        'gemini-flash-lite-latest',
    ];

    $lastError = null;

    foreach ($models as $model) {
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])
                    ->timeout(60)
                    ->post('https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $apiKey, [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.2,
                            'response_mime_type' => 'application/json',
                        ],
                    ]);

                if (!$response->successful()) {
                    $lastError = 'Model ' . $model . ' HTTP ' . $response->status() . ': ' . $response->body();

                    Log::warning('Gemini essay grading failed', [
                        'model' => $model,
                        'attempt' => $attempt,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    if (in_array($response->status(), [429, 500, 502, 503, 504], true)) {
                        sleep($attempt);
                        continue;
                    }

                    break;
                }

                $jsonString = trim((string) ($response->json('candidates.0.content.parts.0.text') ?? ''));
                $jsonString = trim(str_replace(['```json', '```'], '', $jsonString));

                $start = strpos($jsonString, '{');
                $end = strrpos($jsonString, '}');

                if ($start === false || $end === false) {
                    $lastError = 'Gemini essay grading returned non JSON content: ' . $jsonString;
                    break;
                }

                $data = json_decode(substr($jsonString, $start, $end - $start + 1), true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                    $lastError = 'Gemini essay grading JSON decode error: ' . json_last_error_msg();
                    break;
                }

                $score = $data['score'] ?? null;

                if (is_string($score) && preg_match('/-?\d+(?:[\\.,]\d+)?/', $score, $matches)) {
                    $score = str_replace(',', '.', $matches[0]);
                }

                $score = is_numeric($score)
                    ? round(max(0, min(10, (float) $score)), 1)
                    : null;

                $feedback = $data['feedback'] ?? '';

                if (is_array($feedback)) {
                    $feedback = implode(' ', array_filter(array_map('strval', $feedback)));
                }

                $feedback = trim((string) $feedback);

                if ($feedback === '') {
                    $feedback = $score === null
                        ? 'AI chưa đưa ra nhận xét rõ ràng. Giảng viên vui lòng chấm thủ công.'
                        : 'AI đã gợi ý điểm dựa trên bareme. Giảng viên nên rà soát lại trước khi lưu.';
                }

                return [
                    'score' => $score,
                    'feedback' => $feedback,
                ];
            } catch (\Throwable $e) {
                $lastError = 'Model ' . $model . ' exception: ' . $e->getMessage();

                Log::warning('Gemini essay grading exception', [
                    'model' => $model,
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                ]);

                sleep($attempt);
            }
        }
    }

    Log::error('Gemini essay grading unavailable', [
        'error' => $lastError,
    ]);

    return [
        'score' => null,
        'feedback' => 'AI chưa thể phân tích bài làm lúc này. Giảng viên có thể chấm thủ công hoặc thử lại sau.',
    ];
}

private function normalizeEssayHint($content): string
{
    $content = trim((string) $content);

    if ($content === '') {
        return 'Từ khóa/ý chính: chưa có gợi ý.';
    }

    $content = html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $content = str_replace(['•', '–', '—'], '-', $content);
    $content = preg_replace('/(?:^|\s)[\-+]\s+/u', '; ', $content);
    $content = preg_replace('/\b\d+(?:[\.,]\d+)?\s*(?:điểm|đ)\b/iu', '', $content);
    $content = preg_replace('/\r\n|\r|\n/u', '; ', $content);
    $content = preg_replace('/\s+/u', ' ', $content);

    $rawParts = preg_split('/\s*(?:;|\.\s+)\s*/u', $content, -1, PREG_SPLIT_NO_EMPTY);

    if (count($rawParts) < 3) {
        $commaParts = preg_split('/\s*,\s*/u', $content, -1, PREG_SPLIT_NO_EMPTY);

        if (count($commaParts) > count($rawParts)) {
            $rawParts = $commaParts;
        }
    }

    $items = [];
    $seen = [];

    foreach ($rawParts as $part) {
        $part = trim($part);
        $part = preg_replace('/^(?:gợi ý đáp án|bareme|đáp án|ý chính|từ khóa\/?ý chính|từ khóa|nội dung cần đạt)\s*[:\-]\s*/iu', '', $part);
        $part = preg_replace('/^\(?[0-9ivx]+\)?[\.\)]\s*/iu', '', $part);
        $part = trim($part, " \t\n\r\0\x0B.,;:-");

        if ($part === '') {
            continue;
        }

        $part = $this->normalizeShortPlainText($part, 60);
        $key = mb_strtolower(preg_replace('/[^\p{L}\p{N}]+/u', ' ', $part));

        if ($key === '' || isset($seen[$key])) {
            continue;
        }

        $seen[$key] = true;
        $items[] = $part;

        if (count($items) >= 5) {
            break;
        }
    }

    if (count($items) === 0) {
        $items[] = $this->normalizeShortPlainText($content, 60);
    }

    return 'Từ khóa/ý chính: ' . implode('; ', $items) . '.';
}

private function normalizeShortPlainText($content, int $limit): string
{
    $content = html_entity_decode(strip_tags((string) $content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $content = trim(preg_replace('/\s+/u', ' ', $content));

    if ($content === '' || mb_strlen($content) <= $limit) {
        return $content;
    }

    $cut = mb_substr($content, 0, $limit);
    $lastSpace = mb_strrpos($cut, ' ');

    if ($lastSpace !== false && $lastSpace > (int) ($limit * 0.55)) {
        $cut = mb_substr($cut, 0, $lastSpace);
    }

    return rtrim($cut, " \t\n\r\0\x0B.,;:-") . '...';
}

private function buildDuplicateAvoidanceRule(array $excludedQuestions): string
{
    $excludedQuestions = array_values(array_filter(array_map(function ($content) {
        $content = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $content)));
        return mb_substr($content, 0, 220);
    }, $excludedQuestions)));

    if (count($excludedQuestions) === 0) {
        return '';
    }

    $excludedQuestions = array_slice($excludedQuestions, -80);

    $lines = array_map(function ($content, $index) {
        return ($index + 1) . '. ' . $content;
    }, $excludedQuestions, array_keys($excludedQuestions));

    return "QUY TẮC CHỐNG TRÙNG LẶP BẮT BUỘC:
- Tuyệt đối không tạo câu hỏi trùng hoặc gần trùng ý với các câu đã có bên dưới.
- Không chỉ đổi từ ngữ để hỏi lại cùng một kiến thức, sự kiện, nguyên nhân, kết quả hoặc đáp án.
- Nếu tài liệu không đủ ý mới, hãy khai thác góc hỏi khác thay vì lặp lại câu cũ.

DANH SÁCH CÂU HỎI ĐÃ CÓ, KHÔNG ĐƯỢC TẠO LẠI:
" . implode("\n", $lines);
}

private function getActivePromptForExamType(string $examType)
{
    if (!Schema::hasTable('prompts')) {
        return null;
    }

    $query = DB::table('prompts')
        ->where(function ($query) {
            $query->where('status', 1)
                ->orWhere('status', 'active');
        });

    if (Schema::hasColumn('prompts', 'exam_type')) {
        return $query
            ->whereIn('exam_type', [$examType, 'both'])
            ->orderByRaw(
                "CASE
                    WHEN exam_type = ? THEN 0
                    WHEN exam_type = 'both' THEN 1
                    ELSE 2
                END",
                [$examType]
            )
            ->latest()
            ->first();
    }

    return $query->latest()->first();
}
}
