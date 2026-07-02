# Inception: 段階移行計画

## 目標

PHP 5.6 時代のレガシーコード（`legacy/`）を PHP 8.3 の近代化版（`modern/`）へ安全に移行する。

## PHP 5→8 非互換リスト（本作で該当するもの）

| 項目 | レガシー | 近代化 |
|---|---|---|
| DB API | mysqli + 文字列 SQL | PDO + プリペアド |
| エラー処理 | die() | try/catch + RuntimeException |
| 型 | 型なし | strict_types + 型宣言 |
| オートロード | require_once | PSR-4 + Composer |
| セッション | 各画面で直接操作 | 認証ヘルパー経由 |

## 移行フェーズ

### Phase 1: 仕様固定（完了）

- [x] Reverse Engineering → `aidlc-docs/inception/reverse-engineering/`
- [x] レガシー版実装 → `legacy/`
- [x] characterization test → `modern/tests/CharacterizationTest.php`

### Phase 2: 機械的近代化（完了）

- [x] PSR-4 構成 + PDO 化
- [x] Rector / PHPStan 設定（`rector.php`, `phpstan.neon`）
- [x] RBAC 集約（`RbacPolicy`）
- [x] 監査ログ（`AuditLogger`）

### Phase 3: 検証（完了）

- [x] characterization test 全件パス
- [x] PHPStan level 6
- [x] Before/After README 整備

### Phase 4: 将来（第2段階・任意）

- [ ] Laravel 化（`laravel-payment-api` へ統合）
- [ ] Stripe 連携の復元

## 承認ゲート

| ゲート | 判断 | 承認者 |
|---|---|---|
| G1: 復元仕様 | spec-restored.md の内容でレガシー挙動を固定 | 開発者 |
| G2: 近代化方針 | PDO + サービス層 + RBAC + 監査ログ | 開発者 |
| G3: テスト合格 | characterization test 全件 green | CI / 手動 |

## Never Vibe Code 原則の適用

- コード直書き修正ではなく、本ドキュメント → 実装の順で進めた
- AI 生成コードは characterization test で挙動を検証してからマージ
