<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Import các Model liên quan để tránh lỗi
use App\Models\Classroom;
use App\Models\Document;
use App\Models\Exam;
use App\Models\Result;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'teacher' hoặc 'student'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // --------------------------------------------------------
    // CÁC MỐI QUAN HỆ (RELATIONSHIPS) ĐÃ ĐƯỢC CHUẨN HÓA LẠI
    // --------------------------------------------------------

    /**
     * Các lớp học do Giảng viên này TẠO RA
     */
    public function taughtClassrooms() {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    /**
     * Các lớp học mà Học viên này THAM GIA
     * (Đã đổi tên thành 'classrooms' để khớp với Controller)
     */
    public function classrooms() {
        return $this->belongsToMany(Classroom::class, 'classroom_user', 'user_id', 'classroom_id')
                    ->withTimestamps(); // Ghi nhận thời gian gia nhập lớp
    }

    /**
     * Tài liệu học viên đã tải lên
     */
    public function documents() {
        return $this->hasMany(Document::class);
    }

    /**
     * Đề thi Giảng viên đã tạo
     */
    public function exams() {
        return $this->hasMany(Exam::class, 'teacher_id');
    }

    /**
     * Kết quả làm bài thi của Học viên
     * (Đã đổi tên thành 'results' và trỏ tới model Result để khớp Controller)
     */
    public function results() {
        return $this->hasMany(Result::class, 'user_id');
    }
}