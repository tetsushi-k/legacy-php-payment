# 02: 移行チェンジログ（遡及）

`legacy/` と `modern/` の diff を [anti-patterns.md](../../inception/reverse-engineering/anti-patterns.md) の 10 項目と突合した記録です。

| # | アンチパターン | legacy の該当 | modern の対応 | 採用理由 | 検証方法 |
|---|---|---|---|---|---|
| 1 | グローバル `$conn` | `legacy/lib/db.php` | `Database::connection()` | 接続の単一化・テスト容易性 | characterization test（DB 操作全般） |
| 2 | SQL 文字列連結 | `legacy/public/login.php`, `orders.php`, `pay.php` | PDO プリペアド | SQLi リスク低減 | PHPStan + 手動コードレビュー |
| 3 | addslashes のみ | `legacy/public/login.php` | パラメータバインド | エスケープ依存を排除 | 同上 |
| 4 | die() でエラー終了 | 各 legacy ファイル | `RuntimeException` + HTTP ステータス | エラー経路の明示 | characterization test（例外メッセージ） |
| 5 | HTML/PHP 混在 | `legacy/public/*.php` | `modern/templates/` + フロントコントローラ | 責務分離 | 手動（8081 表示確認） |
| 6 | 権限チェック散在 | `orders.php`, `pay.php` | `RbacPolicy` | DRY・面談で説明しやすい | `test_user_sees_only_own_orders`, `test_admin_sees_all_orders`, `test_user_cannot_pay_others_order` |
| 7 | トランザクションなし決済 | `legacy/public/pay.php` | `PaymentService` + `FOR UPDATE` | 競合時の二重決済防止 | `test_payment_updates_pending_order_to_paid` |
| 8 | 監査ログなし | 全体 | `AuditLogger` + `audit_logs` | 決済の追跡可能性 | 決済テストで audit_logs 件数検証 |
| 9 | マジックナンバー的コメント | `legacy/lib/config.php` | 型付き・strict_types | PHP 8.3 の型安全性 | PHPStan L6 |
| 10 | require チェーン | `legacy/public/*.php` | Composer PSR-4 | オートロード標準化 | `composer.json` autoload |

## UC 対応マップ

| UC | legacy | modern | テスト |
|---|---|---|---|
| UC-1 ログイン | `login.php` + mysqli | `AuthService` | `test_user_can_login_with_valid_credentials` |
| UC-2 注文一覧 | `orders.php` + 権限 if | `OrderRepository` + `RbacPolicy` | user/admin 一覧テスト |
| UC-3 モック決済 | `pay.php` 単純 UPDATE | `PaymentService` TX + 監査 | 決済成功・権限拒否・paid 拒否・不存在 |

## 意図的に変えていないこと

- 決済はモックのまま（Stripe 連携なし）— `laravel-payment-api` 側で証明済み
- `legacy/` 自体はアンチパターンのまま保持（Before 断面）
