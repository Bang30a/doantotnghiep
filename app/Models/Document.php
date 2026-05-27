<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = [];

    // Tài liệu do ai up
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Các đề thi được sinh ra từ tài liệu này
    public function exams() {
        return $this->hasMany(Exam::class);
    }
    protected $fillable = [
        'user_id',
        'title',
        'subject',      
        'description',
        'file_path',
        'file_size',
        'file_type',
        'status',
    ];
}
