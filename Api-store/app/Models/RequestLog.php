<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
    protected $table = 'request_logs';

    protected $fillable = [
        'method',
        'path',
        'status_code',
        'ip',
        'user_agent',
        'user_id',
    ];
}


