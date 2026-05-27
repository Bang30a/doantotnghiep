<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UploadDocumentRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $maxSizeMB = DB::table('settings')->where('key', 'max_upload_size')->value('value') ?? 10;
        $maxSizeKB = $maxSizeMB * 1024;

        return [
            'document' => 'required|file|mimes:pdf,doc,docx,txt|max:' . $maxSizeKB,
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string'
        ];
    }
}