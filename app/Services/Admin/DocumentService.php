<?php

namespace App\Services\Admin;

use App\Models\Document;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class DocumentService
{
    /**
     * Lấy danh sách tài liệu có tìm kiếm
     */
    public function getPaginatedDocuments($searchQuery = null, $perPage = 10)
    {
        $query = Document::with('user');

        if (!empty($searchQuery)) {
            $query->where('title', 'like', '%' . $searchQuery . '%')
                  ->orWhere('file_path', 'like', '%' . $searchQuery . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Radar quét tìm đường dẫn file thực tế
     */
    public function findActualFilePath($dbPath)
    {
        if (empty($dbPath)) return null;

        $dbPath = ltrim($dbPath, '/');

        $possiblePaths = [
            storage_path('app/public/' . $dbPath), 
            storage_path('app/' . $dbPath),
            public_path($dbPath),                  
            public_path('storage/' . $dbPath),     
            public_path('uploads/' . $dbPath)
        ];

        foreach ($possiblePaths as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Xóa file vật lý và record trong database
     */
    public function deleteDocument($id)
    {
        // Dùng Eloquent thay vì DB::table
        $document = Document::find($id); 
        
        if (!$document) return false;

        $docTitle = $document->title;
        $actualPath = $this->findActualFilePath($document->file_path);
        
        // Xóa file thật
        if ($actualPath) {
            File::delete($actualPath);
        }
        
        // Xóa trong DB
        $document->delete();

        // Ghi Log
        ActivityLog::create([
            'type' => 'admin_deleted_document',
            'title' => 'Admin xóa tài liệu',
            'description' => 'Quản trị viên <strong>' . Auth::user()->name . '</strong> đã xóa vĩnh viễn tài liệu <span class="text-danger fw-bold">"' . $docTitle . '"</span> khỏi hệ thống.',
            'icon_class' => 'bi-file-earmark-x-fill',
            'color_theme' => 'danger'
        ]);

        return true;
    }
}