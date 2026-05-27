<?php
namespace App\Http\Requests\Shared;
use Illuminate\Foundation\Http\FormRequest;

class JoinClassroomRequest extends FormRequest
{
    public function authorize() { return true; }
    public function rules()
    {
        return [ 'code' => 'required|string|exists:classrooms,code' ];
    }
    public function messages()
    {
        return [ 'code.exists' => 'Mã lớp học không tồn tại!' ];
    }
}