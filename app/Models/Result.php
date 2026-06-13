<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    // Bỏ qua lỗi Mass Assignment
    protected $guarded = [];
    
    // Liên kết: Điểm số này thuộc về Học viên nào
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Liên kết: Điểm số này thuộc về Đề thi nào
    public function exam() {
        return $this->belongsTo(Exam::class);
    }

    public function studentAnswers() {
        return $this->hasMany(StudentAnswer::class, 'exam_result_id');
    }
}
