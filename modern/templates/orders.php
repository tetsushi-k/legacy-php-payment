<?php
/** @var array $user */
/** @var array $orders */
/** @var bool $paid */
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>注文一覧 - Modern EC</title>
</head>
<body>
    <h1>注文一覧（Modern）</h1>
    <?php if (!empty($paid)): ?>
        <p style="color:green;">決済が完了しました</p>
    <?php endif; ?>
    <p>ログイン中: <?= htmlspecialchars($user['email']) ?> (<?= htmlspecialchars($user['role']) ?>)</p>
    <p><a href="/logout.php">ログアウト</a></p>
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
            <td><?= (int)$order['id'] ?></td>
            <td><?= htmlspecialchars($order['user_email']) ?></td>
            <td><?= number_format((int)$order['amount']) ?>円</td>
            <td><?= htmlspecialchars($order['status']) ?></td>
            <td>
                <?php if ($order['status'] === 'pending'): ?>
                    <a href="/pay.php?order_id=<?= (int)$order['id'] ?>">決済する</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
