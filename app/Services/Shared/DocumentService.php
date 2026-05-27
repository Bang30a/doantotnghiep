<?php

namespace App\Services\Shared;

use App\Models\Document;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class DocumentService
{
    /**
     * Hàm xử lý upload dùng chung cho cả GV và Học viên
     */
    public function uploadDocument($file, array $data, $role)
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . uniqid() . '.' . $extension;
        
        $destinationPath = public_path('uploads/documents');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $fileName);
        $dbPath = 'uploads/documents/' . $fileName;
        $fileSize = filesize(public_path($dbPath));

        // Logic phân tách dữ liệu theo Role
        $title = $role === 'teacher' ? ($data['title'] ?? pathinfo($originalName, PATHINFO_FILENAME)) : $originalName;

        $document = Document::create([
            'user_id' => Auth::id(),
            'title' => $title,
            'subject' => $data['subject'] ?? null,
            'description' => $data['description'] ?? null,
            'file_path' => $dbPath,
            'file_size' => $fileSize,
            'file_type' => $extension,
            'status' => 'ready'
        ]);

        $this->logUploadActivity($document, $role);

        return $document;
    }

    public function deleteDocument(Document $document)
    {
        $fullFilePath = public_path($document->file_path);
        
        if (File::exists($fullFilePath)) {
            File::delete($fullFilePath);
        }
        
        $title = $document->title;
        $document->delete();

        ActivityLog::create([
            'type' => 'teacher_document_deleted',
            'title' => 'Giảng viên xóa tài liệu',
            'description' => 'Giảng viên <strong>' . Auth::user()->name . '</strong> đã xóa tài liệu <span class="text-danger fw-bold">"' . $title . '"</span>.',
            'icon_class' => 'bi-file-earmark-x',
            'color_theme' => 'danger'
        ]);
    }

    public function getPreviewPath($dbPath)
    {
        $cleanPath = ltrim($dbPath ?? '', '/');
        $possiblePaths = [
            storage_path('app/' . $cleanPath),
            storage_path('app/public/' . $cleanPath),
            public_path($cleanPath),
            public_path('storage/' . $cleanPath),
            public_path('uploads/' . $cleanPath)
        ];

        foreach ($possiblePaths as $path) {
            if (File::exists($path)) return $path;
        }

        return null; // Không tìm thấy
    }

    private function logUploadActivity(Document $document, $role)
    {
        $isTeacher = $role === 'teacher';
        ActivityLog::create([
            'type' => $isTeacher ? 'teacher_document_uploaded' : 'student_document_uploaded',
            'title' => $isTeacher ? 'Giảng viên tải tài liệu lên' : 'Học viên tải tài liệu lên',
            'description' => ($isTeacher ? 'Giảng viên ' : 'Học viên ') . '<strong>' . Auth::user()->name . '</strong> đã tải lên tài liệu <span class="text-primary fw-bold">"' . $document->title . '"</span>.',
            'icon_class' => $isTeacher ? 'bi-file-earmark-arrow-up' : 'bi-cloud-arrow-up-fill',
            'color_theme' => $isTeacher ? 'purple' : 'info'
        ]);
    }
}