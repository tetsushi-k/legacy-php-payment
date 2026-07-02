# Cloud Agent 依頼プロンプト（コピペ用）

Cursor Cloud Agents の起動プロンプトに、以下の ``` 内をそのまま貼り付けてください。

作業ブランチ: `cloud/upgrade-journal-and-hardening`

---

```
# ミッション

リポジトリ `legacy-php-payment`（EC 最小決済のレガシー PHP 刷新ポートフォリオ）について、
**実装はほぼ完了済み**の状態から、AI-DLC **代理実行モード**で欠落ステージを完走し、
面談で「後から Q&A しながら理解できる」証跡を整備して PR を作成してください。

## AI-DLC 代理実行モード（重要）

あなたは開発者の代理として AI-DLC を進める。チャットで人間の承認を待たない。

1. 最初に `.cursor/rules/ai-dlc-workflow.mdc` と `aidlc-docs/aidlc-state.md` を読む
2. Reverse Engineering / Inception / Construction（Code Generation）は**完了済み**とみなし再実行しない
3. **Build and Test** ステージを自律完走（`.aidlc-rule-details/construction/build-and-test.md` 参照）
4. 判断・承認はすべて `aidlc-docs/audit.md` に **Proxy Approval** として記録（根拠・却下した代替案を必ず書く）
5. 不明点は `OPEN-QUESTIONS.md` に A/B/C 形式で残す。ブロッカーでなければ既存設計ドキュメントのデフォルトで進む
6. 各ステージ完了時に `aidlc-state.md` のチェックボックスを更新

## 背景

- `legacy/` = PHP 7.4 + mysqli（意図的アンチパターン。削除・修正禁止）
- `modern/` = PHP 8.3 + PDO + RbacPolicy + AuditLogger（近代化済み）
- AI-DLC の Reverse Engineering / Inception / Construction は完了
- 主役成果物は `aidlc-docs/`（Never Vibe Code: 設計判断はドキュメント経由）

参照必須:
- aidlc-docs/inception/reverse-engineering/spec-restored.md
- aidlc-docs/inception/reverse-engineering/anti-patterns.md
- aidlc-docs/inception/migration-plan.md
- aidlc-docs/construction/modernization-summary.md
- .cursor/rules/legacy-modernization.mdc

## ゴール（この依頼の完了条件）

1. **upgrade-journal** — バージョンアップの検証・改修・躓きを時系列で記録
2. **AI-DLC 欠落分の補完** — audit.md、build-and-test 手順
3. **品質補強** — CI、テスト追加、Rector 本適用（安全な範囲）
4. **専用ブランチ + PR** — マージは人間が後で判断

## 作業ブランチ

- ブランチ名: `cloud/upgrade-journal-and-hardening`
- main から作成。完了時に PR を作成（タイトル例: `docs: upgrade journal and quality hardening`）

## フェーズ 0: ベースライン検証（必ず最初に実行・記録）

以下を実行し、出力を `aidlc-docs/construction/upgrade-journal/01-baseline-verification.md` に保存:

```bash
make setup
make test
make phpstan
make rector   # dry-run
```

記録フォーマット（各コマンドごと）:
- 実行日時
- 成功/失敗
- 失敗時のエラー全文
- テスト件数・PHPStan 指摘数・Rector 提案 diff の要約

## フェーズ 1: 遡及ジャーナル作成

`legacy/` と `modern/` の diff、および anti-patterns.md の 10 項目を突合し、
`aidlc-docs/construction/upgrade-journal/02-migration-changelog.md` を作成。

各行に必ず含める:
| 項目 | アンチパターン # | legacy の該当 | modern の対応 | 採用理由 | 検証方法 |

`03-stumbling-blocks.md` には以下を書く（推測で埋めない）:
- 既存ドキュメントに書かれている判断
- **今回セッションで実際に遭遇した**失敗
- 試して却下した案

`upgrade-journal/README.md` を索引にし、面談用の想定 Q&A（5〜8 問）を末尾に置く。

## フェーズ 2: 品質補強（make test / make phpstan を常に green に）

### 2a. CI 追加

`.github/workflows/ci.yml` を新規作成（push / pull_request で make test + make phpstan）

### 2b. テスト追加

spec-restored.md の未カバー UC:
- 既に paid の注文への決済 → 例外
- 存在しない order_id → 例外
- admin が他ユーザーの pending 注文を決済できる

### 2c. Rector 本適用（安全な範囲のみ）

### 2d. environment.json 改善（必要なら）

## フェーズ 3: AI-DLC Build and Test（代理実行で完走）

build-and-test/ 一式を作成し、実コマンド出力を summary に記録。

## 禁止事項

- `legacy/` のアンチパターンを「直す」変更
- Phase 4 Laravel 化・Stripe 連携
- make test / make phpstan が red のまま PR

## 作業スタイル

- 1 フェーズ完了ごとにコミット
- 不明点は OPEN-QUESTIONS.md に残し、推測実装しない
- ドキュメントとコードは常に同時更新（Never Vibe Code）
```

## 起動後の確認

PR が上がったら:

1. `aidlc-docs/construction/upgrade-journal/README.md` を読む
2. `make setup && make test && make phpstan` をローカルで再現
3. `aidlc-docs/audit.md` の Proxy Approval を確認
