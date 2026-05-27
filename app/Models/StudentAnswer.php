<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    protected $guarded = [];

    public function examResult() {
        return $this->belongsTo(ExamResult::class);
    }

    public function question() {
        return $this->belongsTo(Question::class);
    }

    public function answer() {
        return $this->belongsTo(Answer::class);
    }
}
