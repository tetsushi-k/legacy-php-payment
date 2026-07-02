# Cursor Cloud Agents セットアップ手順

`legacy-php-payment` を Cloud Agents でスキャフォールド・初期実装するための設定ガイドです。

## 1. 前提条件

- Cursor **Pro** プラン（$20/月）
- GitHub アカウント（リポジトリ: `tetsushi-k/legacy-php-payment` を想定）
- 本リポジトリを GitHub に push 済みであること

## 2. スペンド上限の設定（推奨: $20/月）

1. Cursor を開く → **Settings** → **Billing** → **On-demand usage** を有効化
2. **Spend limit** を **$20** に設定（初月。1プロジェクト完了後に $10〜30 で調整）
3. モデルは **Composer 2.5** をデフォルトに（Opus 等の高額モデルはローカル設計レビュー時のみ）

> Cloud Agents はプラン込みの API 枠を先に消費し、超過分のみ従量課金されます。  
> スペンド上限により意図しない課金を防げます。

## 3. GitHub 接続

1. **Settings** → **Cloud Agents** → **GitHub** を接続
2. リポジトリ `legacy-php-payment` へのアクセスを許可
3. ブランチ `main` をデフォルトに設定

## 4. 環境設定（environment.json）

本リポジトリの [`.cursor/environment.json`](../.cursor/environment.json) に以下を定義済みです。

- インストールコマンド（`composer install`）
- 起動コマンド（`docker compose up -d`）
- テストコマンド（`make test`）

Cloud Agent がリポを clone した際、この設定に従って環境を構築します。

## 5. クラウドに任せる / ローカルでやる の切り分け

| タスク | 担当 | 理由 |
|---|---|---|
| リポ骨格・Docker 雛形 | **Cloud Agent** | ボイラープレート生成に最適 |
| Rector/PHPStan 設定雛形 | **Cloud Agent** | 定型ファイル |
| Reverse Engineering（仕様復元） | **ローカル + AI-DLC** | 設計判断の核心 |
| 決済ロジック・冪等性 | **ローカル** | 面談で説明する箇所 |
| characterization test | **ローカル** | 挙動固定は自分で確認 |
| README・面談トーク | **ローカル** | ナラティブの品質管理 |

## 6. Cloud Agent への指示例

```
legacy-php-payment リポジトリで、EC ドメインのレガシー PHP 決済アプリの
Docker 骨格を整備してください。

- legacy/ に PHP 7.4 + mysqli のレガシーコード（既存を尊重）
- modern/ に PHP 8.3 の近代化版
- docker-compose.yml で legacy:8080 / modern:8081 / MySQL
- make setup で DB 初期化とシードが完結するように

設計判断は aidlc-docs/ を参照。Never Vibe Code 原則に従い、
不明点はドキュメントに質問を残してから実装してください。
```

## 7. 課金の目安

| 用途 | 月額目安 |
|---|---|
| 週1〜2回のスキャフォールド | $10〜15 |
| 毎日クラウドで実装 | $30〜50 |

経費処理: [expense-allocation.md](../../../independence/guides/expense-allocation.md) どおり Cursor は家事按分 70%。
