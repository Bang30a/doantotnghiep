# Ôn tập vấn đáp đồ án EduQuiz AI
## 1. Câu giới thiệu 60 giây
Đề tài của em là **EduQuiz AI**, một hệ thống web hỗ trợ tạo đề thi và ôn tập bằng AI cho môi trường lớp học. Hệ thống có 3 nhóm người dùng chính: quản trị viên, giảng viên và học viên.
Giảng viên có thể tải tài liệu lên, dùng AI để sinh câu hỏi trắc nghiệm hoặc tự luận, tạo đề thi, giao đề cho lớp, theo dõi kết quả và chấm điểm tự luận với gợi ý từ AI. Học viên có thể tham gia lớp bằng mã lớp, làm bài được giao, xem kết quả, thống kê học tập và tự tạo đề ôn luyện từ tài liệu của mình. Quản trị viên quản lý người dùng, lớp học, đề thi, tài liệu, prompt AI, lịch sử sử dụng AI và cấu hình hệ thống.
Về kỹ thuật, hệ thống được xây dựng bằng Laravel theo mô hình MVC, dùng Eloquent ORM để quản lý dữ liệu, Blade/Vite cho giao diện, và tích hợp Gemini API để sinh câu hỏi, gợi ý chấm tự luận. Dữ liệu chính gồm `users`, `classrooms`, `documents`, `exams`, `questions`, `answers`, `results`, `student_answers`, `prompts`, `ai_logs` và `activity_logs`.
## 2. Bài toán là gì và cách giải bài toán
### Bài toán của đồ án
Bài toán em đặt ra là: trong quá trình dạy và học, giảng viên mất nhiều thời gian để biên soạn đề kiểm tra từ tài liệu, học viên thì thiếu công cụ tự tạo bài ôn tập phù hợp với nội dung mình đang học, còn nhà quản trị cần theo dõi người dùng, tài liệu, đề thi và lịch sử sử dụng AI trong hệ thống.

Vì vậy, bài toán không chỉ là “làm một website thi trắc nghiệm”, mà là xây dựng một hệ thống hỗ trợ **tạo đề, giao bài, làm bài, chấm điểm, thống kê và quản lý học tập**, trong đó AI được dùng để giảm thời gian tạo câu hỏi và hỗ trợ chấm tự luận.
### Cách em giải bài toán
Em giải bài toán bằng cách xây dựng hệ thống EduQuiz AI với 3 vai trò chính:
- **Giảng viên** upload tài liệu, dùng AI để sinh câu hỏi trắc nghiệm hoặc tự luận, tạo đề thi, giao bài cho lớp, xem kết quả và chấm bài.
- **Học viên** tham gia lớp, làm bài được giao, xem kết quả, thống kê học tập và tự tạo đề ôn luyện từ tài liệu cá nhân.
- **Admin** quản lý người dùng, lớp học, tài liệu, đề thi, prompt AI, lịch sử AI và hoạt động hệ thống.
Về kỹ thuật, em dùng Laravel theo mô hình MVC để tổ chức hệ thống. Tài liệu sau khi upload sẽ được trích xuất nội dung, gửi sang Gemini API để sinh câu hỏi theo định dạng JSON. Sau đó hệ thống chuẩn hóa dữ liệu, cho người dùng xem/chỉnh sửa rồi mới lưu vào CSDL. Khi học viên làm bài, hệ thống tự chấm trắc nghiệm, lưu bài làm tự luận và hỗ trợ giảng viên bằng gợi ý chấm điểm từ AI.
### Câu trả lời ngắn gọn khi bị hỏi trực tiếp

> Bài toán của em là giảm thời gian tạo đề và hỗ trợ quá trình kiểm tra, ôn tập trong lớp học. Em giải bằng cách xây dựng một hệ thống web có 3 vai trò admin, giảng viên, học viên; tích hợp AI để sinh câu hỏi từ tài liệu, hỗ trợ tạo đề, giao bài, làm bài, chấm điểm, thống kê và quản lý toàn bộ hoạt động học tập.
Nếu hội đồng hỏi tiếp: **“AI ở đây giải quyết phần nào?”**, có thể trả lời:
> AI không thay thế hoàn toàn giảng viên, mà đóng vai trò hỗ trợ. AI giúp sinh câu hỏi từ tài liệu và gợi ý chấm tự luận. Giảng viên vẫn là người kiểm duyệt đề và quyết định điểm cuối cùng, nên hệ thống vừa tận dụng AI vừa giữ được tính kiểm soát trong giáo dục.
## 3. Stack công nghệ
- Backend: PHP 8.2, Laravel 12.
- Frontend: Blade template, CSS/JS riêng theo từng màn hình, Vite.
- Database: migration Laravel, hiện repo có SQLite `database/database.sqlite`, có thể cấu hình sang MySQL theo `.env`.
- AI: Google Gemini API qua HTTP client của Laravel.
- Xử lý file: `spatie/pdf-to-text` cho PDF, `phpoffice/phpword` cho DOC, tự giải nén DOCX bằng `ZipArchive`, hỗ trợ TXT.
- Xuất báo cáo: `maatwebsite/excel`.
- Đăng nhập Google: Laravel Socialite.
- Bảo mật cơ bản: Laravel Auth, CSRF, validate request, hash password, session regenerate, rich text sanitizer.
## 4. Vai trò và chức năng
### Admin
- Xem dashboard tổng quan.
- Quản lý giảng viên, học viên, khóa/mở khóa hoặc xóa tài khoản.
- Quản lý lớp học, đề thi, tài liệu.
- Quản lý prompt AI theo loại đề.
- Xem và export lịch sử AI.
- Cấu hình hệ thống, test email, clear cache, backup database.
### Giảng viên
- Tạo và quản lý lớp học.
- Upload tài liệu học tập.
- Sinh câu hỏi bằng AI từ tài liệu.
- Tạo đề thi, chỉnh sửa, xóa đề.
- Giao đề cho lớp, có hỗ trợ tạo nhiều mã đề và đảo thứ tự câu hỏi.
- Xem kết quả lớp, thống kê, báo cáo.
- Chấm bài tự luận, có gợi ý điểm và nhận xét từ AI.
### Học viên
- Đăng ký/đăng nhập, tham gia lớp bằng mã lớp.
- Xem bài kiểm tra được giao.
- Làm bài trắc nghiệm/tự luận.
- Xem kết quả, lịch sử và thống kê học tập.
- Upload tài liệu riêng và tạo đề tự luyện bằng AI.
- Quản lý kho câu hỏi/đề tự luyện của mình.
## 5. Kiến trúc tổng quan
Hệ thống đi theo mô hình MVC của Laravel:
- Route: `routes/web.php` định nghĩa các nhóm route public, auth, teacher, student, admin.
- Controller: nhận request, gọi service, trả về view/response.
- Service: xử lý nghiệp vụ chính, ví dụ `AiExamService`, `ExamManagementService`, `ExamSubmissionService`, `GradingService`.
- Model: ánh xạ các bảng CSDL và quan hệ Eloquent.
- View: Blade template trong `resources/views`, CSS/JS trong `public/css` và `public/js`.

Lý do tách service: controller gọn hơn, nghiệp vụ dễ test và dễ bảo trì hơn. Ví dụ tạo đề thi nằm trong `ExamManagementService`, nộp bài nằm trong `ExamSubmissionService`, gọi AI nằm trong `AiExamService`.
## 6. Mô hình dữ liệu cần nhớ
- `users`: người dùng, có `role` là `admin`, `teacher`, `student`, có `status`.
- `classrooms`: lớp học, có `teacher_id`, `code`, `status`.
- `classroom_user`: bảng trung gian học viên tham gia lớp.
- `documents`: tài liệu upload, có `user_id`, `file_path`, `file_type`, `file_size`, `status`.
- `exams`: đề thi, có `teacher_id`, `classroom_id`, `document_id`, `title`, `duration`, `deadline`. Các cột `source_exam_id`, `variant_group`, `variant_number`, `variant_count`, `shuffle_questions` dùng cho mã đề.
- `questions`: câu hỏi của đề, có `type` là `multiple_choice` hoặc `essay`, có `ai_explanation`.
- `answers`: đáp án của câu hỏi, với trắc nghiệm dùng `is_correct`, với tự luận có thể lưu gợi ý/bareme.
- `results`: kết quả một lần nộp bài, lưu `user_id`, `exam_id`, `score`, `total_questions`.
- `student_answers`: câu trả lời chi tiết từng câu, có `answer_id` cho trắc nghiệm, `content`, `score`, `feedback`, `ai_feedback` cho tự luận.
- `prompts`: prompt AI do admin cấu hình.
- `ai_logs`: lịch sử sử dụng AI, token, trạng thái.
- `activity_logs`: nhật ký hoạt động trên dashboard.
Quan hệ quan trọng:
- User teacher `hasMany` classrooms, exams.
- User student `belongsToMany` classrooms và `hasMany` results.
- Classroom `hasMany` exams và `belongsToMany` users.
- Exam `hasMany` questions, `hasMany` results, `belongsTo` classroom/document/teacher.
- Question `hasMany` answers.
- Result `hasMany` student_answers.
## 7. Luồng sinh câu hỏi bằng AI
1. Người dùng upload tài liệu PDF/DOC/DOCX/TXT.
2. Màn hình tạo đề gọi route AJAX `/exams/generate-ai` hoặc `/student/exams/generate-ai`.
3. `AiApiController@generate` validate `document_id`, `question_count`, `exam_type`.
4. Lấy file từ bảng `documents`, đọc nội dung bằng `AiExamService@extractTextFromFile`.
5. Nếu file không có text, trả lỗi. Trường hợp PDF scan ảnh có thể không đọc được vì chưa có OCR.
6. `AiExamService@generateQuestionsFromText` cắt nội dung tối đa 15000 ký tự, lấy prompt active từ bảng `prompts` nếu có, ngược lại dùng prompt mặc định.
7. Gọi Gemini với danh sách model fallback: `gemini-2.5-flash`, `gemini-2.5-flash-lite`, `gemini-2.0-flash`, ...
8. Yêu cầu AI trả về JSON, sau đó tách JSON, `json_decode`, chuẩn hóa câu hỏi/đáp án.
9. Chia batch để tạo đủ số câu, có cơ chế tránh trùng câu hỏi bằng `exclude_questions` và normalize nội dung.
10. Ghi `activity_logs` và `ai_logs`, trả câu hỏi về frontend.
11. Khi người dùng bấm lưu đề, `ExamManagementService` hoặc `SelfPracticeService` tạo exam, questions, answers trong transaction.
Câu trả lời ngắn khi bị hỏi:
> Hệ thống không lưu ngay kết quả AI vào CSDL khi vừa generate. AI trả về danh sách câu hỏi để người dùng xem/chỉnh sửa, sau đó khi bấm lưu mới tạo exam, question và answer. Cách này giúp giảng viên kiểm soát nội dung trước khi đưa vào đề thi.

## 8. Luồng làm bài và chấm điểm
1. Học viên vào đề thi, `Student\ExamController@play` load exam, questions và answers.
2. Answers được random thứ tự bằng `inRandomOrder`.
3. Khi nộp bài, `ExamSubmissionService@submitExam` chạy trong `DB::transaction`.
4. Tạo bản ghi `results`.
5. Với trắc nghiệm: kiểm tra đáp án có `is_correct=true`, cộng điểm theo số câu đúng, lưu `answer_id`.
6. Với tự luận: sanitize HTML bằng `RichTextSanitizer`, lưu nội dung vào `student_answers`, điểm sẽ do giảng viên chấm sau.
7. Cập nhật `results.score`.
8. Ghi log hoạt động nộp bài.
Với tự luận:
1. Giảng viên vào màn hình chấm bài.
2. Controller lấy bài làm, câu hỏi và bareme.
3. Gọi `AiExamService@gradeEssayAnswer` để lấy gợi ý điểm và feedback.
4. AI chỉ đóng vai trò gợi ý, giảng viên là người quyết định lưu điểm.
5. `GradingService@updateGrade` cập nhật điểm từng câu và tính lại tổng điểm.
## 9. Cơ chế mã đề và đảo câu hỏi
Khi giao bài cho lớp, giảng viên có thể chọn số lượng mã đề tối đa 10 và tùy chọn đảo câu hỏi.
- `variant_group`: gom các mã đề cùng nguồn.
- `source_exam_id`: đề gốc.
- `variant_number`: số thứ tự mã đề.
- `shuffle_questions`: có đảo câu hỏi hay không.
Khi sinh biến thể, hệ thống tạo exam mới cho từng mã đề, copy câu hỏi và đáp án từ đề gốc. Nếu bật đảo câu hỏi, danh sách câu hỏi được shuffle.
Khi học viên xem lớp, hệ thống gom các exam theo `variant_group` và chọn một mã đề ổn định cho học viên bằng `crc32(userId:variantGroup) % so_ma_de`. Nếu học viên đã nộp một mã đề rồi, lần sau vẫn hiện đúng mã đề đã nộp.
Trả lời ngắn:
> Em không chỉ đảo đáp án trên giao diện, mà tạo các bản ghi đề thi biến thể riêng. Cách này giúp mỗi mã đề có cấu trúc độc lập, dễ lưu kết quả và truy vết sau này.
## 10. Bảo mật và kiểm soát truy cập
Những điểm đã có:

- Route nghiệp vụ nằm trong middleware `auth`.
- Đăng nhập dùng Laravel Auth, password hash, regenerate session sau login/logout.
- Form có CSRF mặc định của Laravel.
- Validate request bằng controller hoặc FormRequest.
- Upload file giới hạn mime và dung lượng.
- Truy cập tài nguyên có ràng buộc chủ sở hữu ở nhiều nơi, ví dụ teacher chỉ sửa/xóa exam của mình, student chỉ xem result của mình.
- Nội dung tự luận rich text được sanitize để giảm nguy cơ XSS.
- Các thao tác quan trọng có ghi `activity_logs`.
- Các thao tác tạo đề/nộp bài được bao trong transaction để tránh lưu dữ liệu dang dở.
Điểm nên nói trung thực nếu bị hỏi:
> Hiện tại do phạm vi đồ án, phân quyền chủ yếu được kiểm soát bằng `auth`, trường `role` và các điều kiện owner trong controller/service. Nếu phát triển tiếp, em sẽ bổ sung middleware/policy riêng cho từng role, ví dụ `role:admin`, `role:teacher`, `role:student`, đồng thời tăng test cho các case truy cập trái phép.
## 11. Điểm mạnh của đồ án

- Có đủ 3 vai trò thực tế: admin, teacher, student.
- AI không chỉ tạo trắc nghiệm mà có cả tự luận và gợi ý chấm điểm.
- Có quản lý prompt AI, lịch sử AI và token.
- Có upload và trích xuất nội dung từ nhiều định dạng file.
- Có lớp học, giao bài, mã đề, đảo câu hỏi.
- Có thống kê, báo cáo, nhật ký hoạt động.
- Nghiệp vụ quan trọng được tách ra service.
## 12. Hạn chế nên nắm trước
Nên chủ động thừa nhận theo kiểu có hướng khắc phục:
- PDF scan ảnh chưa đọc được vì chưa tích hợp OCR.
- Phụ thuộc vào Gemini API, nếu API quá tải hoặc hết quota thì phải thử lại; hệ thống đã có retry/fallback model nhưng vẫn phụ thuộc bên ngoài.
- Phân quyền nên nâng cấp lên middleware/policy chuyên biệt.
- Test tự động còn ít, nên bổ sung feature test cho các luồng chính.
- File upload đang lưu trong public uploads; sản phẩm thật nên cần storage riêng, quét virus và signed URL.
- Prompt injection từ tài liệu người dùng là rủi ro cần bổ sung bộ lọc/nội dung hệ thống chặt hơn.
- Nếu đề thi có cả trắc nghiệm và tự luận trộn lẫn, logic thống kê hiện tại cần chuẩn hóa rõ hơn cách quy đổi điểm.
Mẫu trả lời:
> Trong phạm vi đồ án tốt nghiệp, em ưu tiên hoàn thiện luồng nghiệp vụ chính và khả năng demo end-to-end. Em nhận ra các điểm cần nâng cấp khi đưa vào sản phẩm thực tế là middleware phân quyền, test tự động, OCR cho PDF scan, queue cho AI job dài và cơ chế storage an toàn hơn.
## 13. Câu hỏi vấn đáp thường gặp và cách trả lời
### 1. Vì sao chọn Laravel?
Laravel phù hợp vì có sẵn MVC, routing, auth, validation, migration, Eloquent ORM và hệ sinh thái package tốt. Đồ án cần CRUD nhiều thực thể, phân quyền người dùng, upload file, dashboard và tích hợp API, nên Laravel giúp phát triển nhanh nhưng vẫn có cấu trúc rõ.
### 2. Mô hình MVC thể hiện ở đâu?
Model là các lớp trong `app/Models` như `User`, `Exam`, `Question`, `Result`. View là Blade trong `resources/views`. Controller nằm trong `app/Http/Controllers`, nhận request và trả response. Nghiệp vụ phức tạp được tách vào `app/Services`.
### 3. AI được tích hợp như thế nào?
Hệ thống đọc nội dung tài liệu, tạo prompt yêu cầu Gemini trả về JSON đúng cấu trúc, gọi API bằng Laravel HTTP client, sau đó parse JSON thành câu hỏi và đáp án. Nếu AI trả về lỗi, hệ thống retry, fallback model và thông báo lỗi.
### 4. Làm sao đảm bảo AI trả về đúng format?
Prompt yêu cầu rõ: chỉ trả về JSON, không markdown, mỗi câu có `content`, `answers`, `ai_explanation`. Khi nhận về, code loại bỏ fence ```json, cắt phần mảng JSON, `json_decode`, kiểm tra cấu trúc và chuẩn hóa số đáp án.
### 5. Nếu AI tạo câu hỏi trùng thì sao?
Controller gửi danh sách câu hỏi đã có vào prompt qua `exclude_questions`. Đồng thời code normalize nội dung bằng cách bỏ HTML, chuyển ASCII, lowercase, bỏ ký tự đặc biệt để phát hiện trùng gần đúng trong lần generate hiện tại.
### 6. Trắc nghiệm được chấm điểm thế nào?
Mỗi câu trắc nghiệm có nhiều answer, chỉ một answer có `is_correct=true`. Khi học viên nộp, hệ thống kiểm tra `answer_id` được chọn có đúng không, mỗi câu đúng cộng 1. Khi hiển thị/thống kê thì quy đổi sang thang 10 bằng `score / total_questions * 10`.
### 7. Tự luận được chấm điểm thế nào?
Khi nộp bài tự luận, hệ thống lưu nội dung bài làm vào `student_answers`. AI có thể gợi ý điểm/feedback dựa trên câu hỏi, bareme và bài làm, nhưng giảng viên mới là người lưu điểm chính thức. Khi lưu điểm, hệ thống tính lại tổng điểm trong `results`.
### 8. Vì sao cần `student_answers` riêng, không chỉ lưu trong `results`?
`results` là tổng kết một lần nộp bài. `student_answers` lưu chi tiết từng câu, bao gồm đáp án đã chọn, nội dung tự luận, điểm từng câu và feedback. Nếu không có bảng này thì không xem lại bài làm, không chấm tự luận chi tiết và không phân tích câu nào sai.
### 9. Mã lớp hoạt động như thế nào?

Giảng viên tạo lớp, hệ thống sinh `code` duy nhất. Học viên nhập code để join, quan hệ được lưu trong bảng pivot `classroom_user`.
### 10. Làm sao học viên không xem kết quả của người khác?
Route yêu cầu đăng nhập, và khi xem result controller lọc theo `user_id = Auth::id()` và `exam_id`. Nếu truyền `result_id` của người khác thì query không tìm thấy.
### 11. Làm sao giảng viên không sửa đề của giảng viên khác?
Nhiều thao tác với exam có điều kiện `where('teacher_id', Auth::id())` hoặc gọi `authorizeAccess` kiểm tra `exam.teacher_id`. Nếu không khớp thì trả 403 hoặc 404.
### 12. Tại sao cần transaction?

Tạo đề thi, lưu câu hỏi/đáp án, hoặc nộp bài gồm nhiều bản ghi. Nếu một bước lỗi mà các bước trước đã lưu thì dữ liệu sẽ lệch. Transaction đảm bảo thành công thì lưu tất cả, lỗi thì rollback.
### 13. Hệ thống đọc file PDF/DOCX/DOC/TXT như thế nào?
PDF dùng `spatie/pdf-to-text` gọi `pdftotext`. DOCX được giải nén để đọc `word/document.xml`, strip tag thành text. DOC dùng PhpWord. TXT đọc trực tiếp nội dung file.
### 14. Vì sao có bảng `prompts`?
Bảng `prompts` cho phép admin cấu hình prompt mà không cần sửa code. Hệ thống có thể dùng prompt riêng cho trắc nghiệm, tự luận hoặc cả hai. Nếu không có prompt active thì dùng prompt mặc định trong service.
### 15. AI history có tác dụng gì?
`ai_logs` lưu ai đã sử dụng AI, hành động nào, model, token và status. Admin có thể theo dõi tần suất sử dụng AI, kiểm tra lỗi và ước tính chi phí/tài nguyên.
### 16. Activity log khác AI log như thế nào?
`activity_logs` là nhật ký hoạt động chung trên hệ thống, ví dụ đăng nhập, tạo lớp, nộp bài, upload tài liệu. `ai_logs` chuyên theo dõi các lần gọi AI và token.
### 17. Vai trò của `RichTextSanitizer`?

Khi học viên nộp bài tự luận có rich text, nếu lưu và render HTML nguyên bản sẽ có nguy cơ XSS. Sanitizer loại bỏ script, iframe, form, input và chỉ cho phép một số tag/thuộc tính an toàn.
### 18. Tại sao không để AI chấm điểm tự động luôn?
AI có thể sai hoặc chấm không ổn định. Do đó trong thiết kế này AI chỉ gợi ý điểm và feedback, giảng viên vẫn là người quyết định. Cách này phù hợp bởi cần công bằng và có trách nhiệm trong giáo dục.
### 19. Nếu Gemini bị lỗi thì hệ thống xử lý ra sao?
Service có danh sách model fallback và retry với các lỗi tạm thời như 429, 500, 503. Nếu vẫn lỗi, hệ thống trả thông báo để người dùng thử lại và ghi log thất bại vào `ai_logs`.
### 20. Hướng phát triển tiếp theo là gì?
Bổ sung middleware/policy phân quyền, queue cho các job AI dài, OCR cho PDF scan, test tự động, storage an toàn hơn, rate limit AI, phân tích chất lượng câu hỏi và dashboard nâng cao cho kết quả học tập.
## 14. Câu hỏi khó hội đồng có thể bắt bẻ
### Câu 1: Nếu người dùng upload tài liệu có nội dung độc hại để điều khiển AI thì sao?
Trả lời:
> Đây là rủi ro prompt injection. Hiện tại hệ thống đã ép format JSON và chuẩn hóa output, nhưng nếu triển khai thật cần thêm system instruction chặt hơn, bộ lọc nội dung, giới hạn ngữ cảnh, và có bước giảng viên duyệt câu hỏi trước khi lưu. Điểm quan trọng là AI không tự ghi trực tiếp vào CSDL; người dùng phải xem và lưu.
### Câu 2: Vì sao `results.score` lúc là số câu đúng, lúc là điểm thang 10?
Trả lời:
> Với đề trắc nghiệm, `score` lưu số câu đúng để truy vết chính xác, khi thống kê quy đổi sang thang 10. Với tự luận, điểm giảng viên chấm đã theo thang 10. Nếu phát triển tiếp, em sẽ chuẩn hóa thêm bằng cách lưu riêng `raw_score` và `score_10` để tránh nhầm lẫn, nhất là đề thi trộn trắc nghiệm và tự luận.
### Câu 3: Tại sao học viên tự luyện lại tạo `Exam` với `teacher_id` là user học viên?
Trả lời:
> Em tái sử dụng bảng `exams` để lưu kho đề tự luyện thay vì tạo bảng mới, nên trường `teacher_id` được dùng như `owner_id` của đề. Cách này giảm trùng lặp schema vì cấu trúc đề/câu hỏi/đáp án giống nhau. Nếu refactor, em sẽ đổi tên thành `owner_id` hoặc thêm cột `created_by`/`exam_scope` để rõ nghĩa hơn.
### Câu 4: Nếu hai học viên trong cùng lớp nhận mã đề khác nhau thì chấm và thống kê có đúng không?
Trả lời:

> Mỗi mã đề là một record exam riêng, câu hỏi/đáp án được copy từ đề gốc. Khi học viên nộp bài, result gắn với exam cụ thể của học viên. Nhóm mã đề được liên kết bằng `variant_group`, nên có thể truy vết về cùng đề gốc. Hiện tại thống kê theo exam id; hướng nâng cấp là gom theo `source_exam_id`/`variant_group` để báo cáo tổng hợp tốt hơn.
### Câu 5: File upload lưu public có an toàn không?
Trả lời:
> Với mục tiêu demo đồ án, em lưu file trong public uploads để preview và truy cập đơn giản. Hệ thống có validate mime và dung lượng. Nếu sản phẩm thật, em sẽ chuyển sang private storage, quét virus, signed URL, phân quyền tải file và có chính sách xóa/hết hạn.
### Câu 6: Làm sao tránh việc học viên làm lại bài nhiều lần?
Trả lời:
> Hiện tại code có lưu mỗi lần nộp thành một result và màn hình lấy kết quả theo result gần nhất hoặc result id. Nếu yêu cầu chỉ cho nộp một lần, em sẽ thêm ràng buộc unique theo `user_id + exam_id` hoặc check trước khi submit, đồng thời cấu hình số lần nộp trong exam.
### Câu 7: Nếu AI trả về đáp án đúng sai sai thì ai chịu trách nhiệm?
Trả lời:

> Hệ thống được thiết kế theo hướng AI hỗ trợ, không thay thế giảng viên. Câu hỏi sinh ra được hiện cho giảng viên/học viên xem trước khi lưu. Với đề chính thức, giảng viên là người duyệt nội dung. Với chấm tự luận, AI chỉ gợi ý, giảng viên mới lưu điểm.
### Câu 8: Tại sao cần admin quản lý prompt?
Trả lời:
> Vì chất lượng câu hỏi phụ thuộc nhiều vào prompt. Nếu prompt nằm cùng code, mỗi lần đổi cách sinh câu hỏi phải deploy lại. Bảng `prompts` giúp admin thử nghiệm, bật/tắt và cấu hình prompt theo loại đề linh hoạt hơn.
## 15. Script demo 5-7 phút
1. Đăng nhập teacher.
2. Tạo lớp học, copy mã lớp.
3. Upload một tài liệu.
4. Tạo đề thi: chọn tài liệu, chọn số câu, loại trắc nghiệm/tự luận, bấm sinh AI.
5. Giải thích AI trả câu hỏi JSON, người dùng có thể xem/chỉnh sửa rồi lưu.
6. Giao đề cho lớp, nếu cần chọn tạo nhiều mã đề/đảo câu hỏi.
7. Đăng nhập student, join lớp bằng mã lớp.
8. Làm bài và nộp.
9. Xem kết quả học viên.
10. Quay lại teacher xem kết quả, chấm tự luận nếu có.
11. Vào admin để xem AI history/activity log.
Câu nói khi demo:
> Em sẽ demo luồng end-to-end từ lúc giảng viên upload tài liệu, AI sinh câu hỏi, giao đề cho lớp, học viên làm bài, đến khi giảng viên xem/chấm kết quả và admin theo dõi lịch sử hệ thống.
## 16. Checklist học thuộc trước khi bảo vệ
- Nói được bài toán và lý do cần hệ thống.
- Nói được 3 vai trò và chức năng mỗi vai trò.
- Vẽ/kể lại được luồng sinh đề bằng AI.
- Giải thích được các bảng CSDL chính và quan hệ.
- Giải thích được cách chấm trắc nghiệm, chấm tự luận.
- Giải thích được cơ chế mã đề.
- Nói được các biện pháp bảo mật đã có.
- Chủ động nói được hạn chế và hướng phát triển.
- Chuẩn bị demo với tài liệu có text, tránh PDF scan.
- Chuẩn bị 1 tài khoản admin, 1 teacher, 1 student và dữ liệu mẫu.
