<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'source',
        'quiz_answers',
        'name',
        'phone',
        'comment',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'gclid',
        'yclid',
        'ip',
        'qualification_status',
        'quality_status',
        'conversion_sent',
    ];

    protected function casts(): array
    {
        return [
            'quiz_answers' => 'array',
            'conversion_sent' => 'boolean',
        ];
    }
}
