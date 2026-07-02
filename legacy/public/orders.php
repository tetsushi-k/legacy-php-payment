<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
require_once __DIR__ . '/../lib/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// アンチパターン: 権限チェックが各画面に散在（admin は全件、user は自分のみ）
if ($role == 'admin') {
    $sql = "SELECT o.id, o.amount, o.status, o.created_at, u.email AS user_email
            FROM orders o
            JOIN users u ON u.id = o.user_id
            ORDER BY o.id DESC";
} else {
    $sql = "SELECT o.id, o.amount, o.status, o.created_at, u.email AS user_email
            FROM orders o
            JOIN users u ON u.id = o.user_id
            WHERE o.user_id = $user_id
            ORDER BY o.id DESC";
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    die('注文取得失敗: ' . mysqli_error($conn));
}

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>注文一覧 - Legacy EC</title>
</head>
<body>
    <h1>注文一覧（Legacy）</h1>
    <p>ログイン中: <?php echo htmlspecialchars($_SESSION['email']); ?> (<?php echo htmlspecialchars($role); ?>)</p>
    <p><a href="logout.php">ログアウト</a></p>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>ユーザー</th>
            <th>金額</th>
            <th>ステータス</th>
            <th>操作</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo (int)$order['id']; ?></td>
            <td><?php echo htmlspecialchars($order['user_email']); ?></td>
            <td><?php echo number_format((int)$order['amount']); ?>円</td>
            <td><?php echo htmlspecialchars($order['status']); ?></td>
            <td>
                <?php if ($order['status'] === 'pending'): ?>
                    <a href="pay.php?order_id=<?php echo (int)$order['id']; ?>">決済する</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
