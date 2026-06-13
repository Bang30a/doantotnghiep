<?php
namespace App\Services\Admin;

use App\Models\Prompt;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class PromptService
{
    public function getAllPrompts()
    {
        // Vẫn giữ logic check an toàn của bác
        if (Schema::hasTable('prompts')) {
            return Prompt::latest()->get();
        }
        return collect([]); 
    }

    public function createPrompt(array $data)
    {
        $payload = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'prompt_text' => $data['content'],
            'model_type' => $data['model'] ?? 'gpt-3.5-turbo', // Default nếu không truyền
            'status' => $data['status'] ?? 1,
        ];

        if (Schema::hasColumn('prompts', 'exam_type')) {
            $payload['exam_type'] = $data['exam_type'] ?? 'both';
        }

        $prompt = Prompt::create($payload);

        $this->logActivity('admin_added_prompt', 'Thêm cấu hình AI', 'đã thêm một Prompt mới', $prompt->name, 'bi-robot', 'info');

        return $prompt;
    }

    public function updatePrompt($id, array $data)
    {
        $prompt = Prompt::findOrFail($id);
        
        $payload = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'prompt_text' => $data['content'],
            'model_type' => $data['model'] ?? $prompt->model_type,
            'status' => $data['status'] ?? $prompt->status,
        ];

        if (Schema::hasColumn('prompts', 'exam_type')) {
            $payload['exam_type'] = $data['exam_type'] ?? ($prompt->exam_type ?? 'both');
        }

        $prompt->update($payload);

        $this->logActivity('admin_updated_prompt', 'Cập nhật cấu hình AI', 'đã chỉnh sửa Prompt', $prompt->name, 'bi-pencil-square', 'primary');

        return $prompt;
    }

    public function deletePrompt($id)
    {
        $prompt = Prompt::find($id);
        
        if ($prompt) {
            $promptName = $prompt->name;
            $prompt->delete();

            $this->logActivity('admin_deleted_prompt', 'Xóa cấu hình AI', 'đã xóa vĩnh viễn Prompt', $promptName, 'bi-trash3-fill', 'danger');
        }
    }

    private function logActivity($type, $title, $actionText, $promptName, $icon, $color)
    {
        ActivityLog::create([
            'type' => $type,
            'title' => $title,
            'description' => 'Quản trị viên <strong>' . Auth::user()->name . '</strong> ' . $actionText . ': <span class="text-' . $color . ' fw-bold">"' . $promptName . '"</span>.',
            'icon_class' => $icon,
            'color_theme' => $color
        ]);
    }
}
