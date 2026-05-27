<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePromptRequest;

use App\Services\Admin\PromptService;

class PromptController extends Controller
{
    protected $promptService;

    public function __construct(PromptService $promptService)
    {
        $this->promptService = $promptService;
    }

    public function index()
    {
        $prompts = $this->promptService->getAllPrompts();
        
        return view('dashboards.admin.admin_prompts', compact('prompts'));
    }

    public function store(StorePromptRequest $request)
    {
        $this->promptService->createPrompt($request->validated());

        return back()->with('success', 'Đã thêm cấu hình Prompt mới thành công!');
    }

    public function update(StorePromptRequest $request, $id)
    {
        $this->promptService->updatePrompt($id, $request->validated());

        return back()->with('success', 'Đã cập nhật cấu hình Prompt thành công!');
    }

    public function destroy($id)
    {
        $this->promptService->deletePrompt($id);

        return back()->with('success', 'Đã xóa cấu hình Prompt khỏi hệ thống!');
    }
}