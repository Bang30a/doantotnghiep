<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'name',
        'code',
        'teacher_id',
        'status',
    ];

    // Lop hoc do 1 giang vien tao
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Lop hoc co nhieu de thi
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    // Lop hoc co nhieu hoc vien tham gia
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}