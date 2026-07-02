# 03: 躓き・却下した案

推測で埋めず、ドキュメント根拠または今回セッションの実体験のみを記載。

## 既存ドキュメントに記録済み（Construction 時）

### PHP 5.6 mysql_* は再現不可

- **状況**: 題材は PHP 5.6 時代だが、現行 Docker 公式イメージに `mysql_*` 拡張がない
- **判断**: PHP 7.4 + mysqli で「5.6 時代パターン」を再現（[anti-patterns.md](../../inception/reverse-engineering/anti-patterns.md) 判断ログ 2026-07-02）
- **却下**: PHP 5.6 用カスタムイメージのビルド（ポートフォリオの再現性・面談説明コストが増える）

### Stripe 連携はスコープ外

- **判断**: レガシー版はモック決済。Stripe は `laravel-payment-api` で証明済み

## 今回セッション（Build and Test / 補強）で遭遇

### Docker compose コンテナ名衝突

- **症状**: `make test` 実行時 `legacy_payment_db` コンテナ名が既に使用中で `docker compose run` が失敗
- **原因**: 同一マシンに別 compose プロジェクト名で起動済みの DB コンテナが残存
- **回避**: 既存 `legacy-php-payment_legacy_network` 上で `docker run --rm legacy-php-payment-cli` を使用
- **CI への反映**: GitHub Actions はクリーン VM のため `docker compose up -d --wait` で問題なし（[04-hardening-log.md](04-hardening-log.md)）

### Rector 本適用なし

- **症状**: dry-run で変更提案ゼロ
- **判断**: 無理にルールを足して diff を作らない。PHP 8.3 向けセットは既に適用済みと判断
- **却下**: カスタム Rector ルールの追加（スコープ外）

### PHPStan L7 引き上げ

- **却下理由**: プラン上 L6 green 維持を優先。level 上げは別イテレーション

## 試して却下した案（今回）

| 案 | 却下理由 |
|---|---|
| `legacy/` の die() を直す | 意図的アンチパターン保持がプロジェクトルール |
| Phase 4 Laravel 化を同時進行 | migration-plan で将来項目。スコープ爆発防止 |
