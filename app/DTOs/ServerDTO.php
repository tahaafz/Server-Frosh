<?php

namespace App\DTOs;

class ServerDTO
{
    public $user_id;
    public $server_id;
    public $name;
    public $ip_address;
    public $status;

    public function __construct($user_id, $server_id, $name, $ip_address, $status)
    {
        $this->user_id = $user_id;
        $this->server_id = $server_id;
        $this->name = $name;
        $this->ip_address = $ip_address;
        $this->status = $status;
    }

    // Convert the DTO to an array for easy access
    public function toArray()
    {
        return [
            'user_id' => $this->user_id,
            'server_id' => $this->server_id,
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'status' => $this->status,
        ];
    }
}
