# オマモリ PHP プロジェクト - 全ファイル一覧

## 完全なフォルダ構成

```
omamori-php-final/
│
├── README.md                                    プロジェクト全体ガイド
├── composer.json                                Composer設定 (AltoRouter含む)
├── docker-compose.yml                           Docker設定
├── .env.example                                 環境変数テンプレート
├── .gitignore                                   Git無視ファイル
│
├── app/                                         アプリケーションコア
│   │
│   ├── Core/                                    フレームワーク中核システム
│   │   ├── Application.php                      アプリケーションコンテナ
│   │   ├── Router.php                           AltoRouterベースのルーティング
│   │   ├── Request.php                          HTTPリクエスト処理
│   │   ├── Response.php                         HTTPレスポンス生成
│   │   └── Database.php                         PDO PostgreSQL接続
│   │
│   ├── Common/                                  共通機能
│   │   │
│   │   ├── Base/                                Baseクラス
│   │   │   ├── BaseController.php               すべてのControllerの親
│   │   │   │                                       - validate() 入力検証
│   │   │   │                                       - success() 成功レスポンス
│   │   │   │                                       - error() エラーレスポンス
│   │   │   │                                       - notFound() 404レスポンス
│   │   │   │
│   │   │   ├── BaseService.php                  すべてのServiceの親
│   │   │   │                                       - validateRequired()
│   │   │   │                                       - now() タイムスタンプ
│   │   │   │                                       - only() キー抽出
│   │   │   │                                       - except() キー除外
│   │   │   │
│   │   │   └── BaseRepository.php               すべてのRepositoryの親
│   │   │                                           - findAll() 一覧取得
│   │   │                                           - findById() ID取得
│   │   │                                           - create() 作成
│   │   │                                           - update() 更新
│   │   │                                           - delete() 削除
│   │   │                                           - paginate() ページング
│   │   │
│   │   ├── Exceptions/                          エラー処理
│   │   │   └── ErrorHandler.php                 統合エラーハンドラ
│   │   │                                           - handle() 例外処理
│   │   │                                           - 自動ログ記録
│   │   │                                           - タイプ別レスポンス
│   │   │
│   │   ├── Helpers/                             ユーティリティ
│   │   │   └── functions.php                    env(), dd(), dump()
│   │   │
│   │   └── Middlewares/                         ミドルウェア (後で追加)
│   │
│   └── Modules/                                 機能モジュール
│       │
│       ├── User/                                ユーザーモジュール 完成
│       │   ├── Controllers/
│       │   │   └── UserController.php           HTTPリクエスト処理
│       │   │                                       - index() 一覧
│       │   │                                       - show() 詳細
│       │   │                                       - store() 作成
│       │   │                                       - update() 更新
│       │   │                                       - destroy() 削除
│       │   │
│       │   ├── Services/
│       │   │   └── UserService.php              ビジネスロジック
│       │   │                                       - getAllUsers()
│       │   │                                       - createUser()
│       │   │                                       - authenticate()
│       │   │
│       │   └── Repositories/
│       │       └── UserRepository.php           DBアクセス
│       │                                           - findByEmail()
│       │                                           - emailExists()
│       │                                           - search()
│       │
│       ├── Auth/                                認証モジュール 実装必要
│       │   ├── Controllers/
│       │   ├── Services/
│       │   └── Repositories/
│       │
│       ├── Omamori/                             お守りモジュール 実装必要
│       │   ├── Controllers/
│       │   ├── Services/
│       │   └── Repositories/
│       │
│       └── Community/                           コミュニティモジュール 実装必要
│           ├── Controllers/
│           ├── Services/
│           └── Repositories/
│
├── bootstrap/                                   ブートストラップ
│   └── app.php                                  アプリケーション初期化
│                                                   - Autoload 読み込み
│                                                   - .env 読み込み
│                                                   - 設定 読み込み
│
├── config/                                      設定ファイル
│   └── database.php                             DB接続情報
│
├── database/                                    データベース
│   ├── migrations/                              SQLマイグレーション (後で追加)
│   └── seeds/                                   シードデータ (後で追加)
│
├── docker/                                      Docker設定
│   ├── nginx/
│   │   └── nginx.conf                           Nginx Webサーバー設定
│   └── php/
│       └── Dockerfile                           PHP-FPMコンテナイメージ
│
├── docs/                                        ドキュメント
│   └── HOW_TO_USE.md                            クラス使用の完全ガイド
│                                                   - BaseController 使用方法
│                                                   - BaseService 使用方法
│                                                   - BaseRepository 使用方法
│                                                   - ErrorHandler 使用方法
│                                                   - 完全なサンプルコード
│
├── public/                                      Webルート
│   └── index.php                                エントリポイント (すべてのリクエスト)
│
├── routes/                                      ルーティング
│   ├── api.php                                  メインルートファイル
│   └── modules/                                 モジュール別ルート
│       ├── user.php                             Userルート
│       ├── auth.php                             Authルート
│       ├── omamori.php                          Omamoriルート
│       └── community.php                        Communityルート
│
├── storage/                                     ストレージ (自動生成)
│   ├── omamori/
│   │   ├── layers/                              レイヤー画像
│   │   ├── generated/                           生成されたお守り
│   │   └── temp/                                一時ファイル
│   ├── logs/                                    ログファイル
│   └── cache/                                   キャッシュ
│
├── tests/                                       テスト (後で追加)
│
└── vendor/                                      Composerパッケージ (自動生成)
```

---

## ファイル数統計

### Coreシステム (5個)

- Application.php
- Router.php
- Request.php
- Response.php
- Database.php

### Baseクラス (3個)

- BaseController.php
- BaseService.php
- BaseRepository.php

### Common (2個)

- ErrorHandler.php
- functions.php

### Userモジュール (3個)

- UserController.php
- UserService.php
- UserRepository.php

### ルート (5個)

- api.php
- user.php
- auth.php
- omamori.php
- community.php

### 設定/Docker (5個)

- composer.json
- docker-compose.yml
- nginx.conf
- Dockerfile
- database.php

### ドキュメント (2個)

- README.md
- HOW_TO_USE.md

### その他 (4個)

- .env.example
- .gitignore
- app.php (bootstrap)
- index.php (public)

**合計ファイル: 29個**

---

## 主要ファイル説明

### 必ず読むべきドキュメント

| 파일                   | 설명                           |
| ---------------------- | ------------------------------ |
| **README.md**          | プロジェクト全体ガイド         |
| **docs/HOW_TO_USE.md** | クラス使用の完全ガイド (50KB+) |

### Baseクラス (共通機能)

| 파일                   | 역할                   | 주요 메서드                       |
| ---------------------- | ---------------------- | --------------------------------- |
| **BaseController.php** | すべてのControllerの親 | validate(), success(), error()    |
| **BaseService.php**    | すべてのServiceの親    | validateRequired(), now(), only() |
| **BaseRepository.php** | すべてのRepositoryの親 | findAll(), create(), paginate()   |

### エラー処理

| 파일                 | 역할                               |
| -------------------- | ---------------------------------- |
| **ErrorHandler.php** | Try-Catch 統合エラー処理, 自動ログ |

### モジュール例 (User)

| 파일                   | 역할                                  |
| ---------------------- | ------------------------------------- |
| **UserController.php** | HTTPリクエスト → Service呼び出し      |
| **UserService.php**    | ビジネスロジック → Repository呼び出し |
| **UserRepository.php** | DB CRUD 操作                          |

### ルーティング

| 파일                      | 역할                                |
| ------------------------- | ----------------------------------- |
| **routes/api.php**        | メインルート (全モジュール読み込み) |
| **routes/modules/\*.php** | 各モジュールのエンドポイント定義    |

### コアシステム

| 파일             | 역할                           |
| ---------------- | ------------------------------ |
| **Router.php**   | AltoRouterベースのルーティング |
| **Database.php** | PDO PostgreSQL接続             |
| **Request.php**  | HTTPリクエスト解析             |
| **Response.php** | JSONレスポンス生成             |

---

## ファイル色ガイド

- **完成** (Userモジュール)
- **フォルダのみ** (Auth, Omamori, Community)
- **主要ファイル** (Baseクラス, ErrorHandler)
- **ドキュメント** (README, HOW_TO_USE)

---

## ファイル検索ガイド

### BaseControllerはどこ?

```
app/Common/Base/BaseController.php
```

### Userモジュールはどこ?

```
app/Modules/User/
├── Controllers/UserController.php
├── Services/UserService.php
└── Repositories/UserRepository.php
```

### ルートはどこで登録?

```
routes/modules/user.php  ← 여기서 등록
routes/api.php           ← 여기서 로드
```

### Docker設定はどこ?

```
docker-compose.yml       ← 메인 설정
docker/nginx/nginx.conf  ← Nginx 설정
docker/php/Dockerfile    ← PHP 설정
```

### 使用ガイドはどこ?

```
docs/HOW_TO_USE.md  ← ここ!!!
```

---

## ダウンロード後の実行手順

```bash
# 1. プロジェクトフォルダへ移動
cd omamori-php-final

# 2. .env 作成
cp .env.example .env

# 3. Docker 起動
docker-compose up -d --build

# 4. Composer インストール
docker-compose exec php composer install

# 5. 接続確認
curl http://localhost:8080/api/health
```

---

## 開発開始手順

1. **[README.md](README.md)** を読む
2. **[docs/HOW_TO_USE.md](docs/HOW_TO_USE.md)** を熟読する
3. **Userモジュール** のコードを分析する
4. **新しいモジュール** を作成する

---
