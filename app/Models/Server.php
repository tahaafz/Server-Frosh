<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = [
        'user_id', 'server_id', 'name', 'ip_address', 'status'
    ];

    // ارتباط با مدل User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
