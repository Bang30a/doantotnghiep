<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    protected $table = 'prompts';
    protected $fillable = [
        'name', 'description', 'prompt_text', 'model_type', 'status'
    ];
}
