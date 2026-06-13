/* ========================================================
   FILE: public/js/student/student_create_exam.js
   Xử lý giao diện tạo đề tự luyện bằng AI
======================================================== */

document.addEventListener('DOMContentLoaded', () => {
    const successModal = document.getElementById('successSaveModal');
    if (successModal && successModal.dataset.showOnLoad === 'true') {
        const modal = new bootstrap.Modal(successModal);
        modal.show();
    }

    let quizQuestions = [];

    const formElement = document.getElementById('studentExamForm');
    const area = document.getElementById('preview-questions-area');
    const lblCount = document.getElementById('lbl-question-count');
    const badgeStats = document.getElementById('badge-question-stats');
    const inputData = document.getElementById('ai_questions_data');
    const examTypeSelect = document.getElementById('exam_type');
    const btnSaveExam = document.getElementById('btn-save-exam');

    // ================================
    // 1. Đọc dữ liệu câu hỏi cũ nếu có
    // ================================
    if (formElement && formElement.dataset.existingQuestions) {
        try {
            quizQuestions = JSON.parse(formElement.dataset.existingQuestions || '[]');
        } catch (e) {
            console.error('Lỗi parse existing questions:', e);
            quizQuestions = [];
        }
    }

    // ================================
    // 2. Cập nhật hidden input trước khi lưu
    // ================================
    const syncQuestionData = () => {
        if (inputData) {
            inputData.value = JSON.stringify(quizQuestions);
        }
    };

    // ================================
    // 3. Render danh sách câu hỏi
    // ================================
    const renderQuestionsUI = () => {
        if (!area) return;

        if (quizQuestions.length === 0) {
            area.innerHTML = `
                <div class="empty-questions py-5 text-center rounded-4 bg-light border-dashed transition-all mt-2">
                    <div class="icon-wrapper-sm bg-white text-purple mx-auto rounded-circle mb-3 shadow-sm border" style="width: 56px; height: 56px; font-size: 1.6rem;">
                        <i class="bi bi-inboxes"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Chưa có câu hỏi nào</h5>
                    <p class="text-muted fw-medium mb-0">Sử dụng công cụ AI sinh tự động hoặc nhập thủ công ở cột bên phải</p>
                </div>
            `;

            if (lblCount) {
                lblCount.innerHTML = `
                    <div class="icon-bg-green"><i class="bi bi-card-checklist"></i></div>
                    <span style="font-size: 1.05rem;">DANH SÁCH CÂU HỎI</span>
                `;
            }

            if (badgeStats) badgeStats.textContent = '0 câu';
            syncQuestionData();
            return;
        }

        let html = '<div class="d-flex flex-column gap-3">';

        quizQuestions.forEach((q, index) => {
            const type = q.type || 'multiple_choice';

            const badgeType = type === 'essay'
                ? '<span class="badge bg-info text-dark ms-2 border border-info">Tự luận</span>'
                : '<span class="badge badge-custom-tracnghiem ms-2">Trắc nghiệm</span>';

            html += `
                <div class="question-item position-relative p-4 bg-white border rounded-4 shadow-sm"
                     style="border-left: 4px solid var(--theme-primary) !important;">

                    <div class="position-absolute top-0 end-0 m-3 d-flex gap-2 question-action-box">
                        <button type="button"
                                class="btn btn-sm btn-outline-primary btn-edit-q"
                                data-index="${index}"
                                title="Sửa câu này">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <button type="button"
                                class="btn btn-sm btn-outline-danger btn-remove-q"
                                data-index="${index}"
                                title="Xóa câu này">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>

                    <h6 class="fw-bold mb-3 pe-5 lh-base text-dark" style="padding-right: 90px;">
                        <span class="text-purple me-1">Câu ${index + 1}:</span>
                        ${q.content || ''}
                        ${badgeType}
                    </h6>
            `;

            if (type === 'multiple_choice' && Array.isArray(q.answers)) {
                html += `<div class="row g-2">`;

                q.answers.forEach((ans, aIndex) => {
                    const label = String.fromCharCode(65 + aIndex);
                    const isCorrect =
                        ans.is_correct === true ||
                        ans.is_correct === 1 ||
                        String(ans.is_correct) === 'true';

                    const correctClass = isCorrect
                        ? 'answer-correct bg-success bg-opacity-10 border-success text-success fw-bold'
                        : 'answer-option bg-light border-soft text-muted';

                    const icon = isCorrect
                        ? '<i class="bi bi-check-circle-fill ms-1"></i>'
                        : '';

                    html += `
                        <div class="col-md-6">
                            <div class="p-2 rounded-3 border ${correctClass}">
                                <strong>${label}.</strong> ${ans.content || ''} ${icon}
                            </div>
                        </div>
                    `;
                });

                html += `</div>`;
            }

            if (type === 'essay' && Array.isArray(q.answers) && q.answers.length > 0) {
                const rawText = q.answers[0].content || '';
                const formattedAnswer = rawText
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\n/g, '<br>');

                html += `
                    <div class="p-4 bg-light border rounded-3 text-dark mt-2" style="font-size: 0.95rem;">
                        <strong class="text-success fs-6">
                            <i class="bi bi-check2-square me-1"></i> Gợi ý đáp án:
                        </strong>
                        <div class="mt-2 text-muted" style="line-height: 1.8;">${formattedAnswer}</div>
                    </div>
                `;
            }

            if (q.ai_explanation) {
                const formattedExp = String(q.ai_explanation)
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\n/g, '<br>');

                html += `
                    <div class="mt-3 p-3 bg-purple-soft rounded-3 small theme-text-primary border border-purple-subtle">
                        <strong><i class="bi bi-robot"></i> AI Giải thích:</strong>
                        <span class="text-dark opacity-75">${formattedExp}</span>
                    </div>
                `;
            }

            html += `</div>`;
        });

        html += '</div>';

        area.innerHTML = html;

        if (lblCount) {
            lblCount.innerHTML = `
                <div class="icon-bg-green"><i class="bi bi-card-checklist"></i></div>
                <span style="font-size: 1.05rem;">Danh sách câu hỏi (${quizQuestions.length})</span>
            `;
        }

        if (badgeStats) badgeStats.textContent = `${quizQuestions.length} câu`;
        syncQuestionData();
    };

    renderQuestionsUI();

    // ================================
    // 4. Xóa / Sửa câu hỏi
    // ================================
    if (area) {
        area.addEventListener('click', (e) => {
            const btnRemove = e.target.closest('.btn-remove-q');
            const btnEdit = e.target.closest('.btn-edit-q');

            if (btnRemove) {
                e.preventDefault();
                e.stopPropagation();

                const idx = parseInt(btnRemove.dataset.index, 10);

                if (isNaN(idx)) return;

                if (confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')) {
                    quizQuestions.splice(idx, 1);
                    renderQuestionsUI();
                }

                return;
            }

            if (btnEdit) {
                e.preventDefault();
                e.stopPropagation();

                const idx = parseInt(btnEdit.dataset.index, 10);
                const q = quizQuestions[idx];

                if (isNaN(idx) || !q) return;

                const modalEl = document.getElementById('editQuestionModal');
                const inputIndex = document.getElementById('edit_q_index');
                const inputContent = document.getElementById('edit_q_content');

                // Nếu HTML chưa có modal thì không cho JS bị chết
                if (!modalEl || !inputIndex || !inputContent) {
                    alert('Thiếu modal sửa câu hỏi trong file Blade. Bạn cần thêm editQuestionModal vào HTML.');
                    return;
                }

                inputIndex.value = idx;
                inputContent.value = q.content || '';

                if ((q.type || 'multiple_choice') === 'multiple_choice' && Array.isArray(q.answers)) {
                    q.answers.forEach((ans, i) => {
                        if (i < 4) {
                            const ansInput = document.getElementById(`edit_ans_${i}`);
                            const correctRadio = document.querySelector(`input[name="edit_correct"][value="${i}"]`);

                            if (ansInput) ansInput.value = ans.content || '';

                            const isCorrect =
                                ans.is_correct === true ||
                                ans.is_correct === 1 ||
                                String(ans.is_correct) === 'true';

                            if (correctRadio && isCorrect) {
                                correctRadio.checked = true;
                            }
                        }
                    });
                }

                const editModal = new bootstrap.Modal(modalEl);
                editModal.show();
            }
        });
    }

    // ================================
    // 5. Lưu câu hỏi sau khi sửa modal
    // ================================
    const btnSaveEdit = document.getElementById('btn-save-edit-question');

    if (btnSaveEdit) {
        btnSaveEdit.addEventListener('click', (e) => {
            e.preventDefault();

            const idxInput = document.getElementById('edit_q_index');
            const contentInput = document.getElementById('edit_q_content');

            if (!idxInput || !contentInput) return;

            const idx = parseInt(idxInput.value, 10);
            const q = quizQuestions[idx];

            if (isNaN(idx) || !q) return;

            const newContent = contentInput.value.trim();

            if (!newContent) {
                alert('Nội dung câu hỏi không được để trống!');
                return;
            }

            q.content = newContent;

            if ((q.type || 'multiple_choice') === 'multiple_choice') {
                const checkedRadio = document.querySelector('input[name="edit_correct"]:checked');
                const correctIdx = checkedRadio ? parseInt(checkedRadio.value, 10) : 0;

                for (let i = 0; i < 4; i++) {
                    const ansInput = document.getElementById(`edit_ans_${i}`);

                    if (q.answers && q.answers[i] && ansInput) {
                        q.answers[i].content = ansInput.value.trim();
                        q.answers[i].is_correct = i === correctIdx;
                    }
                }
            }

            const modalEl = document.getElementById('editQuestionModal');
            if (modalEl) {
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
            }

            renderQuestionsUI();
        });
    }

    // ================================
    // 6. Đổi loại câu hỏi nhập tay
    // ================================
    const toggleManualArea = () => {
        const mcqArea = document.getElementById('manual_mcq_area');
        const essayArea = document.getElementById('manual_essay_area');

        if (!mcqArea || !essayArea || !examTypeSelect) return;

        if (examTypeSelect.value === 'essay') {
            mcqArea.classList.add('d-none');
            essayArea.classList.remove('d-none');
        } else {
            mcqArea.classList.remove('d-none');
            essayArea.classList.add('d-none');
        }
    };

    if (examTypeSelect) {
        examTypeSelect.addEventListener('change', toggleManualArea);
        toggleManualArea();
    }

    // ================================
    // 7. Thêm câu hỏi nhập tay
    // ================================
    const btnAddManual = document.getElementById('btn-add-manual');

    if (btnAddManual) {
        btnAddManual.addEventListener('click', (e) => {
            e.preventDefault();

            if (!examTypeSelect) return;

            const type = examTypeSelect.value;
            const contentInput = document.getElementById('manual_q_content');
            const content = contentInput ? contentInput.value.trim() : '';

            if (!content) {
                alert('Vui lòng nhập nội dung câu hỏi!');
                return;
            }

            const newQuestion = {
                type: type,
                content: content,
                answers: []
            };

            if (type === 'multiple_choice') {
                const answerInputs = document.querySelectorAll('.manual-ans-input');
                const correctRadio = document.querySelector('input[name="manual_correct"]:checked');
                const correctIdx = correctRadio ? parseInt(correctRadio.value, 10) : 0;

                if (answerInputs.length < 4) {
                    alert('Không tìm thấy đủ 4 ô đáp án.');
                    return;
                }

                let isValid = true;

                answerInputs.forEach((input, idx) => {
                    const ansText = input.value.trim();

                    if (!ansText) isValid = false;

                    newQuestion.answers.push({
                        content: ansText,
                        is_correct: idx === correctIdx
                    });
                });

                if (!isValid) {
                    alert('Vui lòng nhập đủ 4 đáp án!');
                    return;
                }
            } else {
                const essayAnsInput = document.getElementById('manual_e_answer');
                const essayAns = essayAnsInput ? essayAnsInput.value.trim() : '';

                newQuestion.answers.push({
                    content: essayAns || 'Học viên tự làm.',
                    is_correct: true
                });
            }

            quizQuestions.push(newQuestion);
            renderQuestionsUI();

            if (contentInput) contentInput.value = '';
            document.querySelectorAll('.manual-ans-input').forEach(input => input.value = '');

            const essayAnsInput = document.getElementById('manual_e_answer');
            if (essayAnsInput) essayAnsInput.value = '';

            const defaultRadio = document.querySelector('input[name="manual_correct"][value="0"]');
            if (defaultRadio) defaultRadio.checked = true;
        });
    }

    // ================================
    // 8. Gọi AI sinh câu hỏi
    // ================================
    const btnGenerateAI = document.getElementById('btn-generate-ai');

    if (btnGenerateAI) {
        btnGenerateAI.addEventListener('click', async function (e) {
            e.preventDefault();

            const docId = document.getElementById('document_id')?.value;
            const questionCountInput = document.getElementById('question_count');
            const qCount = Math.max(1, Math.min(50, parseInt(questionCountInput?.value || '10', 10) || 10));
            const currentType = examTypeSelect?.value || 'multiple_choice';
            const batchSize = currentType === 'essay' ? 5 : 10;
            const totalBatches = Math.ceil(qCount / batchSize);
            const maxBatches = totalBatches + 3;

            if (!docId) {
                alert('Vui lòng chọn tài liệu để AI phân tích!');
                return;
            }

            if (questionCountInput) {
                questionCountInput.value = qCount;
            }

            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang phân tích...';
            this.disabled = true;

            const renderGeneratingState = (batchNumber, generatedCount) => {
                if (!area) return;

                const visibleTotalBatches = Math.max(totalBatches, batchNumber);

                area.innerHTML = `
                    <div class="py-5 text-center bg-light rounded-3 mt-3">
                        <div class="spinner-border text-purple mb-2" style="width: 2rem; height: 2rem;" role="status"></div>
                        <h6 class="fw-bold text-purple fs-6">Đang sinh câu hỏi...</h6>
                        <p class="text-muted small mb-1" style="font-size: 0.8rem;">Đợt ${batchNumber}/${visibleTotalBatches} - đã tạo ${generatedCount}/${qCount} câu không trùng</p>
                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">Tối đa 50 câu/lần, hệ thống tự chia nhỏ và lọc câu trùng</p>
                    </div>
                `;
            };

            try {
                if (!formElement) {
                    throw new Error('Không tìm thấy form studentExamForm.');
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const ajaxUrl = formElement.dataset.ajaxUrl;

                if (!ajaxUrl || ajaxUrl === '#') {
                    throw new Error('Đường dẫn generate AI không hợp lệ.');
                }

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

                const getExcludedQuestions = () => [...quizQuestions, ...generatedQuestions]
                    .map(q => q.content)
                    .filter(Boolean);

                for (let batch = 1; generatedQuestions.length < qCount && batch <= maxBatches; batch++) {
                    if (generatedQuestions.length >= qCount) break;

                    const batchQuestionCount = Math.min(batchSize, qCount - generatedQuestions.length);
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

                    const batchData = (resData.data || []).map(q => ({
                        ...q,
                        type: q.type || currentType
                    }));

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

                quizQuestions = [...quizQuestions, ...generatedQuestions.slice(0, qCount)];
                renderQuestionsUI();

            } catch (error) {
                console.error('Lỗi AI Generate:', error);

                if (area) {
                    area.innerHTML = `
                        <div class="py-4 text-center bg-danger bg-opacity-10 text-danger rounded-3 mt-3">
                            <i class="bi bi-exclamation-triangle-fill fs-3 mb-2 d-block"></i>
                            <h6 class="fw-bold fs-6">Thất bại</h6>
                            <p class="small fw-medium mb-0 px-3">${error.message || 'Lỗi kết nối AI. Vui lòng thử lại sau.'}</p>
                        </div>
                    `;
                }
            } finally {
                this.innerHTML = originalText;
                this.disabled = false;
            }
        });
    }

    // ================================
    // 9. Lưu đề
    // ================================
    if (btnSaveExam && formElement) {
        btnSaveExam.addEventListener('click', (e) => {
            e.preventDefault();
            formElement.requestSubmit();
        });
    }

    if (formElement) {
        formElement.addEventListener('submit', (e) => {
            syncQuestionData();

            if (quizQuestions.length === 0) {
                e.preventDefault();
                alert('Bạn chưa có câu hỏi nào. Hãy dùng AI hoặc Nhập tay!');
                return false;
            }

            if (btnSaveExam) {
                btnSaveExam.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang lưu...';
                btnSaveExam.disabled = true;
            }
        });
    }

});
