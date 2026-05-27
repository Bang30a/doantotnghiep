<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreSelfPracticeRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'document_id' => 'nullable|exists:documents,id',
            'duration' => 'required|integer|min:1',
            'ai_questions_data' => 'required|string'
        ];
    }
}