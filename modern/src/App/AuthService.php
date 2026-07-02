<?php

declare(strict_types=1);

namespace App;

use PDO;

final readonly class AuthService
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return array{id: int, email: string, role: string}|null
     */
    public function attempt(string $email, string $password): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, password_hash, role FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            return null;
        }

        return [
            'id' => (int)$user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
    }
}
