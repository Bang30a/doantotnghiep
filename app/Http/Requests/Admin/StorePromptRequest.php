<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePromptRequest extends FormRequest
{
    /**
     * Cấp quyền cho mọi request chạy qua (Bắt buộc phải là true)
     */
    public function authorize() 
    { 
        return true; 
    }

    /**
     * Bộ luật kiểm tra dữ liệu đầu vào (Phải khớp 100% với thuộc tính name="" ở thẻ input)
     */
    public function rules()
    {
        return [
            'name'        => 'required|string|max:255',
            'model'       => 'required|string',
            'exam_type'   => 'required|in:multiple_choice,essay,both',
            'description' => 'nullable|string',
            'content'     => 'required|string', 
            'status'      => 'nullable|in:0,1' // Giới hạn chỉ nhận giá trị 0 hoặc 1 từ Radio button
        ];
    }

    /**
     * Việt hóa thông báo lỗi để hiển thị ra ngoài màn hình (nếu có lỗi)
     */
    public function messages()
    {
        return [
            'name.required'    => 'Vui lòng nhập Tên Prompt.',
            'model.required'   => 'Vui lòng chọn Model AI.',
            'exam_type.required' => 'Vui lòng chọn loại đề áp dụng cho Prompt.',
            'exam_type.in'      => 'Loại đề áp dụng cho Prompt không hợp lệ.',
            'content.required' => 'Vui lòng nhập Nội dung Prompt (Câu chỉ thị).',
            'status.in'        => 'Trạng thái cấu hình không hợp lệ.',
        ];
    }
}
