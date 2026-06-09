<?php

namespace app\Domain\User;

class UserStatus
{
    public const ACTIVE = 'active';
    public const BLOCKED = 'blocked';

    public static function all(): array
    {
        return [
            self::ACTIVE,
            self::BLOCKED,
        ];
    }
}
