<?php

declare(strict_types=1);

namespace App;

use PDO;

final readonly class AuditLogger
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @param array<string, mixed>|null $before
     * @param array<string, mixed>|null $after
     */
    public function log(
        int $userId,
        string $action,
        ?string $entityType,
        ?int $entityId,
        ?array $before,
        ?array $after
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO audit_logs (user_id, action, entity_type, entity_id, before_data, after_data)
             VALUES (:user_id, :action, :entity_type, :entity_id, :before_data, :after_data)'
        );

        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'before_data' => $before !== null ? json_encode($before, JSON_UNESCAPED_UNICODE) : null,
            'after_data' => $after !== null ? json_encode($after, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }
}
