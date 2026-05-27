<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $guarded = [];

    // Đề thi của giảng viên nào
    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Đề thi thuộc tài liệu nào
    public function document() {
        return $this->belongsTo(Document::class);
    }

    // Danh sách câu hỏi của đề
    public function questions() {
        return $this->hasMany(Question::class);
    }

    // Kết quả thi của các học viên 
    public function results() {
        return $this->hasMany(Result::class);
    }
    
    // Đề thi này thuộc về lớp học nào
    public function classroom() {
        return $this->belongsTo(Classroom::class);
    }
    protected $fillable = [
        'teacher_id', 
        'classroom_id', 
        'document_id', 
        'title', 
        'subject', 
        'duration',
        'deadline'
    ];
    
}