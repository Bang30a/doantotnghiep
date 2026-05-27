<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Http\Requests\Student\UploadDocumentRequest as StudentUploadDocumentRequest;
use App\Http\Requests\Teacher\UploadDocumentRequest as TeacherUploadDocumentRequest;
use App\Services\Shared\DocumentService;

use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    // ==========================================
    // MODULE HỌC VIÊN
    // ==========================================
    public function index()
    {
        $documents = Document::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('dashboards.student.student_upload', compact('documents'));
    }

    public function store(StudentUploadDocumentRequest $request)
    {
        if ($request->hasFile('document')) {
            $this->documentService->uploadDocument($request->file('document'), $request->validated(), 'student');
            return back()->with('success', 'Đã tải tài liệu lên thành công!');
        }
        return back()->with('error', 'Có lỗi xảy ra trong quá trình xử lý tệp.');
    }
    public function destroy($id)
    {
        $document = \App\Models\Document::findOrFail($id);

        if ($document->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa tài liệu này!');
        }
        $this->documentService->deleteDocument($document);

        return redirect()->back()->with('success', 'Đã xóa tài liệu thành công!');
    }

    // ==========================================
    // MODULE GIẢNG VIÊN
    // ==========================================
    public function teacherIndex()
    {
        $user = Auth::user();
        $documents = Document::where('user_id', $user->id)->latest()->paginate(12);
        
        $totalSize = Document::where('user_id', $user->id)->sum('file_size');
        $totalSizeMB = number_format($totalSize / 1048576, 2);

        return view('dashboards.teacher.teacher_documents', compact('documents', 'totalSizeMB'));
    }

    public function teacherStore(TeacherUploadDocumentRequest $request)
    {
        $this->documentService->uploadDocument($request->file('document'), $request->validated(), 'teacher');
        return back()->with('success', 'Đã tải tài liệu lên thành công!');
    }

    public function teacherDestroy($id)
    {
        $document = Document::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $this->documentService->deleteDocument($document);

        return back()->with('success', 'Đã xóa tài liệu thành công!');
    }

    public function teacherPreview($id)
    {
        $document = Document::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        $filePath = $this->documentService->getPreviewPath($document->file_path);

        if ($filePath) {
            return response()->file($filePath);
        }

        return abort(404, 'Không tìm thấy file thực tế trên hệ thống ổ cứng.');
    }
}