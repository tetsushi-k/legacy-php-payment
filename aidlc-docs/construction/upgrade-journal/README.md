# Upgrade Journal

PHP 7.4（レガシーパターン再現）→ PHP 8.3 近代化の検証・改修・躓きを記録したジャーナルです。  
面談では **コードより先に** このディレクトリを画面共有してください。

## 読む順番

1. [01-baseline-verification.md](01-baseline-verification.md) — 補強前の検証ベースライン
2. [02-migration-changelog.md](02-migration-changelog.md) — アンチパターン別の改修対応表
3. [03-stumbling-blocks.md](03-stumbling-blocks.md) — 躓き・却下した案
4. [04-hardening-log.md](04-hardening-log.md) — CI・テスト追加・Rector の補強記録

## 面談用 想定 Q&A

**Q1. バージョンアップ前に何を検証しましたか？**  
A. characterization test でレガシー同等挙動を固定し、PHPStan L6 と Rector dry-run で静的品質を確認しました（[01-baseline-verification.md](01-baseline-verification.md)）。

**Q2. なぜ PDO に変えたのですか？**  
A. legacy は mysqli + 文字列 SQL で SQL インジェクションリスクがありました。プリペアドステートメントでパラメータバインドに統一しています（[02-migration-changelog.md](02-migration-changelog.md) #2, #3）。

**Q3. 権限チェックはどう整理しましたか？**  
A. `orders.php` / `pay.php` に散在していた if 文を `RbacPolicy` に集約しました。admin は全注文、user は自分のみです。

**Q4. 決済の競合はどう扱いましたか？**  
A. `PaymentService` でトランザクション + `SELECT ... FOR UPDATE` により、pending 以外への二重決済を防ぎます。監査ログに before/after を記録します。

**Q5. テストは何を担保していますか？**  
A. characterization test でログイン・一覧の RBAC・決済成功・権限拒否に加え、paid 済み拒否・存在しない注文・admin の他ユーザー決済を検証します（[04-hardening-log.md](04-hardening-log.md)）。

**Q6. Rector は使いましたか？**  
A. 設定済みですが、ベースライン時点では適用提案なし。無理に diff を出すより green 維持を優先しました（[04-hardening-log.md](04-hardening-log.md)）。

**Q7. 躓いた点はありますか？**  
A. PHP 5.6 の mysql_* は現行 Docker で非対応のため PHP 7.4 + mysqli で再現したこと、ローカルで compose プロジェクト名が衝突すると `make test` が失敗すること（[03-stumbling-blocks.md](03-stumbling-blocks.md)）。

**Q8. AI-DLC 上いまどこまでですか？**  
A. Construction 完了後、Build and Test ステージを代理実行で完走。`aidlc-docs/aidlc-state.md` と `audit.md` を参照してください。
