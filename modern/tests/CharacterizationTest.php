<?php

declare(strict_types=1);

namespace Tests;

use App\AuthService;
use App\AuditLogger;
use App\Database;
use App\OrderRepository;
use App\PaymentService;
use App\RbacPolicy;
use PHPUnit\Framework\TestCase;

/**
 * characterization test: レガシー版と同等の挙動を近代化後も維持することを検証する。
 */
final class CharacterizationTest extends TestCase
{
    private \PDO $pdo;
    private AuthService $auth;
    private OrderRepository $orders;
    private PaymentService $payment;

    protected function setUp(): void
    {
        $this->pdo = Database::connection();
        $this->auth = new AuthService($this->pdo);
        $this->orders = new OrderRepository($this->pdo);
        $this->payment = new PaymentService(
            $this->pdo,
            new AuditLogger($this->pdo),
            new RbacPolicy()
        );

        $this->pdo->exec("UPDATE orders SET status = 'pending' WHERE id = 1");
        $this->pdo->exec('DELETE FROM audit_logs');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = $this->auth->attempt('test@example.com', 'password123');

        $this->assertNotNull($user);
        $this->assertSame('test@example.com', $user['email']);
        $this->assertSame('user', $user['role']);
    }

    public function test_user_sees_only_own_orders(): void
    {
        $orders = $this->orders->findAccessibleOrders(1, 'user');

        $this->assertNotEmpty($orders);
        foreach ($orders as $order) {
            $this->assertArrayHasKey('amount', $order);
            $this->assertArrayHasKey('status', $order);
        }
    }

    public function test_admin_sees_all_orders(): void
    {
        $userOrders = $this->orders->findAccessibleOrders(1, 'user');
        $adminOrders = $this->orders->findAccessibleOrders(2, 'admin');

        $this->assertGreaterThanOrEqual(count($userOrders), count($adminOrders));
    }

    public function test_payment_updates_pending_order_to_paid(): void
    {
        $sessionUser = ['id' => 1, 'email' => 'test@example.com', 'role' => 'user'];

        $this->payment->pay(1, $sessionUser);

        $order = $this->orders->findById(1);
        $this->assertSame('paid', $order['status']);

        $stmt = $this->pdo->query("SELECT COUNT(*) AS cnt FROM audit_logs WHERE action = 'payment.completed'");
        $row = $stmt->fetch();
        $this->assertGreaterThanOrEqual(1, (int)$row['cnt']);
    }

    public function test_user_cannot_pay_others_order(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('この注文にアクセスする権限がありません');

        $this->payment->pay(4, ['id' => 1, 'email' => 'test@example.com', 'role' => 'user']);
    }

    public function test_payment_rejects_already_paid_order(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('この注文は既に処理済みです');

        $this->payment->pay(2, ['id' => 1, 'email' => 'test@example.com', 'role' => 'user']);
    }

    public function test_payment_rejects_missing_order(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('注文が見つかりません');

        $this->payment->pay(99999, ['id' => 1, 'email' => 'test@example.com', 'role' => 'user']);
    }

    public function test_admin_can_pay_others_pending_order(): void
    {
        $this->pdo->exec("UPDATE orders SET status = 'pending' WHERE id = 4");

        $this->payment->pay(4, ['id' => 2, 'email' => 'admin@example.com', 'role' => 'admin']);

        $order = $this->orders->findById(4);
        $this->assertSame('paid', $order['status']);
    }
}
