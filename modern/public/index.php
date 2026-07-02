<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use App\AuthService;
use App\AuditLogger;
use App\Database;
use App\OrderRepository;
use App\PaymentService;
use App\RbacPolicy;

session_start();

$pdo = Database::connection();
$auth = new AuthService($pdo);
$orders = new OrderRepository($pdo);
$audit = new AuditLogger($pdo);
$rbac = new RbacPolicy();
$payment = new PaymentService($pdo, $audit, $rbac);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

try {
    match ($path) {
        '/', '/index.php' => redirect('/login.php'),
        '/login.php' => handleLogin($auth),
        '/logout.php' => handleLogout(),
        '/orders.php' => handleOrders($orders),
        '/pay.php' => handlePay($payment),
        default => throw new RuntimeException('Not Found', 404),
    };
} catch (RuntimeException $e) {
    $code = $e->getCode() >= 400 ? $e->getCode() : 500;
    http_response_code($code);
    echo htmlspecialchars($e->getMessage());
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/**
 * @return array{id: int, email: string, role: string}
 */
function requireAuth(): array
{
    if (!isset($_SESSION['user_id'])) {
        redirect('/login.php');
    }

    return [
        'id' => (int)$_SESSION['user_id'],
        'email' => (string)$_SESSION['email'],
        'role' => (string)$_SESSION['role'],
    ];
}

function handleLogin(AuthService $auth): void
{
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $auth->attempt(
            (string)($_POST['email'] ?? ''),
            (string)($_POST['password'] ?? '')
        );

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            redirect('/orders.php');
        }

        $error = 'メールアドレスまたはパスワードが正しくありません';
    }

    render('login', ['error' => $error]);
}

function handleLogout(): never
{
    session_destroy();
    redirect('/login.php');
}

function handleOrders(OrderRepository $orders): void
{
    $user = requireAuth();
    $orderList = $orders->findAccessibleOrders($user['id'], $user['role']);

    render('orders', [
        'user' => $user,
        'orders' => $orderList,
        'paid' => isset($_GET['paid']),
    ]);
}

function handlePay(PaymentService $payment): void
{
    $user = requireAuth();
    $orderId = (int)($_GET['order_id'] ?? 0);

    if ($orderId <= 0) {
        throw new RuntimeException('不正な注文ID', 400);
    }

    $payment->pay($orderId, $user);
    redirect('/orders.php?paid=1');
}

/**
 * @param array<string, mixed> $data
 */
function render(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require __DIR__ . '/../templates/' . $template . '.php';
}
