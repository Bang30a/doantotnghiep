<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    protected $guarded = [];

    public function examResult() {
        return $this->belongsTo(Result::class, 'exam_result_id');
    }

    public function result() {
        return $this->belongsTo(Result::class, 'exam_result_id');
    }

    public function question() {
        return $this->belongsTo(Question::class);
    }

    public function answer() {
        return $this->belongsTo(Answer::class);
    }
}
