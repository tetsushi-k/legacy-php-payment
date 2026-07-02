# Build Instructions

## Prerequisites

- Docker / Docker Compose
- Git
- ポート: 8080（legacy）, 8081（modern）, 3307（MySQL）

## Build Steps

### 1. 初回セットアップ

```bash
make setup
```

`docker compose up -d --build`、MySQL 起動待ち、`composer install` まで実行します。

### 2. 起動のみ

```bash
make up
```

### 3. 停止

```bash
make down
```

## Verify Build Success

- Legacy: http://localhost:8080/login.php
- Modern: http://localhost:8081/login.php
- ログイン: `test@example.com` / `password123`

## Troubleshooting

### コンテナ名衝突

`legacy_payment_db` が既に存在する場合、`docker compose run` が失敗することがあります。  
`docker compose ps` で既存スタックを確認し、不要なら `docker compose down` 後に再実行してください。
