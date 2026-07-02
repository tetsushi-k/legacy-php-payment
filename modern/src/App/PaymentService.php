<?php

declare(strict_types=1);

namespace App;

use PDO;

final readonly class PaymentService
{
    public function __construct(
        private PDO $pdo,
        private AuditLogger $auditLogger,
        private RbacPolicy $rbac
    ) {
    }

    /**
     * @param array{id: int, email: string, role: string} $sessionUser
     */
    public function pay(int $orderId, array $sessionUser): void
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, user_id, amount, status FROM orders WHERE id = :id FOR UPDATE'
            );
            $stmt->execute(['id' => $orderId]);
            $order = $stmt->fetch();

            if (!$order) {
                throw new \RuntimeException('注文が見つかりません');
            }

            if (!$this->rbac->canAccessOrder($sessionUser, (int)$order['user_id'])) {
                throw new \RuntimeException('この注文にアクセスする権限がありません');
            }

            if ($order['status'] !== 'pending') {
                throw new \RuntimeException('この注文は既に処理済みです');
            }

            $before = ['status' => $order['status']];

            $update = $this->pdo->prepare(
                "UPDATE orders SET status = 'paid' WHERE id = :id AND status = 'pending'"
            );
            $update->execute(['id' => $orderId]);

            $after = ['status' => 'paid'];

            $this->auditLogger->log(
                (int)$sessionUser['id'],
                'payment.completed',
                'order',
                $orderId,
                $before,
                $after
            );

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
