# Integration Test Instructions

本作はモノリシック PHP + 共有 MySQL のため、統合テストは **Docker 上の HTTP + DB** で手動確認します。

## 手動 E2E チェックリスト

### Legacy（8080）

1. `/login.php` で `test@example.com` / `password123` ログイン
2. 注文一覧に自分の注文のみ表示
3. pending 注文の決済リンクで paid に遷移

### Modern（8081）

1. 同上フローでログイン・一覧・決済
2. `audit_logs` に `payment.completed` が記録されること（DB 確認は任意）

## 自動化の範囲

- PHPUnit characterization test がサービス層の統合挙動をカバー
- GitHub Actions CI で test + PHPStan を自動実行（`.github/workflows/ci.yml`）

## 将来拡張

- Laravel 化（Phase 4）時に Feature Test / HTTP テストへ移行予定
