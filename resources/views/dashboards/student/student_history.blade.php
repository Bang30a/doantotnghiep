@extends('layouts.student.student_app')

@section('title', 'Lịch sử làm bài')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/student/student_history.css') }}">
@endpush

@section('content')
    @php
        $historyResults = collect($results ?? []);
        $realCount = $historyResults->count();
        $realHighestScore = 0;
        $realTotalScore = 0;
        $subjectCounts = [];

        foreach ($historyResults as $historyResult) {
            $exam = $historyResult->exam;
            $questions = ($exam && $exam->questions) ? $exam->questions : collect();
            $hasEssay = $questions->where('type', 'essay')->count() > 0;
            $totalQ = max(1, intval($historyResult->total_questions ?: $questions->count() ?: 1));
            $rawScore = floatval($historyResult->score ?? 0);
            $score10 = $hasEssay ? $rawScore : (($rawScore / $totalQ) * 10);
            $score10 = max(0, min(10, $score10));
            $subject = $exam->subject ?? ($exam->classroom->name ?? 'Môn chung');

            $realHighestScore = max($realHighestScore, $score10);
            $realTotalScore += $score10;
            $subjectCounts[$subject] = ($subjectCounts[$subject] ?? 0) + 1;
        }

        $realAverageScore = $realCount > 0 ? $realTotalScore / $realCount : 0;
        $scoreRate = $realCount > 0 ? min(100, round(($realAverageScore / 10) * 100)) : 0;
    @endphp

    <div class="history-modern-page">
        <div class="student-page-heading history-hero mb-4 mt-3">
            <div class="history-hero-main">
                <div class="history-hero-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1 theme-text-dark">Lịch sử làm bài</h3>
                    <p class="mb-0">Theo dõi tiến độ, điểm số và kết quả học tập của bạn</p>
                </div>
            </div>

            <div class="history-hero-badge">
                <span>{{ $realCount }}</span>
                <small>lượt làm</small>
            </div>
        </div>

        <div class="history-stat-grid mb-4">
            <div class="history-stat-card history-stat-primary">
                <div class="history-stat-icon"><i class="bi bi-file-earmark-check"></i></div>
                <p>Lượt làm bài</p>
                <h4>{{ $realCount }}</h4>
            </div>

            <div class="history-stat-card history-stat-amber">
                <div class="history-stat-icon"><i class="bi bi-bullseye"></i></div>
                <p>Điểm trung bình</p>
                <h4>{{ number_format($realAverageScore, 1) }}<span>/10</span></h4>
            </div>

            <div class="history-stat-card history-stat-success">
                <div class="history-stat-icon"><i class="bi bi-trophy"></i></div>
                <p>Điểm cao nhất</p>
                <h4>{{ number_format($realHighestScore, 1) }}<span>/10</span></h4>
            </div>

            <div class="history-stat-card history-stat-blue">
                <div class="history-stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <p>Tỷ lệ điểm đạt</p>
                <h4>{{ $scoreRate }}<span>%</span></h4>
            </div>
        </div>

        <div class="history-filter-panel mb-4">
            <div class="history-search-row">
                <div class="history-search-box">
                    <i class="bi bi-search"></i>
                    <input type="search" id="historySearch" placeholder="Tìm kiếm bài kiểm tra...">
                </div>

                <select id="historySort" class="history-sort-select" aria-label="Sắp xếp lịch sử làm bài">
                    <option value="newest">Mới nhất</option>
                    <option value="score_desc">Điểm cao nhất</option>
                    <option value="score_asc">Điểm thấp nhất</option>
                </select>
            </div>

            <div class="history-filter-chips" id="historySubjectFilters">
                <button type="button" class="history-filter-chip active" data-subject="all">
                    Tất cả <span>{{ $realCount }}</span>
                </button>

                @foreach($subjectCounts as $subjectName => $count)
                    <button type="button" class="history-filter-chip" data-subject="{{ md5($subjectName) }}">
                        {{ $subjectName }} <span>{{ $count }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="history-results-list" id="historyResultsList">
            @forelse($historyResults as $result)
                @php
                    $exam = $result->exam;
                    $questions = ($exam && $exam->questions) ? $exam->questions : collect();
                    $essayCount = $questions->where('type', 'essay')->count();
                    $hasEssay = $essayCount > 0;
                    $totalQ = max(1, intval($result->total_questions ?: $questions->count() ?: 1));
                    $answeredCount = collect($result->studentAnswers ?? [])->count();
                    $completionRate = round(($answeredCount / $totalQ) * 100);
                    $rawScore = floatval($result->score ?? 0);
                    $score10 = $hasEssay ? $rawScore : (($rawScore / $totalQ) * 10);
                    $score10 = max(0, min(10, $score10));
                    $subject = $exam->subject ?? ($exam->classroom->name ?? 'Môn chung');
                    $title = $exam->title ?? 'Bài thi đã xóa';
                    $duration = $exam && $exam->duration ? $exam->duration . ' phút' : 'Không rõ';
                    $typeText = $hasEssay
                        ? ($essayCount === $questions->count() ? 'Tự luận' : 'Hỗn hợp')
                        : 'Trắc nghiệm';
                    $correctText = $answeredCount === 0
                        ? 'Bỏ trống toàn bộ bài'
                        : $answeredCount . '/' . $totalQ . ' câu đã trả lời';
                    $accuracyText = $hasEssay
                        ? number_format($score10, 1) . '/10 điểm'
                        : intval($rawScore) . '/' . $totalQ . ' câu đúng';
                    $scoreTone = $score10 >= 8 ? 'success' : ($score10 >= 5 ? 'warning' : 'danger');
                    $dateValue = $result->created_at ? $result->created_at->timestamp : 0;
                @endphp

                <article class="history-exam-card history-score-{{ $scoreTone }}"
                         data-title="{{ mb_strtolower($title) }}"
                         data-subject="{{ md5($subject) }}"
                         data-score="{{ number_format($score10, 2, '.', '') }}"
                         data-date="{{ $dateValue }}">
                    <div class="history-card-top">
                        <div class="history-exam-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>

                        <div class="history-exam-title">
                            <h5>{{ $title }}</h5>
                            <div class="history-meta-pills">
                                <span class="history-subject-pill">{{ $subject }}</span>
                                <span><i class="bi bi-list-check"></i>{{ $totalQ }} câu</span>
                                <span><i class="bi bi-clock"></i>{{ $duration }}</span>
                                <span><i class="bi bi-ui-checks"></i>{{ $typeText }}</span>
                            </div>
                        </div>

                        <div class="history-score-block">
                            <strong>{{ number_format($score10, 1) }}</strong>
                            <span>/ 10 điểm</span>
                        </div>
                    </div>

                    <div class="history-card-metrics">
                        <div class="history-metric history-metric-accuracy">
                            <span><i class="bi bi-bullseye"></i>{{ $hasEssay ? 'Điểm đạt' : 'Số câu đúng' }}</span>
                            <strong>{{ $accuracyText }}</strong>
                        </div>
                        <div class="history-metric history-metric-date">
                            <span><i class="bi bi-calendar-check"></i>Ngày làm</span>
                            <strong>{{ $result->created_at ? $result->created_at->format('d/m/Y') : '--/--/----' }}</strong>
                        </div>
                        <div class="history-metric history-metric-time">
                            <span><i class="bi bi-alarm"></i>Thời gian nộp</span>
                            <strong>{{ $result->created_at ? $result->created_at->format('H:i') : '--:--' }}</strong>
                        </div>
                    </div>

                    <div class="history-progress-row">
                        <div>
                            <span>Tiến độ hoàn thành</span>
                            <strong>{{ $correctText }}</strong>
                        </div>
                        <div class="history-progress">
                            <span style="width: {{ min(100, max(0, $completionRate)) }}%"></span>
                        </div>
                    </div>

                    <div class="history-card-action">
                        @if($exam)
                            <a href="{{ route('exams.result', ['id' => $exam->id, 'result_id' => $result->id]) }}" class="history-detail-btn">
                                Xem chi tiết <i class="bi bi-chevron-right"></i>
                            </a>
                        @else
                            <button type="button" class="history-detail-btn disabled" disabled>
                                Đề đã bị xóa
                            </button>
                        @endif
                    </div>
                </article>
            @empty
                <div class="history-empty-state">
                    <div class="history-empty-icon"><i class="bi bi-inboxes"></i></div>
                    <h5>Chưa có lịch sử làm bài</h5>
                    <p>Bạn chưa hoàn thành bài kiểm tra nào. Hãy bắt đầu luyện tập để hệ thống ghi nhận kết quả.</p>
                    <a href="{{ route('student.exams.create', ['show_back' => 1]) }}" class="btn btn-theme-primary px-4 py-2 fw-bold">
                        <i class="bi bi-magic me-2"></i>Bắt đầu tự luyện
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('historySearch');
            const sortSelect = document.getElementById('historySort');
            const list = document.getElementById('historyResultsList');
            const cards = Array.from(list?.querySelectorAll('.history-exam-card') ?? []);
            const chips = Array.from(document.querySelectorAll('#historySubjectFilters .history-filter-chip'));
            let activeSubject = 'all';

            const applyHistoryFilters = () => {
                const keyword = (searchInput?.value || '').trim().toLowerCase();

                cards.forEach((card) => {
                    const matchesTitle = (card.dataset.title || '').includes(keyword);
                    const matchesSubject = activeSubject === 'all' || card.dataset.subject === activeSubject;
                    card.hidden = !(matchesTitle && matchesSubject);
                });

                const sortedCards = cards.slice().sort((a, b) => {
                    if (sortSelect?.value === 'score_desc') {
                        return Number(b.dataset.score || 0) - Number(a.dataset.score || 0);
                    }

                    if (sortSelect?.value === 'score_asc') {
                        return Number(a.dataset.score || 0) - Number(b.dataset.score || 0);
                    }

                    return Number(b.dataset.date || 0) - Number(a.dataset.date || 0);
                });

                sortedCards.forEach((card) => list.appendChild(card));
            };

            chips.forEach((chip) => {
                chip.addEventListener('click', () => {
                    chips.forEach((item) => item.classList.remove('active'));
                    chip.classList.add('active');
                    activeSubject = chip.dataset.subject || 'all';
                    applyHistoryFilters();
                });
            });

            searchInput?.addEventListener('input', applyHistoryFilters);
            sortSelect?.addEventListener('change', applyHistoryFilters);
        });
    </script>
@endpush
