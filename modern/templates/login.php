<?php
/** @var string $error */
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン - Modern EC</title>
</head>
<body>
    <h1>Modern EC ログイン</h1>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post">
        <label>メール: <input type="email" name="email" value="test@example.com"></label><br>
        <label>パスワード: <input type="password" name="password" value="password123"></label><br>
        <button type="submit">ログイン</button>
    </form>
</body>
</html>
