<?php

declare(strict_types=1);

namespace App;

use PDO;

final readonly class OrderRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findAccessibleOrders(int $userId, string $role): array
    {
        if ($role === 'admin') {
            $stmt = $this->pdo->query(
                'SELECT o.id, o.amount, o.status, o.created_at, u.email AS user_email
                 FROM orders o
                 JOIN users u ON u.id = o.user_id
                 ORDER BY o.id DESC'
            );
            return $stmt->fetchAll();
        }

        $stmt = $this->pdo->prepare(
            'SELECT o.id, o.amount, o.status, o.created_at, u.email AS user_email
             FROM orders o
             JOIN users u ON u.id = o.user_id
             WHERE o.user_id = :user_id
             ORDER BY o.id DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, amount, status FROM orders WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch();

        return $order ?: null;
    }
}
