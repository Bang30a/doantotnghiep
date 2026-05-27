<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    public function authorize()
    {
        // Trả về true nếu ai cũng có quyền submit form này (hoặc check role ở đây)
        return true; 
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'duration' => 'required|numeric|min:1',
            'ai_questions_data' => 'required',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'description' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Tiêu đề đề thi không được để trống.',
            'duration.required' => 'Thời gian thi không được để trống.',
            'ai_questions_data.required' => 'Dữ liệu câu hỏi bị trống.'
        ];
    }
}