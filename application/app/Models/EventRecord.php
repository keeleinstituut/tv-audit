<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventRecord extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'happened_at',

        'actor_pic',
        'actor_name',
        'actor_session',
        'actor_department_id',
        'actor_institution_id',
        'actor_institution_user_id',

        'action',
        'web_path',

        'path',
        'request_method',
        'request_query',
        'request_body',

        'response_status_code',
    ];

    protected $casts = [
        'response_body' => 'array',
        'request_body' => 'array',
        'request_query' => 'array',
        'happened_at' => 'datetime',
    ];
}
