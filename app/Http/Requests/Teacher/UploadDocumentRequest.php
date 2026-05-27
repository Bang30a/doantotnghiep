<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'document' => 'required|mimes:pdf,doc,docx,txt|max:10240', // Max 10MB
            'title' => 'nullable|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'document.required' => 'Vui lòng chọn một file.',
            'document.mimes' => 'Chỉ hỗ trợ file PDF, Word hoặc TXT.',
            'document.max' => 'Dung lượng file tối đa là 10MB.'
        ];
    }
}