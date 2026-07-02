<?php
require_once __DIR__ . '/config.php';

// アンチパターン: グローバル接続を直接公開
global $conn;
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$conn) {
    die('DB接続失敗: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

function get_connection()
{
    global $conn;
    return $conn;
}
