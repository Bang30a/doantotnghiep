<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $guarded = [];
    public function getTimeAgoAttribute()
    {
        return Carbon::parse($this->created_at)->locale('vi')->diffForHumans();
    }
    protected $fillable = [
        'type', 
        'title', 
        'description', 
        'icon_class', 
        'color_theme'
    ];
}