$(document).ready(function() {
        // ===============================
    // ĐỔI FORM NHẬP TAY THEO LOẠI CÂU HỎI
    // ===============================
    function toggleManualQuestionForm() {
        let type = $('#exam_type').val();

        console.log('Loại câu hỏi đang chọn:', type);

        if (type === 'essay') {
            $('#manual_mcq_area').hide();
            $('#manual_essay_area').show();
        } else {
            $('#manual_mcq_area').show();
            $('#manual_essay_area').hide();
        }
    }

    $(document).on('change input', '#exam_type', function () {
        toggleManualQuestionForm();
    });

    toggleManualQuestionForm();
    
    let modalElement = document.getElementById('successSaveModal');
    if (modalElement && (modalElement.dataset.showOnLoad === 'true' || window.showSuccessModalOnLoad)) {
        let successModal = new bootstrap.Modal(modalElement);
        successModal.show();
    }

    $('#configCollapse').on('hidden.bs.collapse', function () {
        $('#configHeader .toggle-icon').css('transform', 'rotate(180deg)');
        $('#configStatusText').text('Mở rộng');
    });
    $('#configCollapse').on('shown.bs.collapse', function () {
        $('#configHeader .toggle-icon').css('transform', 'rotate(0deg)');
        $('#configStatusText').text('Thu gọn');
    });

    let rawData = $('#studentExamForm').attr('data-existing-questions');
    let quizQuestions = [];
    let editModal = null;
    const editModalElement = document.getElementById('editQuestionModal');

    if (editModalElement) {
        editModal = new bootstrap.Modal(editModalElement);
    }
    
    if (rawData) {
        try { quizQuestions = JSON.parse(rawData); } catch (e) {}
    }

    if (quizQuestions.length > 0) { renderQuestionsUI(); }

    function renderQuestionsUI() {
        let area = $('#preview-questions-area');
        
        if (quizQuestions.length === 0) {
            area.html(`
                <div class="empty-questions py-4 mt-3 text-center rounded-3 bg-light border-dashed transition-all">
                    <div class="icon-wrapper-sm bg-white text-purple mx-auto rounded-circle mb-2 shadow-sm" style="width: 45px; height: 45px; font-size: 1.4rem;">
                        <i class="bi bi-inboxes"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1 fs-6">Chưa có câu hỏi nào</h6>
                    <p class="text-muted small fw-medium mb-0" style="font-size: 0.8rem;">Sử dụng AI hoặc nhập thủ công bên phải</p>
                </div>
            `);
            $('#lbl-question-count').text('Danh sách câu hỏi');
            $('#badge-question-stats').text('0 câu');
            $('#ai_questions_data').val(''); 
            return;
        }

        let html = '<div class="d-flex flex-column gap-3">';
        
        quizQuestions.forEach(function(q, index) {
            let badgeType = q.type === 'essay' 
                ? '<span class="badge bg-info bg-opacity-10 text-info px-2 py-1 ms-2"><i class="bi bi-list"></i> Tự luận</span>' 
                : '<span class="badge bg-purple-light text-purple px-2 py-1 ms-2"><i class="bi bi-list-ul"></i> Trắc nghiệm</span>';
            
            html += `
            <div class="premium-question-card p-4 position-relative bg-white shadow-sm">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center">
                        <div class="q-number-box fw-bold d-flex align-items-center justify-content-center flex-shrink-0">${index + 1}</div>
                        ${badgeType}
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-q d-flex align-items-center justify-content-center" data-index="${index}" title="Sửa" style="width:32px;height:32px;">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-q d-flex align-items-center justify-content-center" data-index="${index}" title="Xóa" style="width:32px;height:32px;">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
                
                <h6 class="fw-bold mb-3 text-dark lh-base" style="font-size: 0.95rem;">${q.content}</h6>
            `;
            
            // TRẮC NGHIỆM
            if (q.type === 'multiple_choice' && q.answers) {
                html += `<div class="row g-2">`;
                q.answers.forEach(function(ans, aIndex) {
                    let label = String.fromCharCode(65 + aIndex);
                    let isCorrect = ans.is_correct === true || ans.is_correct === "true" || ans.is_correct === 1;
                    
                    let ansClass = isCorrect ? 'ans-box-correct' : 'ans-box-neutral';
                    let textClass = isCorrect ? 'text-purple fw-bold' : 'text-dark';
                    let icon = isCorrect ? '<i class="bi bi-check-circle-fill text-purple"></i>' : '<i class="bi bi-circle opacity-25"></i>';
                    
                    html += `
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-2 ${ansClass}">
                                <div class="me-2 d-flex align-items-center justify-content-center">${icon}</div>
                                <div class="flex-grow-1" style="font-size: 0.85rem; line-height: 1.4;">
                                    <span class="fw-bold me-1 ${textClass}">${label}.</span>
                                    <span class="${textClass}">${ans.content}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += `</div>`;
            } 
            
            // AI GIẢI THÍCH / BAREME TỰ LUẬN
            let expContent = q.ai_explanation || (q.answers && q.answers.length > 0 && q.type === 'essay' ? q.answers[0].content : '');
            if(expContent) {
                let formattedExp = expContent.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
                html += `
                <div class="ai-explain-box mt-3 p-3 position-relative overflow-hidden">
                    <div class="d-flex gap-2 position-relative z-1">
                        <div class="ai-avatar flex-shrink-0 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 28px; height: 28px; font-size: 0.7rem;"><i class="bi bi-stars"></i></div>
                        <div>
                            <h6 class="fw-bold text-purple mb-1 text-uppercase letter-spacing-1" style="font-size: 0.7rem;">GIẢI THÍCH AI</h6>
                            <div class="text-muted fw-medium" style="font-size: 0.8rem; line-height: 1.5;">${formattedExp}</div>
                        </div>
                    </div>
                </div>`;
            }

            html += `</div>`;
        });
        
        html += '</div>';

        area.html(html);
        $('#lbl-question-count').text(`DANH SÁCH CÂU HỎI`);
        $('#badge-question-stats').text(`${quizQuestions.length} câu`);
        $('#ai_questions_data').val(JSON.stringify(quizQuestions));
    }

    $(document).on('click', '.btn-remove-q', function() {
        if(confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')) {
            quizQuestions.splice($(this).data('index'), 1);
            renderQuestionsUI();
        }
    });

    // ==========================================
    // MỞ MODAL SỬA CÂU HỎI
    // ==========================================
    $(document).on('click', '.btn-edit-q', function() {
        let idx = $(this).data('index');
        let q = quizQuestions[idx];
        
        $('#edit_q_index').val(idx);
        $('#edit_q_type').val(q.type);
        $('#edit_q_content').val(q.content);
        $('#edit_q_explanation').val(q.ai_explanation || (q.answers && q.type === 'essay' ? q.answers[0].content : ''));

        let ansHtml = '';
        if (q.type === 'multiple_choice') {
            $('#edit_mcq_area').show();
            q.answers.forEach(function(ans, aIndex) {
                let label = String.fromCharCode(65 + aIndex);
                let isCorrect = ans.is_correct === true || ans.is_correct === "true" || ans.is_correct === 1;
                let checked = isCorrect ? 'checked' : '';
                
                ansHtml += `
                    <div class="input-group custom-input-group teacher-edit-answer-group mb-2">
                        <span class="input-group-text">
                            <input class="form-check-input m-0 custom-radio-edit"
                                type="radio"
                                name="edit_correct"
                                value="${aIndex}"
                                ${checked}
                                id="edit_rad_${label}">
                        </span>

                        <span class="input-group-text fw-bold text-purple">
                            ${label}.
                        </span>

                        <input type="text"
                            class="form-control edit-ans-input"
                            value="${ans.content || ''}"
                            placeholder="Nhập đáp án ${label}...">
                    </div>
                `;
            });
            $('#edit_answers_container').html(ansHtml);
        } else {
            $('#edit_mcq_area').hide();
        }

        if (editModal) {
        editModal.show();
    }
    });

    // CẬP NHẬT LƯU CÂU HỎI
    $('#btn-update-question').click(function() {
        let idx = $('#edit_q_index').val();
        let q = quizQuestions[idx];
        
        q.content = $('#edit_q_content').val().trim();
        let explanation = $('#edit_q_explanation').val().trim();
        q.ai_explanation = explanation;

        if (q.type === 'multiple_choice') {
            let correctIdx = $('input[name="edit_correct"]:checked').val();
            $('.edit-ans-input').each(function(i) {
                q.answers[i].content = $(this).val().trim();
                q.answers[i].is_correct = (i == correctIdx);
            });
        } else {
            if(q.answers.length === 0) q.answers.push({});
            q.answers[0].content = explanation;
            q.answers[0].is_correct = true;
        }

        quizQuestions[idx] = q;
        renderQuestionsUI();
        if (editModal) {
            editModal.hide();
        }
    });

    $('#btn-add-manual').click(function() {
        let type = $('#exam_type').val();
        let content = $('#manual_q_content').val().trim();

        if (!content) { alert('Vui lòng nhập nội dung câu hỏi!'); return; }

        let newQuestion = { type: type, content: content, answers: [], ai_explanation: '' };

        if (type === 'multiple_choice') {
            let isValid = true;
            let correctIdx = $('input[name="manual_correct"]:checked').val();
            
            $('.manual-ans-input').each(function(idx) {
                let ansText = $(this).val().trim();
                if(!ansText) isValid = false;
                newQuestion.answers.push({ content: ansText, is_correct: (idx == correctIdx) });
            });

            if(!isValid) { alert('Vui lòng nhập đủ 4 đáp án!'); return; }
        } else {
            let essayAns = $('#manual_e_answer').val().trim();
            newQuestion.answers.push({ content: essayAns ? essayAns : 'Học viên tự làm.', is_correct: true });
            newQuestion.ai_explanation = essayAns;
        }

        quizQuestions.push(newQuestion);
        renderQuestionsUI();

        $('#manual_q_content').val('');
        $('.manual-ans-input').val('');
        $('#manual_e_answer').val('');
        $('input[name="manual_correct"][value="0"]').prop('checked', true);
    });

    $('#btn-generate-ai').click(async function() {
        let docId = $('#document_id').val();
        let qCount = Math.max(1, Math.min(50, parseInt($('#question_count').val() || '10', 10) || 10));
        let currentType = $('#exam_type').val();
        let batchSize = currentType === 'essay' ? 5 : 10;
        let totalBatches = Math.ceil(qCount / batchSize);
        let maxBatches = totalBatches + 3;

        if(!docId) { alert('Vui lòng chọn tài liệu để AI phân tích!'); return; }

        $('#question_count').val(qCount);

        let $btn = $(this); 
        let originalText = $btn.html();
        
        $btn.html('<span class="spinner-border spinner-border-sm"></span> Đang phân tích...').prop('disabled', true);

        const renderGeneratingState = function(batchNumber, generatedCount) {
            const visibleTotalBatches = Math.max(totalBatches, batchNumber);

            $('#preview-questions-area').html(`
                <div class="py-5 text-center bg-light rounded-3 mt-3">
                    <div class="spinner-border text-purple mb-2" style="width: 2rem; height: 2rem;" role="status"></div>
                    <h6 class="fw-bold text-purple fs-6">Đang sinh câu hỏi...</h6>
                    <p class="text-muted small mb-1" style="font-size: 0.8rem;">Đợt ${batchNumber}/${visibleTotalBatches} - đã tạo ${generatedCount}/${qCount} câu không trùng</p>
                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">Tối đa 50 câu/lần, hệ thống tự chia nhỏ và lọc câu trùng</p>
                </div>
            `);
        };

        try {
            const ajaxUrl = $('#studentExamForm').data('ajax-url');
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const generatedQuestions = [];
            const normalizeQuestionContent = (content) => String(content || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd')
                .replace(/Đ/g, 'd')
                .toLowerCase()
                .replace(/[^a-z0-9\s]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();

            const getExcludedQuestions = () => quizQuestions
                .concat(generatedQuestions)
                .map(q => q.content)
                .filter(Boolean);

            if (!ajaxUrl) {
                throw new Error('Đường dẫn generate AI không hợp lệ.');
            }

            for (let batch = 1; generatedQuestions.length < qCount && batch <= maxBatches; batch++) {
                if (generatedQuestions.length >= qCount) break;

                let batchQuestionCount = Math.min(batchSize, qCount - generatedQuestions.length);
                if (batchQuestionCount <= 0) break;

                renderGeneratingState(batch, generatedQuestions.length);

                const response = await fetch(ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        document_id: docId,
                        question_count: batchQuestionCount,
                        exam_type: currentType,
                        exclude_questions: getExcludedQuestions().slice(-80)
                    })
                });

                const responseText = await response.text();
                let resData = null;

                try {
                    resData = responseText ? JSON.parse(responseText) : null;
                } catch (parseError) {
                    throw new Error('Máy chủ trả về dữ liệu không hợp lệ. Có thể request AI đã bị timeout, vui lòng thử lại sau.');
                }

                if (!response.ok || !resData || !resData.success) {
                    throw new Error(resData?.message || 'Lỗi kết nối AI. Vui lòng thử lại sau.');
                }

                let batchData = (resData.data || []).map(q => ({...q, type: q.type || currentType}));
                const seenKeys = new Set(getExcludedQuestions().map(normalizeQuestionContent));
                const uniqueBatchData = batchData.filter(q => {
                    const key = normalizeQuestionContent(q.content);

                    if (!key || seenKeys.has(key)) {
                        return false;
                    }

                    seenKeys.add(key);
                    return true;
                });

                generatedQuestions.push(...uniqueBatchData);
            }

            if (generatedQuestions.length === 0) {
                throw new Error('AI chưa tạo được câu hỏi hợp lệ.');
            }

            if (generatedQuestions.length < qCount) {
                throw new Error(`AI chỉ tạo được ${generatedQuestions.length}/${qCount} câu không trùng. Tài liệu có thể chưa đủ ý mới, vui lòng giảm số câu hoặc đổi tài liệu.`);
            }

            quizQuestions = quizQuestions.concat(generatedQuestions.slice(0, qCount));
            renderQuestionsUI();
        } catch (error) {
            console.error('Lỗi AI Generate:', error);

            $('#preview-questions-area').html(`
                    <div class="py-4 text-center bg-danger bg-opacity-10 text-danger rounded-3 mt-3">
                        <i class="bi bi-exclamation-triangle-fill fs-3 mb-2 d-block"></i>
                        <h6 class="fw-bold fs-6">Thất bại</h6>
                    <p class="small fw-medium mb-0 px-3">${error.message || 'Lỗi kết nối AI. Vui lòng thử lại sau.'}</p>
                    </div>
                `);
        } finally {
            $btn.html(originalText).prop('disabled', false);
        }
    });

    $('#studentExamForm').submit(function(e) {
        if (quizQuestions.length === 0) {
            e.preventDefault();
            alert('Bạn chưa có câu hỏi nào. Hãy dùng AI hoặc Nhập tay!');
            return false;
        }
        $('#btn-save-exam').html('<span class="spinner-border spinner-border-sm"></span> Đang lưu...').prop('disabled', true);
    });
});
