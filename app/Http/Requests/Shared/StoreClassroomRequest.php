<?php
namespace App\Http\Requests\Shared;
use Illuminate\Foundation\Http\FormRequest;

class StoreClassroomRequest extends FormRequest
{
    public function authorize() { return true; }
    public function rules()
    {
        return [ 'name' => 'required|string|max:255' ];
    }
}