<?php
// アンチパターン: グローバル変数で設定を保持
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'legacy_payment';
$DB_USER = getenv('DB_USER') ?: 'legacy_user';
$DB_PASS = getenv('DB_PASS') ?: 'legacy_password';

// 注文ステータス（マジックナンバー的な定数なし）
// 0=pending, 1=paid, 2=failed は使わず文字列だが散在
