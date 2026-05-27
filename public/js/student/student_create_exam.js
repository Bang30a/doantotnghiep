/* ========================================================
   FILE: public/js/student/student_create_exam.js
   Xu ly giao dien tao de tu luyen bang AI
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
    // 1. Doc du lieu cau hoi cu neu co
    // ================================
    if (formElement && formElement.dataset.existingQuestions) {
        try {
            quizQuestions = JSON.parse(formElement.dataset.existingQuestions || '[]');
        } catch (e) {
            console.error('Loi parse existing questions:', e);
            quizQuestions = [];
        }
    }

    // ================================
    // 2. Cap nhat hidden input truoc khi luu
    // ================================
    const syncQuestionData = () => {
        if (inputData) {
            inputData.value = JSON.stringify(quizQuestions);
        }
    };

    // ================================
    // 3. Render danh sach cau hoi
    // ================================
    const renderQuestionsUI = () => {
        if (!area) return;

        if (quizQuestions.length === 0) {
            area.innerHTML = `
                <div class="empty-questions py-5 text-center rounded-4 bg-light border-dashed transition-all mt-2">
                    <div class="icon-wrapper-sm bg-white text-purple mx-auto rounded-circle mb-3 shadow-sm border" style="width: 56px; height: 56px; font-size: 1.6rem;">
                        <i class="bi bi-inboxes"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Chua co cau hoi nao</h5>
                    <p class="text-muted fw-medium mb-0">Su dung cong cu AI sinh tu dong hoac nhap thu cong o cot ben phai</p>
                </div>
            `;

            if (lblCount) {
                lblCount.innerHTML = `
                    <div class="icon-bg-green"><i class="bi bi-card-checklist"></i></div>
                    <span style="font-size: 1.05rem;">DANH SACH CAU HOI</span>
                `;
            }

            if (badgeStats) badgeStats.textContent = '0 cau';
            syncQuestionData();
            return;
        }

        let html = '<div class="d-flex flex-column gap-3">';

        quizQuestions.forEach((q, index) => {
            const type = q.type || 'multiple_choice';

            const badgeType = type === 'essay'
                ? '<span class="badge bg-info text-dark ms-2 border border-info">Tu luan</span>'
                : '<span class="badge badge-custom-tracnghiem ms-2">Trac nghiem</span>';

            html += `
                <div class="question-item position-relative p-4 bg-white border rounded-4 shadow-sm"
                     style="border-left: 4px solid var(--theme-primary) !important;">

                    <div class="position-absolute top-0 end-0 m-3 d-flex gap-2 question-action-box">
                        <button type="button"
                                class="btn btn-sm btn-outline-primary btn-edit-q"
                                data-index="${index}"
                                title="Sua cau nay">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <button type="button"
                                class="btn btn-sm btn-outline-danger btn-remove-q"
                                data-index="${index}"
                                title="Xoa cau nay">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>

                    <h6 class="fw-bold mb-3 pe-5 lh-base text-dark" style="padding-right: 90px;">
                        <span class="text-purple me-1">Cau ${index + 1}:</span>
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
                            <i class="bi bi-check2-square me-1"></i> Goi y dap an:
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
                        <strong><i class="bi bi-robot"></i> AI Giai thich:</strong>
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
                <span style="font-size: 1.05rem;">Danh sach cau hoi (${quizQuestions.length})</span>
            `;
        }

        if (badgeStats) badgeStats.textContent = `${quizQuestions.length} cau`;
        syncQuestionData();
    };

    renderQuestionsUI();

    // ================================
    // 4. Xoa / Sua cau hoi
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

                if (confirm('Bac co chac muon xoa cau hoi nay khong?')) {
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

                // Neu HTML chua co modal thi khong cho JS bi chet
                if (!modalEl || !inputIndex || !inputContent) {
                    alert('Thieu modal sua cau hoi trong file Blade. Bac can them editQuestionModal vao HTML.');
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
    // 5. Luu cau hoi sau khi sua modal
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
                alert('Noi dung cau hoi khong duoc de trong!');
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
    // 6. Doi loai cau hoi nhap tay
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
    // 7. Them cau hoi nhap tay
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
                alert('Vui long nhap noi dung cau hoi!');
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
                    alert('Khong tim thay du 4 o dap an.');
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
                    alert('Vui long nhap du 4 dap an!');
                    return;
                }
            } else {
                const essayAnsInput = document.getElementById('manual_e_answer');
                const essayAns = essayAnsInput ? essayAnsInput.value.trim() : '';

                newQuestion.answers.push({
                    content: essayAns || 'Hoc vien tu lam.',
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
    // 8. Goi AI sinh cau hoi
    // ================================
    const btnGenerateAI = document.getElementById('btn-generate-ai');

    if (btnGenerateAI) {
        btnGenerateAI.addEventListener('click', async function (e) {
            e.preventDefault();

            const docId = document.getElementById('document_id')?.value;
            const qCount = document.getElementById('question_count')?.value || 10;
            const currentType = examTypeSelect?.value || 'multiple_choice';

            if (!docId) {
                alert('Vui long chon tai lieu de AI phan tich!');
                return;
            }

            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Dang phan tich...';
            this.disabled = true;

            if (area) {
                area.innerHTML = `
                    <div class="py-5 text-center">
                        <div class="spinner-border" style="width: 3rem; height: 3rem; color: var(--theme-primary);" role="status"></div>
                        <h6 class="mt-3 fw-bold theme-text-primary">AI dang doc tai lieu va soan cau hoi...</h6>
                        <p class="text-muted small">Qua trinh nay co the mat tu 10 - 30 giay.</p>
                    </div>
                `;
            }

            try {
                if (!formElement) {
                    throw new Error('Khong tim thay form studentExamForm.');
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const ajaxUrl = formElement.dataset.ajaxUrl;

                if (!ajaxUrl || ajaxUrl === '#') {
                    throw new Error('Duong dan generate AI khong hop le.');
                }

                const response = await fetch(ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        document_id: docId,
                        question_count: qCount,
                        type: currentType
                    })
                });

                const resData = await response.json();

                if (response.ok && resData.success) {
                    const aiData = (resData.data || []).map(q => ({
                        ...q,
                        type: q.type || currentType
                    }));

                    quizQuestions = [...quizQuestions, ...aiData];
                    renderQuestionsUI();
                } else {
                    throw new Error(resData.message || 'Loi he thong khi sinh cau hoi.');
                }

            } catch (error) {
                console.error('Loi AI Generate:', error);

                if (area) {
                    area.innerHTML = `
                        <div class="py-5 text-center text-danger">
                            <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                            <h6 class="mt-3 fw-bold">Tao cau hoi that bai</h6>
                            <p class="small">${error.message || 'Co loi xay ra khi goi AI.'}</p>
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
    // 9. Luu de
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
                alert('Ban chua co cau hoi nao. Hay dung AI hoac Nhap tay!');
                return false;
            }

            if (btnSaveExam) {
                btnSaveExam.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Dang luu...';
                btnSaveExam.disabled = true;
            }
        });
    }

});