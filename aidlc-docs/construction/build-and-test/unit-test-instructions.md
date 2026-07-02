# Unit Test Execution

## Prerequisites

- `make setup` 完了
- MySQL コンテナ healthy

## Run Tests

```bash
make test
```

内部コマンド:

```bash
docker compose run --rm cli ./vendor/bin/phpunit
```

## Expected Output

```
OK (8 tests, 22 assertions)
```

（補強前ベースラインは 5 tests / 17 assertions）

## Test Scope

`modern/tests/CharacterizationTest.php` — レガシー版と同等の挙動を近代化後も維持することを検証。

| テスト | 検証内容 |
|---|---|
| login | 有効な認証情報 |
| user orders | user は自分の注文のみ |
| admin orders | admin は全件以上 |
| payment success | pending → paid + 監査ログ |
| pay others denied | user は他人の注文を決済不可 |
| paid rejected | paid 済みは例外 |
| missing order | 不存在 ID は例外 |
| admin pays other | admin は他ユーザー pending を決済可 |

## Troubleshooting

DB 接続エラー時は `docker compose ps` で `db` が healthy か確認し、`make setup` を再実行してください。
