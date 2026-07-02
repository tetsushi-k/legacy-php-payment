# Reverse Engineering: アンチパターン一覧

> `legacy/` に意図的に残している「PHP 5.6 時代の典型的な問題」。面談で Before として語る材料。

| # | アンチパターン | 該当ファイル | 近代化での対応 |
|---|---|---|---|
| 1 | グローバル変数 `$conn` | `legacy/lib/db.php` | `Database::connection()` シングルトン |
| 2 | SQL 文字列連結 | `legacy/public/login.php`, `orders.php` | PDO プリペアドステートメント |
| 3 | addslashes のみのエスケープ | `legacy/public/login.php` | パラメータバインド |
| 4 | die() でエラー終了 | 各 legacy ファイル | 例外 + HTTP ステータスコード |
| 5 | HTML/PHP 混在 | `legacy/public/*.php` | テンプレート分離 (`modern/templates/`) |
| 6 | 権限チェック散在 | `orders.php`, `pay.php` | `RbacPolicy` に集約 |
| 7 | トランザクションなし決済 | `legacy/public/pay.php` | `PaymentService` + `FOR UPDATE` |
| 8 | 監査ログなし | 全体 | `AuditLogger` + `audit_logs` テーブル |
| 9 | マジックナンバー的コメント | `legacy/lib/config.php` | 型付き Enum 相当の文字列定数（近代化版） |
| 10 | require チェーン | `legacy/public/*.php` | Composer PSR-4 オートロード |

## 復元プロセス（AI-DLC）

1. `legacy/` 全ファイルを AI に読み込ませ、ユースケースを抽出
2. 上記アンチパターンを分類・リスト化
3. [spec-restored.md](spec-restored.md) と照合し、仕様の抜け漏れを確認
4. ユーザー（開発者）が承認後、Inception へ進行

## 判断ログ

- **2026-07-02**: 題材は `laravel-payment-api` と同一 EC ドメイン（案A）で確定
- **2026-07-02**: レガシー版は PHP 7.4 + mysqli で再現（PHP 5.6 の mysql_* は現行環境で非対応のため）
- **2026-07-02**: 決済はモックのまま（Stripe 連携は laravel-payment-api 側で証明済み）
