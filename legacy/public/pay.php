<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
require_once __DIR__ . '/../lib/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = (int)($_GET['order_id'] ?? 0);
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($order_id <= 0) {
    die('不正な注文ID');
}

// アンチパターン: 権限チェックのコピペ（orders.php と重複）
$sql = "SELECT id, user_id, amount, status FROM orders WHERE id = $order_id";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die('注文取得失敗: ' . mysqli_error($conn));
}

$order = mysqli_fetch_assoc($result);
if (!$order) {
    die('注文が見つかりません');
}

if ($role != 'admin' && (int)$order['user_id'] !== (int)$user_id) {
    die('この注文にアクセスする権限がありません');
}

if ($order['status'] !== 'pending') {
    die('この注文は既に処理済みです');
}

// モック決済: Stripe 連携なし。ステータスを paid に更新するだけ
$update = "UPDATE orders SET status = 'paid' WHERE id = $order_id AND status = 'pending'";
if (!mysqli_query($conn, $update)) {
    die('決済更新失敗: ' . mysqli_error($conn));
}

header('Location: orders.php?paid=1');
exit;
