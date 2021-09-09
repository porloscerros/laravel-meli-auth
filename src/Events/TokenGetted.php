<?php

namespace Porloscerros\Meli\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class TokenGetted
{
    use Dispatchable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }
} 
