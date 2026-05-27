<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Nếu đã có Middleware Admin bọc ở Route thì để true
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive,banned', // Đảm bảo status hợp lệ
            'password' => 'nullable|string|min:6', // Nếu có nhập pass thì phải >= 6 ký tự
        ];
    }
}