# Reverse Engineering: 復元仕様書

> AI-DLC Inception フェーズの成果物。`legacy/` のコードを読み解き、ビジネス仕様を復元した。

## ドメイン概要

EC サービスの最小決済フロー。`laravel-payment-api` の前身を想定。

## エンティティ

### users

| カラム | 型 | 説明 |
|---|---|---|
| id | INT | PK |
| email | VARCHAR(255) | ログインID（ユニーク） |
| password_hash | VARCHAR(255) | bcrypt |
| role | ENUM | `admin` \| `user` |

### orders

| カラム | 型 | 説明 |
|---|---|---|
| id | INT | PK |
| user_id | INT | FK → users |
| amount | INT | 金額（円） |
| status | ENUM | `pending` \| `paid` \| `failed` |

### audit_logs（近代化版で追加）

| カラム | 型 | 説明 |
|---|---|---|
| id | INT | PK |
| user_id | INT | 操作者 |
| action | VARCHAR(50) | 例: `payment.completed` |
| entity_type | VARCHAR(50) | 例: `order` |
| entity_id | INT | 対象ID |
| before_data | JSON | 変更前 |
| after_data | JSON | 変更後 |

## ユースケース

### UC-1: ログイン

- **入力**: email, password
- **処理**: users テーブルから email で検索 → password_verify
- **成功**: セッションに user_id, email, role を保存 → 注文一覧へ
- **失敗**: エラーメッセージ表示

### UC-2: 注文一覧

- **前提**: ログイン済み
- **admin**: 全ユーザーの注文を表示
- **user**: 自分の注文のみ表示
- **表示**: ID, ユーザーemail, 金額, ステータス, 操作（pending なら決済リンク）

### UC-3: モック決済

- **入力**: order_id（GET パラメータ）
- **前提**: ログイン済み、注文が pending、自分の注文（admin は全件可）
- **処理**: orders.status を `paid` に UPDATE
- **成功**: 注文一覧へリダイレクト
- **注意**: Stripe 連携なし（レガシー版はモック決済のみ）

## 状態遷移（orders.status）

```
pending → paid   （決済成功）
pending → failed （未実装・将来拡張）
```

## セキュリティ上の既知課題（レガシー版）

1. SQL に文字列連結（addslashes のみ）
2. 権限チェックが画面ごとに散在
3. die() によるエラー終了
4. 監査ログなし

→ 近代化版で解消（[migration-plan.md](../migration-plan.md) 参照）
