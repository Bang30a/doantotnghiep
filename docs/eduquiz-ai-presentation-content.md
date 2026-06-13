# Nội dung thuyết trình PowerPoint - EduQuiz AI

Nguồn xây dựng nội dung:
- Báo cáo: `Phan Tự Trường - Đồ án tốt nghiệp.pdf`
- Sản phẩm thực tế: EduQuiz AI - ứng dụng web hỗ trợ ôn tập và sinh đề thi tự động từ tài liệu

Thông tin đề tài:
- Trường: Trường Đại học Công nghệ Đông Á
- Ngành: Công nghệ thông tin
- Đề tài: Xây dựng ứng dụng web hỗ trợ ôn tập và sinh đề thi tự động từ tài liệu, sử dụng PHP Laravel, Bootstrap, Docker và tích hợp API AI
- Sinh viên thực hiện: Phan Tự Trường
- MSSV: 20221889
- Lớp: DCCNTT 13.10.10
- Giảng viên hướng dẫn: ThS. Mai Văn Linh

Gợi ý thời lượng:
- Bản trình bày 12-15 phút: dùng đủ 15 slide.
- Bản trình bày 7-10 phút: trình bày kỹ slide 2, 3, 6, 8, 9, 10, 12, 13, 14.

---

## Slide 1. Giới thiệu đề tài

Nội dung trên slide:
- EduQuiz AI
- Ứng dụng web hỗ trợ ôn tập và sinh đề thi tự động từ tài liệu
- Công nghệ: PHP Laravel, MySQL, Bootstrap, Docker, Gemini API
- Sinh viên: Phan Tự Trường - MSSV 20221889
- Giảng viên hướng dẫn: ThS. Mai Văn Linh

Lời thuyết trình:
> Em xin trình bày đồ án tốt nghiệp với đề tài "Xây dựng ứng dụng web hỗ trợ ôn tập và sinh đề thi tự động từ tài liệu". Hệ thống có tên EduQuiz AI, tập trung hỗ trợ học viên tự ôn tập, hỗ trợ giảng viên tạo và giao đề, đồng thời tích hợp AI để tự động sinh câu hỏi từ tài liệu học tập.

Gợi ý thiết kế:
- Dùng ảnh nền nhẹ kiểu dashboard giáo dục hoặc mockup giao diện chính.
- Đặt tên EduQuiz AI lớn, dùng tím - xanh - teal đúng màu web.

---

## Slide 2. Lý do chọn đề tài

Nội dung trên slide:
- Học viên mất nhiều thời gian đọc tài liệu, tổng hợp kiến thức và tự tạo câu hỏi ôn tập.
- Giảng viên phải biên soạn câu hỏi, tạo đề, giao bài và theo dõi kết quả thủ công.
- Nhu cầu học tập trực tuyến ngày càng tăng.
- AI có thể hỗ trợ xử lý tài liệu, sinh câu hỏi và tự động hóa một phần quy trình ôn tập.

Lời thuyết trình:
> Trong thực tế, học viên thường có nhiều tài liệu nhưng khó biến tài liệu đó thành câu hỏi luyện tập. Về phía giảng viên, việc tạo đề, giao bài và theo dõi kết quả cũng tốn nhiều thời gian nếu làm thủ công. Vì vậy em chọn đề tài này để xây dựng một hệ thống web có thể kết hợp quản lý học tập với AI, giúp quá trình ôn tập và tạo đề nhanh hơn, có hệ thống hơn.

Gợi ý thiết kế:
- Chia 2 cột: "Khó khăn hiện tại" và "Giải pháp EduQuiz AI".
- Dùng icon tài liệu, đồng hồ, giáo viên, AI.

---

## Slide 3. Mục tiêu và phạm vi

Nội dung trên slide:
- Xây dựng ứng dụng web hỗ trợ ôn tập và sinh đề tự động từ tài liệu.
- Quản lý người dùng theo 3 vai trò: học viên, giảng viên, quản trị viên.
- Học viên: tham gia lớp, tải tài liệu, tạo đề tự luyện, làm bài, xem kết quả.
- Giảng viên: quản lý lớp, học viên, tài liệu, đề thi, giao bài và theo dõi kết quả.
- Quản trị viên: quản lý tài khoản, lớp học, tài liệu, kho đề, prompt AI và cài đặt hệ thống.

Lời thuyết trình:
> Mục tiêu chính của đồ án là xây dựng một hệ thống web hoàn chỉnh cho ba nhóm người dùng. Học viên có thể tự luyện tập từ tài liệu cá nhân. Giảng viên có thể quản lý lớp, tạo đề và giao bài. Quản trị viên có thể quản lý dữ liệu toàn hệ thống, cấu hình prompt AI và theo dõi hoạt động sử dụng AI.

Gợi ý thiết kế:
- Dùng sơ đồ 3 vai trò: Student - Teacher - Admin.
- Mỗi vai trò có 3-4 chức năng chính, tránh nhồi quá nhiều chữ.

---

## Slide 4. Cơ sở lý thuyết và công nghệ

Nội dung trên slide:
- Mô hình hệ thống học tập trực tuyến.
- Quản lý tài liệu, câu hỏi, đề thi và kết quả học tập.
- Sinh câu hỏi tự động bằng AI từ tài liệu.
- Kiến trúc MVC và Service Layer.
- Công nghệ sử dụng:
  - Laravel, PHP
  - MySQL
  - Bootstrap 5, JavaScript
  - Docker
  - Gemini API

Lời thuyết trình:
> Về cơ sở lý thuyết, đồ án dựa trên mô hình học tập trực tuyến, trong đó có quản lý người dùng, lớp học, tài liệu, đề thi và kết quả. Về kỹ thuật, hệ thống được xây dựng bằng Laravel theo mô hình MVC, kết hợp Service Layer để tách nghiệp vụ. Cơ sở dữ liệu dùng MySQL, giao diện dùng Bootstrap và JavaScript, môi trường có hỗ trợ Docker, còn chức năng AI sử dụng Gemini API.

Gợi ý thiết kế:
- Dùng hàng icon công nghệ.
- Có thể thêm dòng "MVC + Service Layer" làm điểm kỹ thuật nổi bật.

---

## Slide 5. Bài toán đặt ra

Nội dung trên slide:
- Đầu vào: tài liệu học tập do học viên hoặc giảng viên tải lên.
- Xử lý: đọc tài liệu, chọn prompt phù hợp, gửi yêu cầu tới AI, kiểm tra dữ liệu trả về.
- Đầu ra: bộ câu hỏi, đáp án, giải thích, đề thi và kết quả làm bài.
- Yêu cầu:
  - Dễ sử dụng.
  - Phân quyền rõ ràng.
  - Lưu trữ dữ liệu đầy đủ.
  - Có khả năng mở rộng.

Lời thuyết trình:
> Bài toán của hệ thống là chuyển tài liệu học tập thành các bộ câu hỏi có thể dùng để ôn tập hoặc giao bài. Dữ liệu đầu vào là tài liệu, thông tin đề và số lượng câu hỏi. Hệ thống xử lý bằng cách tạo prompt, gọi AI, kiểm tra cấu trúc phản hồi, sau đó lưu thành câu hỏi, đáp án và đề thi trong cơ sở dữ liệu.

Gợi ý thiết kế:
- Dùng flow ngang: Tài liệu -> Prompt -> Gemini API -> Kiểm tra -> Lưu DB -> Làm bài.

---

## Slide 6. Chức năng theo vai trò

Nội dung trên slide:
- Học viên:
  - Tham gia lớp học.
  - Quản lý tài liệu cá nhân.
  - Tạo đề tự luyện bằng AI.
  - Làm bài, nộp bài, xem kết quả, lịch sử và thống kê.
- Giảng viên:
  - Quản lý lớp học và học viên.
  - Quản lý tài liệu giảng dạy.
  - Tạo đề AI, quản lý đề, giao bài.
  - Theo dõi kết quả và báo cáo.
- Quản trị viên:
  - Quản lý người dùng, lớp học, tài liệu, kho đề.
  - Cấu hình AI/Prompt.
  - Theo dõi lịch sử AI, nhật ký hoạt động và cài đặt hệ thống.

Lời thuyết trình:
> Hệ thống được chia theo ba vai trò chính. Học viên là người học và tự luyện. Giảng viên là người tổ chức lớp, tạo và giao đề. Quản trị viên quản lý toàn bộ dữ liệu và cấu hình hệ thống. Việc phân quyền này giúp giao diện và chức năng của từng nhóm rõ ràng, tránh người dùng truy cập sai phạm vi.

Gợi ý thiết kế:
- Dùng 3 card lớn, mỗi card một màu phụ: tím, xanh, teal.
- Mỗi card chỉ giữ 4 ý chính.

---

## Slide 7. Phân tích và thiết kế hệ thống

Nội dung trên slide:
- Biểu đồ phân cấp chức năng.
- Use Case cho khách, học viên, giảng viên, quản trị viên.
- Biểu đồ lớp mô tả các thực thể chính.
- Biểu đồ trạng thái cho đề thi, bài làm, tài khoản, tài liệu.
- Biểu đồ tuần tự và hoạt động cho các quy trình chính.

Lời thuyết trình:
> Trong chương phân tích thiết kế, báo cáo đã mô tả hệ thống bằng nhiều loại biểu đồ. Biểu đồ phân cấp chức năng cho thấy các nhóm chức năng chính. Use Case mô tả tương tác của từng vai trò. Biểu đồ lớp làm rõ các thực thể như User, Classroom, Exam, Question, Answer và ExamResult. Ngoài ra còn có biểu đồ trạng thái, tuần tự và hoạt động để mô tả quy trình nghiệp vụ.

Gợi ý thiết kế:
- Không cần đưa toàn bộ biểu đồ vào một slide.
- Chọn 1 sơ đồ tổng quan hoặc vẽ lại đơn giản bằng các khối chức năng.

---

## Slide 8. Quy trình sinh đề bằng AI

Nội dung trên slide:
1. Người dùng chọn tài liệu nguồn.
2. Nhập tên đề, loại câu hỏi, thời gian và số lượng câu.
3. Hệ thống lấy prompt phù hợp từ cơ sở dữ liệu.
4. Ghép prompt với nội dung tài liệu và yêu cầu người dùng.
5. Gửi yêu cầu tới Gemini API.
6. Kiểm tra cấu trúc phản hồi, loại bỏ dữ liệu lỗi hoặc trùng.
7. Lưu đề, câu hỏi, đáp án và giải thích vào cơ sở dữ liệu.

Lời thuyết trình:
> Đây là luồng quan trọng nhất của hệ thống. Khi người dùng tạo đề, hệ thống không dùng một prompt cố định trong code, mà có thể lấy prompt theo loại câu hỏi từ cơ sở dữ liệu. Sau đó hệ thống ghép prompt với tài liệu và thông tin yêu cầu, gọi Gemini API, kiểm tra phản hồi rồi lưu vào các bảng đề thi, câu hỏi và đáp án. Cách này giúp prompt linh hoạt và dễ chỉnh sửa hơn.

Gợi ý thiết kế:
- Dùng sơ đồ pipeline.
- Nhấn mạnh "Prompt lưu trong DB" và "Validate trước khi lưu".

---

## Slide 9. Thiết kế cơ sở dữ liệu

Nội dung trên slide:
- CSDL: MySQL, tên database `eduquiz`.
- Nhóm tài khoản và phân quyền:
  - `users`
- Nhóm lớp học:
  - `classrooms`, `classroom_user`
- Nhóm tài liệu và đề thi:
  - `documents`, `exams`, `questions`, `answers`
- Nhóm bài làm và kết quả:
  - `exam_results`, `student_answers`, `results`
- Nhóm hệ thống và AI:
  - `prompts`, `ai_logs`, `activity_logs`, `settings`

Lời thuyết trình:
> Cơ sở dữ liệu được thiết kế theo các nhóm bảng rõ ràng. Nhóm người dùng quản lý tài khoản và vai trò. Nhóm lớp học lưu lớp và quan hệ học viên - lớp. Nhóm tài liệu, đề thi, câu hỏi và đáp án là phần lõi của hệ thống. Nhóm kết quả lưu quá trình làm bài. Cuối cùng là nhóm bảng phục vụ AI và quản trị như prompt, log sử dụng AI và cài đặt hệ thống.

Gợi ý thiết kế:
- Vẽ ERD rút gọn, không cần đủ mọi cột.
- Nên đưa các bảng lõi: users, classrooms, documents, exams, questions, answers, exam_results.

---

## Slide 10. Kiến trúc mã nguồn

Nội dung trên slide:
- Laravel MVC:
  - Model: ánh xạ dữ liệu.
  - View: giao diện Blade.
  - Controller: điều phối request.
- Service Layer:
  - `AiExamService`: xử lý tạo đề bằng AI.
  - `ExamSubmissionService`: xử lý nộp bài và tính điểm.
  - `GradingService`: hỗ trợ chấm điểm.
  - `PromptService`, `SystemService`, `UserService`: nghiệp vụ quản trị.
- Request Validation:
  - Kiểm tra dữ liệu đầu vào trước khi xử lý.
- Routes:
  - `web.php`, `api.php`.

Lời thuyết trình:
> Về kiến trúc mã nguồn, hệ thống dùng Laravel MVC nhưng không dồn toàn bộ nghiệp vụ vào Controller. Các phần xử lý quan trọng được tách sang Service Layer, ví dụ tạo đề AI, nộp bài, chấm điểm và cấu hình prompt. Cách tổ chức này giúp code dễ bảo trì, dễ kiểm thử và dễ mở rộng khi thêm chức năng mới.

Gợi ý thiết kế:
- Dùng sơ đồ: Route -> Controller -> Request -> Service -> Model -> Database -> View.
- Có thể chèn ảnh cấu trúc thư mục dự án.

---

## Slide 11. Sản phẩm hoàn thiện - Học viên

Nội dung trên slide:
- Dashboard học viên.
- Tham gia lớp học bằng mã lớp.
- Upload và quản lý tài liệu cá nhân.
- Tạo đề tự luyện bằng AI.
- Làm bài, nộp bài, xem kết quả.
- Lịch sử làm bài và thống kê học tập.

Lời thuyết trình:
> Với vai trò học viên, hệ thống cung cấp các chức năng phục vụ tự học. Học viên có thể tham gia lớp bằng mã, tải tài liệu lên, dùng AI để tạo đề tự luyện, làm bài trực tuyến và xem kết quả sau khi nộp. Ngoài ra hệ thống có lịch sử làm bài và thống kê để học viên theo dõi quá trình học tập.

Gợi ý thiết kế:
- Dùng 2-3 ảnh chụp màn hình: dashboard, tạo đề AI, lịch sử/kết quả.
- Chỉ đặt caption ngắn dưới từng ảnh.

---

## Slide 12. Sản phẩm hoàn thiện - Giảng viên và quản trị

Nội dung trên slide:
- Giảng viên:
  - Quản lý lớp học và học viên.
  - Upload tài liệu giảng dạy.
  - Tạo đề bằng AI và quản lý danh sách đề.
  - Giao đề, xem kết quả, báo cáo.
- Quản trị viên:
  - Quản lý học viên, giảng viên, lớp học.
  - Quản lý tài liệu hệ thống và kho đề chung.
  - Cấu hình prompt AI, theo dõi lịch sử AI.
  - Cài đặt hệ thống.

Lời thuyết trình:
> Với giảng viên, hệ thống hỗ trợ quản lý lớp và học viên, tạo đề từ tài liệu, giao bài và theo dõi kết quả. Với quản trị viên, hệ thống có các chức năng quản lý toàn cục như tài khoản, lớp học, tài liệu, kho đề, cấu hình prompt AI và lịch sử sử dụng AI.

Gợi ý thiết kế:
- Chia slide thành hai nửa: Teacher và Admin.
- Mỗi nửa có một ảnh dashboard hoặc một ảnh trang quản lý chính.

---

## Slide 13. Kiểm thử hệ thống

Nội dung trên slide:
- Môi trường kiểm thử:
  - Windows, PHP, Laravel, MySQL, Docker, Chrome, Gemini API.
- Nhóm chức năng đã kiểm thử:
  - Đăng ký, đăng nhập, đăng xuất.
  - Quản lý lớp học.
  - Upload và quản lý tài liệu.
  - Tạo đề bằng AI.
  - Giao đề cho lớp.
  - Làm bài, nộp bài, xem kết quả.
  - Thống kê, lịch sử, báo cáo.
  - Chức năng quản trị.
- Kết quả: các kịch bản chính đạt yêu cầu.

Lời thuyết trình:
> Hệ thống được kiểm thử trên môi trường local với Laravel, MySQL và trình duyệt Chrome. Các nhóm chức năng chính đều được kiểm thử theo tình huống hợp lệ, thiếu dữ liệu và dữ liệu không hợp lệ. Kết quả cho thấy các chức năng cốt lõi như đăng nhập, quản lý lớp, tải tài liệu, tạo đề AI, giao bài, làm bài và xem kết quả hoạt động đúng theo yêu cầu.

Gợi ý thiết kế:
- Dùng bảng kiểm thử rút gọn với cột: Nhóm chức năng - Tình huống - Kết quả.
- Không đưa quá nhiều test case nhỏ.

---

## Slide 14. Kết quả đạt được

Nội dung trên slide:
- Xây dựng được hệ thống web có đủ 3 phân hệ: học viên, giảng viên, quản trị viên.
- Hoàn thiện quy trình từ tài liệu -> sinh đề AI -> làm bài -> lưu kết quả.
- Cơ sở dữ liệu tổ chức rõ ràng, hỗ trợ mở rộng.
- Giao diện được thiết kế theo từng vai trò.
- Prompt AI có thể cấu hình từ trang quản trị.
- Hệ thống đáp ứng các chức năng chính trong phạm vi đồ án.

Lời thuyết trình:
> Sau quá trình thực hiện, đồ án đã xây dựng được một hệ thống web hoàn chỉnh với ba phân hệ chính. Điểm nổi bật là quy trình tạo đề từ tài liệu bằng AI, sau đó lưu thành đề thi để học viên luyện tập hoặc giảng viên giao bài. Hệ thống cũng có cơ chế quản trị prompt, quản lý dữ liệu và theo dõi kết quả học tập.

Gợi ý thiết kế:
- Dùng timeline hoặc flow hoàn chỉnh.
- Có thể dùng con số nổi bật: 3 vai trò, nhiều nhóm bảng, nhiều nhóm kiểm thử, quy trình AI hoàn chỉnh.

---

## Slide 15. Hạn chế và hướng phát triển

Nội dung trên slide:
- Hạn chế:
  - Chất lượng câu hỏi AI phụ thuộc vào tài liệu nguồn và prompt.
  - Tốc độ sinh đề phụ thuộc API bên ngoài.
  - Chưa kiểm thử sâu với số lượng người dùng lớn.
  - Phân tích năng lực học viên mới ở mức cơ bản.
  - Chấm tự luận AI cần tiếp tục hoàn thiện để đạt độ tin cậy cao.
- Hướng phát triển:
  - Mở rộng loại câu hỏi: tự luận, điền khuyết, đúng/sai, ghép nối.
  - Hoàn thiện chấm tự luận bằng AI kèm nhận xét chi tiết.
  - Gợi ý lộ trình học cá nhân hóa.
  - Tối ưu hiệu năng, bảo mật và triển khai thực tế.
  - Thêm thông báo deadline, sao lưu và tối ưu giao diện mobile.

Lời thuyết trình:
> Bên cạnh kết quả đạt được, hệ thống vẫn còn một số hạn chế. Chất lượng đề phụ thuộc vào tài liệu và prompt, đồng thời tốc độ xử lý phụ thuộc API AI. Trong tương lai, hệ thống có thể mở rộng nhiều dạng câu hỏi hơn, hoàn thiện chấm tự luận AI, phân tích năng lực học viên sâu hơn và triển khai trên môi trường thực tế với tối ưu hiệu năng, bảo mật và giao diện mobile.

Gợi ý thiết kế:
- Chia 2 cột: Hạn chế và Hướng phát triển.
- Hướng phát triển nên dùng icon mũi tên hoặc roadmap.

---

## Slide 16. Demo và kết luận

Nội dung trên slide:
- Demo đề xuất:
  1. Đăng nhập học viên/giảng viên.
  2. Upload tài liệu.
  3. Tạo đề bằng AI.
  4. Lưu đề và xem danh sách câu hỏi.
  5. Làm bài và xem kết quả.
  6. Giảng viên xem/chấm/theo dõi kết quả.
- Kết luận:
  - EduQuiz AI hỗ trợ tự động hóa quá trình ôn tập và tạo đề.
  - Hệ thống có tính ứng dụng trong học tập trực tuyến.
  - Có khả năng mở rộng thành nền tảng học tập thông minh hơn.

Lời thuyết trình:
> Kết luận lại, EduQuiz AI đã giải quyết được bài toán hỗ trợ ôn tập và sinh đề từ tài liệu bằng AI. Hệ thống giúp học viên luyện tập chủ động hơn, giúp giảng viên giảm thời gian tạo đề và giúp quản trị viên quản lý dữ liệu tập trung. Đây là nền tảng có thể tiếp tục phát triển thêm các chức năng thông minh như chấm tự luận AI, phân tích năng lực và gợi ý lộ trình học cá nhân hóa.

Gợi ý thiết kế:
- Dùng ảnh chụp màn hình quy trình demo.
- Kết slide bằng câu: "Xin cảm ơn thầy cô đã lắng nghe."

---

# Kịch bản thuyết trình ngắn 5-7 phút

Kính thưa thầy cô, em xin trình bày đồ án tốt nghiệp với đề tài "Xây dựng ứng dụng web hỗ trợ ôn tập và sinh đề thi tự động từ tài liệu, sử dụng PHP Laravel, Bootstrap, Docker và tích hợp API AI". Sản phẩm của em có tên EduQuiz AI.

Lý do em chọn đề tài này là vì trong quá trình học tập, học viên thường có nhiều tài liệu nhưng việc tự đọc, tổng hợp và tạo câu hỏi ôn tập mất nhiều thời gian. Giảng viên cũng gặp khó khăn khi phải biên soạn đề, giao bài và theo dõi kết quả thủ công. Trong khi đó, AI có khả năng hỗ trợ xử lý tài liệu và sinh câu hỏi, nên em muốn ứng dụng AI vào một hệ thống học tập trực tuyến thực tế.

Mục tiêu của đồ án là xây dựng một ứng dụng web có ba vai trò chính. Học viên có thể tham gia lớp, tải tài liệu, tạo đề tự luyện bằng AI, làm bài và xem kết quả. Giảng viên có thể quản lý lớp, học viên, tài liệu, tạo đề, giao bài và xem báo cáo. Quản trị viên có thể quản lý người dùng, lớp học, tài liệu, kho đề, prompt AI, lịch sử sử dụng AI và cài đặt hệ thống.

Về công nghệ, hệ thống được xây dựng bằng Laravel theo mô hình MVC kết hợp Service Layer. Cơ sở dữ liệu sử dụng MySQL. Giao diện sử dụng Bootstrap 5 và JavaScript. Môi trường có hỗ trợ Docker, còn chức năng sinh đề tự động tích hợp Gemini API.

Quy trình quan trọng nhất của hệ thống là sinh đề từ tài liệu. Người dùng chọn tài liệu nguồn, nhập thông tin đề và số lượng câu hỏi. Hệ thống lấy prompt phù hợp từ cơ sở dữ liệu, ghép với nội dung tài liệu, gửi tới Gemini API, sau đó kiểm tra phản hồi và lưu vào các bảng đề thi, câu hỏi, đáp án. Việc lưu prompt trong cơ sở dữ liệu giúp quản trị viên có thể điều chỉnh prompt mà không phải sửa trực tiếp trong mã nguồn.

Về cơ sở dữ liệu, hệ thống gồm các nhóm bảng chính như users, classrooms, classroom_user, documents, exams, questions, answers, exam_results, student_answers, prompts, ai_logs và settings. Các bảng này hỗ trợ đầy đủ việc quản lý tài khoản, lớp học, tài liệu, đề thi, bài làm, kết quả và cấu hình AI.

Trong quá trình xây dựng, em đã hoàn thiện các phân hệ học viên, giảng viên và quản trị viên. Học viên có dashboard, quản lý tài liệu, tạo đề tự luyện, làm bài và xem lịch sử. Giảng viên có quản lý lớp, học viên, tài liệu, đề thi và báo cáo. Quản trị viên có quản lý tài khoản, lớp học, tài liệu hệ thống, kho đề, prompt AI và cài đặt hệ thống.

Hệ thống đã được kiểm thử trên môi trường local với các nhóm chức năng chính như đăng ký, đăng nhập, quản lý lớp, upload tài liệu, tạo đề AI, giao đề, làm bài, xem kết quả, thống kê và quản trị. Các kịch bản chính đều hoạt động đúng theo yêu cầu.

Bên cạnh kết quả đạt được, hệ thống vẫn còn một số hạn chế như chất lượng câu hỏi AI phụ thuộc tài liệu và prompt, tốc độ phụ thuộc API bên ngoài, chưa kiểm thử sâu với số lượng người dùng lớn và chức năng chấm tự luận AI cần tiếp tục hoàn thiện. Trong tương lai, em định hướng mở rộng thêm nhiều dạng câu hỏi, hoàn thiện chấm tự luận bằng AI, phân tích năng lực học viên và triển khai hệ thống trên môi trường thực tế.

Em xin kết thúc phần trình bày. Em xin cảm ơn thầy cô đã lắng nghe.

---

# Câu hỏi hội đồng có thể hỏi và gợi ý trả lời

## 1. Vì sao chọn Laravel thay vì Node.js hoặc Django?

Gợi ý trả lời:
> Em chọn Laravel vì Laravel hỗ trợ tốt mô hình MVC, routing, validation, Eloquent ORM, middleware phân quyền và Blade template. Với phạm vi đồ án web quản lý học tập, Laravel giúp triển khai nhanh, cấu trúc rõ ràng và dễ bảo trì.

## 2. Điểm mới của đề tài so với một LMS thông thường là gì?

Gợi ý trả lời:
> Điểm khác biệt là hệ thống không chỉ quản lý lớp, tài liệu và bài làm, mà còn tích hợp AI để sinh đề từ tài liệu. Người dùng có thể biến tài liệu học tập thành câu hỏi luyện tập hoặc đề thi, giúp giảm thời gian biên soạn thủ công.

## 3. Prompt AI được xử lý như thế nào?

Gợi ý trả lời:
> Prompt không chỉ viết cố định trong code. Hệ thống có bảng `prompts` để quản trị viên cấu hình prompt theo loại câu hỏi hoặc mục đích sử dụng. Khi tạo đề, service sẽ lấy prompt phù hợp, ghép với nội dung tài liệu và tham số như số câu, chủ đề, loại câu hỏi trước khi gửi tới AI.

## 4. Làm sao hạn chế AI trả về sai cấu trúc?

Gợi ý trả lời:
> Hệ thống yêu cầu AI trả về theo cấu trúc cụ thể, sau đó kiểm tra dữ liệu trước khi lưu. Nếu dữ liệu thiếu câu hỏi, thiếu đáp án, sai số lượng hoặc không đúng định dạng, hệ thống sẽ báo lỗi thay vì lưu trực tiếp vào cơ sở dữ liệu.

## 5. Nếu AI sinh câu hỏi trùng thì xử lý thế nào?

Gợi ý trả lời:
> Hướng xử lý là so sánh nội dung câu hỏi trong cùng một lần sinh đề và loại bỏ câu trùng hoặc quá giống nhau trước khi lưu. Ngoài ra prompt cũng yêu cầu AI tạo câu hỏi đa dạng, không lặp nội dung. Đây là phần có thể tiếp tục cải tiến bằng so khớp ngữ nghĩa thay vì chỉ so khớp chuỗi.

## 6. Vì sao cần Service Layer?

Gợi ý trả lời:
> Nếu để toàn bộ nghiệp vụ trong Controller thì code sẽ dài và khó bảo trì. Service Layer giúp tách các nghiệp vụ như tạo đề AI, nộp bài, chấm điểm, quản lý prompt ra khỏi Controller. Nhờ vậy hệ thống dễ mở rộng và dễ kiểm tra logic hơn.

## 7. Hệ thống phân quyền như thế nào?

Gợi ý trả lời:
> Người dùng có trường `role` gồm admin, teacher và student. Các route được chia theo vai trò và kiểm soát bằng middleware. Mỗi nhóm người dùng chỉ truy cập được các chức năng phù hợp, ví dụ học viên không được vào trang quản trị hoặc trang quản lý lớp của giảng viên.

## 8. Cơ sở dữ liệu có những bảng quan trọng nào?

Gợi ý trả lời:
> Các bảng lõi gồm `users`, `classrooms`, `classroom_user`, `documents`, `exams`, `questions`, `answers`, `exam_results`, `student_answers`. Ngoài ra có các bảng phục vụ AI và quản trị như `prompts`, `ai_logs`, `activity_logs`, `settings`.

## 9. Chấm tự luận AI đã hoàn chỉnh chưa?

Gợi ý trả lời:
> Trong phạm vi báo cáo, chấm tự luận AI được xác định là hướng cần tiếp tục hoàn thiện. Sản phẩm có thể hỗ trợ gợi ý hoặc hiển thị barem để giảng viên chấm thuận tiện hơn, nhưng để dùng như chấm tự động hoàn toàn thì cần kiểm thử thêm về độ chính xác và độ ổn định.

## 10. Hạn chế lớn nhất của hệ thống là gì?

Gợi ý trả lời:
> Hạn chế lớn nhất là phụ thuộc vào chất lượng tài liệu nguồn, prompt và API AI bên ngoài. Nếu tài liệu thiếu rõ ràng hoặc API phản hồi không ổn định thì chất lượng câu hỏi có thể bị ảnh hưởng. Vì vậy hệ thống cần bước kiểm tra dữ liệu và vẫn cần người dùng xem lại đề trước khi sử dụng chính thức.

## 11. Nếu triển khai thực tế cần bổ sung gì?

Gợi ý trả lời:
> Cần tối ưu hiệu năng, kiểm thử tải với nhiều người dùng, bổ sung bảo mật triển khai, backup dữ liệu, giám sát lỗi, quản lý quota AI và tối ưu giao diện mobile. Ngoài ra cần hoàn thiện thêm phân tích năng lực học viên và gợi ý lộ trình học cá nhân hóa.

## 12. Docker được dùng để làm gì?

Gợi ý trả lời:
> Docker hỗ trợ đóng gói môi trường chạy ứng dụng, giúp việc cài đặt và triển khai đồng nhất hơn giữa các máy. Với đồ án, Docker giúp quản lý môi trường Laravel, database và các dịch vụ liên quan thuận tiện hơn.

---

# Gợi ý thiết kế PowerPoint

## Phong cách chung

- Không dùng quá nhiều chữ mỗi slide.
- Dùng ảnh chụp màn hình thật của hệ thống ở các slide sản phẩm.
- Tông màu nên theo web hiện tại:
  - Tím chủ đạo: `#6d28d9` hoặc gần tương đương.
  - Xanh dương: `#2563eb`.
  - Teal/xanh ngọc: `#0f766e`.
  - Cam nhấn: `#f59e0b`.
  - Nền sáng: trắng, xanh xám rất nhạt.
- Mỗi slide nên có một điểm nhấn rõ: một sơ đồ, một bảng, hoặc một ảnh giao diện.

## Layout đề xuất

- Slide 1: Hero lớn, có tên đề tài.
- Slide 2-3: Vấn đề và mục tiêu, dùng 2-3 cột.
- Slide 4-5: Công nghệ và bài toán, dùng sơ đồ khối.
- Slide 6: 3 card vai trò.
- Slide 7-10: Phần kỹ thuật, dùng sơ đồ luồng và ERD rút gọn.
- Slide 11-12: Ảnh màn hình sản phẩm.
- Slide 13: Bảng kiểm thử rút gọn.
- Slide 14-15: Kết quả, hạn chế, hướng phát triển.
- Slide 16: Kết luận và demo.

## Ảnh nên chụp để đưa vào slide

- Dashboard học viên.
- Trang tạo đề tự luyện AI.
- Trang làm bài.
- Trang kết quả/lịch sử làm bài.
- Dashboard giảng viên.
- Trang quản lý lớp/học viên.
- Trang quản trị prompt AI hoặc cài đặt hệ thống.

## Diagram nên có

Luồng sinh đề AI:

```text
Tài liệu học tập
    -> Trích xuất nội dung
    -> Chọn prompt theo loại đề
    -> Gửi Gemini API
    -> Kiểm tra phản hồi
    -> Lưu đề, câu hỏi, đáp án
    -> Học viên làm bài / Giảng viên giao bài
```

Kiến trúc xử lý request:

```text
Browser
    -> Route
    -> Controller
    -> Form Request Validation
    -> Service Layer
    -> Model / Eloquent
    -> MySQL
    -> Blade View / JSON Response
```

Phân quyền:

```text
User
    -> Student: tự học, làm bài, xem kết quả
    -> Teacher: quản lý lớp, tạo đề, giao bài
    -> Admin: quản lý hệ thống, prompt, tài khoản
```
