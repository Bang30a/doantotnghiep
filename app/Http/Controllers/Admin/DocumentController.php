<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller; 
use App\Models\Document;
use App\Services\Admin\DocumentService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    // ==========================================
    // Hiển thị danh sách tài liệu
    // ==========================================
    public function index(Request $request)
    {
        // 1. Lấy số lượng hiển thị từ URL (mặc định là 10)
        $perPage = $request->get('per_page', 10);

        // 2. Truyền $perPage vào Service
        $documents = $this->documentService->getPaginatedDocuments($request->search, $perPage);

        return view('dashboards.admin.admin_documents', compact('documents'));
    }

    // ==========================================
    // 1. Xem trước tài liệu (Mở tab mới)
    // ==========================================
    public function preview($id)
    {
        $document = Document::findOrFail($id);
        
        // Dùng chung hàm tìm file giống y hệt nút Download cho chuẩn xác tuyệt đối
        $actualPath = $this->documentService->findActualFilePath($document->file_path ?? '');

        if (!$actualPath || !file_exists($actualPath)) {
            return back()->with('error', 'Không tìm thấy file tài liệu trên hệ thống!');
        }

        // Lấy đúng đuôi file từ file thật
        $extension = strtolower(pathinfo($actualPath, PATHINFO_EXTENSION));

        // Các định dạng trình duyệt hỗ trợ xem trực tiếp
        $viewableExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'txt'];

        if (in_array($extension, $viewableExtensions)) {
            // Đọc được -> Hiển thị trên tab mới (Thêm header inline để ép trình duyệt mở thay vì tải)
            return response()->file($actualPath, [
                'Content-Disposition' => 'inline; filename="' . ($document->title ?? 'Tai_lieu') . '.' . $extension . '"'
            ]);
        } else {
            // Trình duyệt KHÔNG đọc được (doc, docx, xls...)
            // Thay vì báo lỗi bắt người ta quay lại, ép tải về luôn với TÊN CHUẨN cho xịn!
            $downloadName = ($document->title ?? 'Tai_lieu') . '.' . $extension;
            return response()->download($actualPath, $downloadName);
        }
    }

    // ==========================================
    // 2. Tải xuống tài liệu
    // ==========================================
    public function download($id)
    {
        $document = Document::find($id);
        $actualPath = $this->documentService->findActualFilePath($document->file_path ?? '');

        if (!$document || !$actualPath) {
            $pathInDB = $document->file_path ?? 'Trống';
            return back()->with('error', 'Không tìm thấy file trên hệ thống để tải! (Đường dẫn DB: ' . $pathInDB . ')');
        }

        $extension = pathinfo($actualPath, PATHINFO_EXTENSION);
        $downloadName = ($document->title ?? 'Tai_lieu') . '.' . $extension;

        return response()->download($actualPath, $downloadName);
    }

    // ==========================================
    // 3. Xóa vĩnh viễn tài liệu
    // ==========================================
    public function destroy($id)
    {
        $deleted = $this->documentService->deleteDocument($id);

        if ($deleted) {
            return back()->with('success', 'Đã xóa tài liệu vĩnh viễn khỏi hệ thống!');
        }

        return back()->with('error', 'Tài liệu không tồn tại hoặc đã bị xóa.');
    }
}