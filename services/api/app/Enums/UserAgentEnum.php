<?php

namespace App\Enums;

/**
 *
 */
enum UserAgentEnum
{
    case LINUX_CHROME;

    public function value(): string
    {
        return match ($this) {
            UserAgentEnum::LINUX_CHROME => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36'
        };
    }
}
