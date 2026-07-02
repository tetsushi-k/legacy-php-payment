<?php

declare(strict_types=1);

namespace App;

final class RbacPolicy
{
    /**
     * @param array{id?: int, role?: string} $sessionUser
     */
    public function canAccessOrder(array $sessionUser, int $orderOwnerId): bool
    {
        if (($sessionUser['role'] ?? '') === 'admin') {
            return true;
        }

        return (int)($sessionUser['id'] ?? 0) === $orderOwnerId;
    }
}
