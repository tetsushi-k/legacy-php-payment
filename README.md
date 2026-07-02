# legacy-php-payment

GitHub: [https://github.com/tetsushi-k/legacy-php-payment](https://github.com/tetsushi-k/legacy-php-payment)

`laravel-payment-api` の **レガシー前身** を再現し、AI-DLC の Reverse Engineering で仕様を復元したうえで PHP 8.3 に近代化するポートフォリオです。

> **主役の成果物は `aidlc-docs/`** — 面談ではコードより先に設計判断の証跡を画面共有してください。

---

## ① 概要

| フェーズ | ディレクトリ | 技術 |
|---|---|---|
| **Before** | `legacy/` | PHP 7.4（5.6 時代パターン再現）+ mysqli + 生 HTML |
| **After** | `modern/` | PHP 8.3 + PDO + PSR-4 + RBAC + 監査ログ |
| **プロセス** | `aidlc-docs/` | AI-DLC Reverse Engineering → Inception → Construction |

前作 [`laravel-payment-api`](../laravel-payment-api) との物語接続:
- 前作 = Brownfield で React SPA 化（新フロント追加）
- 本作 = 既存ロジックの言語バージョン移行・近代化（Reverse Engineering が主役）

---

## ② 使用技術

| カテゴリ | 技術 |
|---|---|
| レガシー | PHP 7.4 / mysqli / セッション認証 |
| 近代化 | PHP 8.3 / PDO / readonly class / フロントコントローラ |
| テスト | PHPUnit（characterization test） |
| 静的解析 | PHPStan L6 / Rector |
| インフラ | Docker Compose / MySQL 8.0 |
| AI プロセス | AI-DLC + Cursor Cloud Agents |

---

## ③ 設計上の工夫

### レガシー版（意図的アンチパターン）

- グローバル `$conn`、SQL 文字列連結、die() によるエラー終了
- 権限チェックが `orders.php` / `pay.php` に散在
- 詳細: [`aidlc-docs/inception/reverse-engineering/anti-patterns.md`](aidlc-docs/inception/reverse-engineering/anti-patterns.md)

### 近代化版

| 課題 | 対応 |
|---|---|
| SQL インジェクションリスク | PDO プリペアドステートメント |
| 権限チェック散在 | `RbacPolicy` に集約 |
| 決済の競合 | トランザクション + `FOR UPDATE` |
| 監査要件 | `AuditLogger` + `audit_logs` テーブル |
| リグレッション | characterization test で挙動固定 |

### AI 駆動開発ワークフロー

1. **Cursor Cloud Agents**: リポ骨格・Docker 雛形（[セットアップ手順](docs/CLOUD_AGENTS_SETUP.md)）
2. **AI-DLC Reverse Engineering**: コードから仕様復元 → `aidlc-docs/`
3. **ローカル Cursor**: 決済ロジック・テスト・README を深掘り

---

## ④ Before / After

| 観点 | legacy/ | modern/ |
|---|---|---|
| PHP | 7.4（5.6 パターン） | 8.3 |
| DB | mysqli + 文字列 SQL | PDO + プリペアド |
| 構成 | 画面ごと PHP+HTML | フロントコントローラ + テンプレート |
| 権限 | if 文散在 | `RbacPolicy` |
| 決済 | 単純 UPDATE | TX + FOR UPDATE + 監査ログ |
| テスト | なし | characterization test 8件 |
| 静的解析 | なし | PHPStan L6 + Rector |

---

## ⑤ ローカル起動方法

```bash
make setup    # 初回: Docker 起動 + composer install
make test     # characterization test
make phpstan  # 静的解析
```

| URL | 説明 |
|---|---|
| http://localhost:8080/login.php | レガシー版 |
| http://localhost:8081/login.php | 近代化版 |

**ログイン**: `test@example.com` / `password123`（user）  
**管理者**: `admin@example.com` / `password123`（admin）

---

## ⑥ 面談版（1分）— トークI

1. **何を作ったか（15秒）**  
   `laravel-payment-api` の前身を想定したレガシー PHP 決済アプリを、AI-DLC の Reverse Engineering で仕様復元してから PHP 8.3 に近代化しました。

2. **設計上の工夫（30秒）**  
   レガシー版は mysqli + 権限チェック散在など意図的アンチパターンを残し、近代化版では PDO・`RbacPolicy`・`AuditLogger` に集約しました。characterization test で移行前の挙動を固定してからリファクタしています。

3. **実務との接続（15秒）**  
   現職では CodeIgniter 2 のレガシー EC を8年担当してきました。仕様がドキュメント化されていないコードから、AI-DLC で仕様を復元してから安全に触る流れは現場の刷新案件にそのまま応用できます。

---

## ⑦ 関連リンク

| リポジトリ | 関係 |
|---|---|
| [`laravel-payment-api`](../laravel-payment-api) | 近代化後の Laravel + React SPA 版 |
| [`laravel-batch`](../laravel-batch) | 同一 EC ドメインのバッチ処理 |
| [`aidlc-docs/README.md`](aidlc-docs/README.md) | 面談での画面共有導線 |
| [`docs/CLOUD_AGENTS_SETUP.md`](docs/CLOUD_AGENTS_SETUP.md) | Cloud Agents 設定手順 |

---

## ⑧ ステータス

| 項目 | 状態 |
|---|---|
| レガシー版実装 | ✅ |
| AI-DLC ドキュメント | ✅ |
| 近代化 + characterization test | ✅ |
| PHPStan L6 / Rector | ✅ |

最終更新: 2026-07-02
