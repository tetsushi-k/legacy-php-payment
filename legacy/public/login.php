<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
require_once __DIR__ . '/../lib/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // アンチパターン: addslashes のみで SQL を組み立て（プリペアドステートメントなし）
    $email = addslashes($email);
    $sql = "SELECT id, email, password_hash, role FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die('クエリ失敗: ' . mysqli_error($conn));
    }

    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        header('Location: orders.php');
        exit;
    }

    $error = 'メールアドレスまたはパスワードが正しくありません';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン - Legacy EC</title>
</head>
<body>
    <h1>Legacy EC ログイン</h1>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post">
        <label>メール: <input type="email" name="email" value="test@example.com"></label><br>
        <label>パスワード: <input type="password" name="password" value="password123"></label><br>
        <button type="submit">ログイン</button>
    </form>
</body>
</html>
