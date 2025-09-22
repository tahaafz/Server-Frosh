<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name','email','password',
        'telegram_user_id','telegram_chat_id',
        'tg_current_state','tg_data','tg_last_message_id',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tg_data' => 'array',
    ];
}
