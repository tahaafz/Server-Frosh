<?php

namespace App\DTOs;

class ServerCreateDTO
{
    public function __construct(
        public int    $user_id,
        public string $provider,     // e.g. 'gcore'
        public string $plan,         // e.g. 'g2s-shared-1-1-25'
        public string $region_id,    // e.g. '116'
        public string $os_image_id,  // e.g. ubuntu-22 id
        public string $vm_name,      // e.g. 501813541-ABC123
        public string $login_user,   // e.g. 'ubuntu'
        public string $login_pass    // random
    ) {}

    public static function fromArray(array $a): self
    {
        return new self(
            user_id:      $a['user_id'],
            provider:     $a['provider'],
            plan:         $a['plan'],
            region_id:    $a['region_id'],
            os_image_id:  $a['os_image_id'],
            vm_name:      $a['vm_name'],
            login_user:   $a['login_user'],
            login_pass:   $a['login_pass'],
        );
    }

    public function toArray(): array
    {
        return [
            'user_id'      => $this->user_id,
            'provider'     => $this->provider,
            'plan'         => $this->plan,
            'region_id'    => $this->region_id,
            'os_image_id'  => $this->os_image_id,
            'vm_name'      => $this->vm_name,
            'login_user'   => $this->login_user,
            'login_pass'   => $this->login_pass,
        ];
    }
}
