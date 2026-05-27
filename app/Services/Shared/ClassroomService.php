<?php

namespace App\Services\Shared;

use App\Models\Classroom;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ClassroomService
{
    public function createClassroom(array $data)
    {
        $classroom = Classroom::create([
            'name' => $data['name'],
            'code' => strtoupper(Str::random(6)),
            'teacher_id' => Auth::id(),
            'status' => 1,
        ]);

        $this->logActivity(
            'classroom_created',
            'Giang vien tao lop hoc',
            'da tao lop hoc moi',
            $classroom->name,
            'bi-diagram-3-fill',
            'purple'
        );

        return $classroom;
    }

    public function joinClassroom($code, $user)
    {
        $code = strtoupper(trim($code));

        $classroom = Classroom::where('code', $code)->first();

        if (!$classroom) {
            throw new \Exception('Ma lop khong ton tai!');
        }

        if ($classroom->status == 0) {
            throw new \Exception('Lop hoc nay da bi khoa, khong the tham gia!');
        }

        $isJoined = $user->classrooms()
            ->where('classrooms.id', $classroom->id)
            ->exists();

        if ($isJoined) {
            throw new \Exception('Ban da tham gia lop hoc nay roi!');
        }

        $user->classrooms()->attach($classroom->id);

        $this->logActivity(
            'classroom_joined',
            'Hoc vien tham gia lop',
            'da tham gia vao lop',
            $classroom->name,
            'bi-door-open-fill',
            'info'
        );

        return $classroom;
    }

    private function logActivity($type, $title, $actionText, $targetName, $icon, $color)
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $roleText = $user->role === 'teacher' ? 'Giang vien' : 'Hoc vien';
        $textClass = $user->role === 'teacher' ? 'text-primary' : 'text-info';

        ActivityLog::create([
            'type' => $type,
            'title' => $title,
            'description' => $roleText . ' <strong>' . $user->name . '</strong> ' . $actionText . ' <span class="' . $textClass . ' fw-bold">"' . $targetName . '"</span>.',
            'icon_class' => $icon,
            'color_theme' => $color
        ]);
    }
}