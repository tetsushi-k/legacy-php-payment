# Construction: 近代化サマリー

## Before / After

| 観点 | legacy/ | modern/ |
|---|---|---|
| PHP | 7.4（5.6 パターン再現） | 8.3 |
| DB | mysqli + 文字列 SQL | PDO + プリペアド |
| 構成 | 画面ごとに PHP+HTML | フロントコントローラ + テンプレート |
| 権限 | if 文散在 | RbacPolicy |
| 決済 | 単純 UPDATE | トランザクション + FOR UPDATE + 監査ログ |
| テスト | なし | characterization test 8件 |
| 静的解析 | なし | PHPStan L6 + Rector |

## 追加した泥臭い要件

1. **RBAC 簡易版**: admin は全注文、user は自分のみ（`RbacPolicy`）
2. **監査ログ**: 決済完了時に before/after を `audit_logs` に記録
3. **エラーハンドリング**: die() → 例外 + HTTP ステータス

## ツール

| ツール | 用途 |
|---|---|
| PHPUnit | characterization test |
| Rector | PHP 8.3 向け機械的リファクタ |
| PHPStan | 静的型解析（level 6） |
| Cursor Cloud Agents | 骨格スキャフォールド（[docs/CLOUD_AGENTS_SETUP.md](../../docs/CLOUD_AGENTS_SETUP.md)） |
| AI-DLC | Reverse Engineering → Inception → Construction |

## 開発プロセス記録

| フェーズ | AI に任せた範囲 | 自分が判断した範囲 |
|---|---|---|
| スキャフォールド | Docker 骨格、ディレクトリ構成 | ドメイン選定、題材決定 |
| Reverse Engineering | コード読解・仕様ドラフト | 仕様承認、アンチパターン分類 |
| Construction | ボイラープレート生成 | 決済トランザクション設計、RBAC 方針 |
| テスト | テスト雛形 | 期待挙動の定義・承認 |
| Build and Test + ジャーナル | CI 雛形・upgrade-journal 遡及・テスト 3 件追加 | Proxy Approval を audit.md に記録（ローカル代理実行セッション） |
