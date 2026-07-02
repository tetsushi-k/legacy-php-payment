# 01: ベースライン検証

補強作業（CI・テスト追加）**前**の検証結果です。

**実行環境**: Docker（`legacy-php-payment-cli` イメージ、既存 `legacy-php-payment_legacy_network`）  
**実行日**: 2026-07-02

## make test

| 項目 | 結果 |
|---|---|
| 成否 | 成功 |
| テスト件数 | 5 |
| アサーション | 17 |
| 出力要約 | `OK (5 tests, 17 assertions)` |

## make phpstan

| 項目 | 結果 |
|---|---|
| 成否 | 成功 |
| Level | 6 |
| 対象 | `modern/src`, `modern/public/index.php` |
| 指摘数 | 0 (`No errors`) |

## make rector（dry-run）

| 項目 | 結果 |
|---|---|
| 成否 | 成功（変更提案なし） |
| 出力要約 | `Rector is done!` — 7ファイル解析、diff なし |

## 補足: make setup / docker compose

同一マシンに別 compose プロジェクトの `legacy_payment_db` コンテナが存在する場合、`docker compose run` がコンテナ名衝突で失敗することがある。  
回避: 既存スタックを `docker compose up -d` で起動したうえで、同一ネットワーク上の cli イメージを `docker run` する（詳細は [03-stumbling-blocks.md](03-stumbling-blocks.md)）。

## ベースライン結論

- 近代化コードは characterization test・PHPStan・Rector の観点で **green**
- 不足していたのは CI、UC 未カバーのテスト、AI-DLC Build and Test 証跡
