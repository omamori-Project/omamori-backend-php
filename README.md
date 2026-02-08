# 오마모리 플랫폼 - PHP 백엔드 (オマモリプラットフォーム - 最終PHPバックエンド)

**모듈 기반 + Base 클래스 + AltoRouter + 공통 에러 처리 (モジュール基盤 + Baseクラス + AltoRouter + 共通エラー処理)**

---

## 주요 특징 (主な特徴)

### 1. 모듈 기반 구조 (モジュール基盤構造)

기능별로 코드가 한 곳에 모여있어 관리가 쉽습니다. (機能別にコードが一か所にまとまっており管理が容易です。)

### 2. Base 클래스 시스템 (Baseクラスシステム)

- **BaseController** - 공통 검증 및 응답 메서드 (共通の検証およびレスポンスメソッド)
- **BaseService** - 공통 비즈니스 로직 (共通ビジネスロジック)
- **BaseRepository** - 기본 CRUD 자동 제공 (基本CRUDの自動提供)

### 3. 통합 에러 처리 (統合エラー処理)

**ErrorHandler**로 모든 에러를 try-catch로 처리하고 자동 로깅 (**ErrorHandler**で全てのエラーをtry-catchで処理し自動ログ記録)

### 4. AltoRouter

강력한 RESTful 라우팅 라이브러리 사용 (強力なRESTfulルーティングライブラリを使用)

### 5. 완벽한 문서 (完全なドキュメント)

각 클래스의 역할과 사용법을 상세히 설명한 가이드 포함 (各クラスの役割と使い方を詳細に説明したガイドを含む)

---

## 프로젝트 구조 (プロジェクト構造)

```
omamori-php-final/
│
├── app/
│   ├── Core/                    프레임워크 핵심 (フレームワーク中核)
│   │   ├── Application.php
│   │   ├── Router.php           AltoRouter 기반 (AltoRouterベース)
│   │   ├── Request.php
│   │   ├── Response.php
│   │   └── Database.php
│   │
│   ├── Common/                  공통 기능 (共通機能)
│   │   ├── Base/                Base 클래스들 (Baseクラス群)
│   │   │   ├── BaseController.php
│   │   │   ├── BaseService.php
│   │   │   └── BaseRepository.php
│   │   ├── Exceptions/
│   │   │   └── ErrorHandler.php  통합 에러 처리 (統合エラー処理)
│   │   ├── Helpers/
│   │   │   └── functions.php
│   │   └── Middlewares/
│   │
│   └── Modules/                 기능 모듈 (機能モジュール)
│       ├── User/                완성 (完成)
│       │   ├── Controllers/UserController.php
│       │   ├── Services/UserService.php
│       │   └── Repositories/UserRepository.php
│       ├── Auth/                구현 필요 (実装必要)
│       ├── Omamori/             구현 필요 (実装必要)
│       └── Community/           구현 필요 (実装必要)
│
├── routes/
│   ├── api.php                  메인 라우트 (メインルート)
│   └── modules/                 모듈별 라우트 (モジュール別ルート)
│       ├── user.php
│       ├── auth.php
│       ├── omamori.php
│       └── community.php
│
├── docs/
│   └── HOW_TO_USE.md            클래스 사용 가이드 (クラス利用ガイド)
│
├── docker-compose.yml
├── composer.json                AltoRouter 포함 (AltoRouter含む)
└── README.md                    이 파일 (このファイル)
```

---

## 시작하기 (はじめに)

### 1. 환경 설정 (環境設定)

도커 실행 -> Composer 설치 (컨테이너 명으로) -> 오토로드 갱신 (Docker起動 -> Composer導入 (コンテナ名指定) -> オートロード更新)

```bash
cd omamori-php-final

# Docker 실행 (Docker起動)

docker compose up -d --build

# Composer 설치: 컨테이너 명 omamori_php_fpm (Composer導入: コンテナ名 omamori_php_fpm)

docker exec -it omamori_php_fpm composer install

# 오토로드 갱신 (オートロード更新)

docker exec -it omamori_php_fpm composer dump-autoload

```

### 2. 데이터베이스 설정 (データベース設定)

```sql
-- PostgreSQL 접속 (PostgreSQL接続)
docker compose exec postgres psql -U omamori_user -d omamori_db

-- Users 테이블 생성 (Usersテーブル作成)
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

### 3. 접속 확인 (接続確認)

```bash
curl http://localhost:8080/api/health
```

---

## 문서 (ドキュメント)

### 필독: 클래스 사용 가이드 (必読: クラス利用ガイド)

**[docs/HOW_TO_USE.md](docs/HOW_TO_USE.md)**

다음 내용을 자세히 설명합니다: (以下の内容を詳しく説明します:)

- BaseController 사용법 (validate, success, error 등) (BaseControllerの使い方)
- BaseService 사용법 (validateRequired, now, only 등) (BaseServiceの使い方)
- BaseRepository 사용법 (findAll, create, paginate 등) (BaseRepositoryの使い方)
- ErrorHandler 사용법 (통합 에러 처리) (ErrorHandlerの使い方)
- 완전한 예시 코드 (完全なサンプルコード)

---

## 핵심 개념 (主要概念)

### 1. Base 클래스 상속 (Baseクラス継承)

```php
// 모든 Controller는 BaseController 상속 (全てのControllerはBaseControllerを継承)
class UserController extends BaseController { }

// 모든 Service는 BaseService 상속 (全てのServiceはBaseServiceを継承)
class UserService extends BaseService { }

// 모든 Repository는 BaseRepository 상속 (全てのRepositoryはBaseRepositoryを継承)
class UserRepository extends BaseRepository { }
```

### 2. Try-Catch + ErrorHandler

```php
public function store(Request $request): Response
{
    try {
        // 로직... (ロジック...)
    } catch (\Exception $e) {
        // 항상 ErrorHandler 사용! (常にErrorHandlerを使用!)
        return ErrorHandler::handle($e);
    }
}
```

### 3. BaseController 메서드 활용 (BaseControllerメソッド活用)

```php
// validate() - 입력 검증 (入力検証)
$data = $this->validate($request, [
    'email' => 'required|email',
    'name' => 'required|min:2'
]);

// success() - 성공 응답 (成功レスポンス)
return $this->success($users, 'Retrieved successfully');

// notFound() - 404 응답 (404レスポンス)
return $this->notFound('User not found');
```

### 4. BaseRepository 자동 메서드 (BaseRepository自動メソッド)

```php
// BaseRepository를 상속받으면 자동으로 사용 가능! (BaseRepositoryを継承すると自動で使用可能!)
$userRepository->findAll();
$userRepository->findById(1);
$userRepository->create($data);
$userRepository->update($id, $data);
$userRepository->delete($id);
$userRepository->paginate(1, 15);
```

---

## 새 모듈 추가하기 (新しいモジュールの追加)

### 1단계: 폴더 생성 (1ステップ: フォルダ作成)

```bash
mkdir -p app/Modules/Product/Controllers
mkdir -p app/Modules/Product/Services
mkdir -p app/Modules/Product/Repositories
```

### 2단계: Repository 작성 (2ステップ: Repository作成)

```php
<?php
namespace App\Modules\Product\Repositories;

use App\Common\Base\BaseRepository;  // ← 상속 (継承)

class ProductRepository extends BaseRepository
{
    protected string $table = 'products';

    // findAll(), findById(), create() 등 자동 사용 가능! (など自動使用可能)
}
```

### 3단계: Service 작성 (3ステップ: Service作成)

```php
<?php
namespace App\Modules\Product\Services;

use App\Common\Base\BaseService;  // ← 상속 (継承)

class ProductService extends BaseService
{
    protected ProductRepository $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository(new \App\Core\Database());
    }

    public function createProduct(array $data): string
    {
        // validateRequired(), now() 등 사용 가능! (など使用可能)
        $this->validateRequired($data, ['name', 'price']);
        $data['created_at'] = $this->now();

        return $this->productRepository->create($data);
    }
}
```

### 4단계: Controller 작성 (4ステップ: Controller作成)

```php
<?php
namespace App\Modules\Product\Controllers;

use App\Common\Base\BaseController;  // ← 상속 (継承)
use App\Common\Exceptions\ErrorHandler;

class ProductController extends BaseController
{
    protected ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function store(Request $request): Response
    {
        try {
            // validate(), success() 등 사용 가능! (など使用可能)
            $data = $this->validate($request, [
                'name' => 'required',
                'price' => 'required|numeric'
            ]);

            $id = $this->productService->createProduct($data);

            return $this->success(['id' => $id], 'Created', 201);

        } catch (\Exception $e) {
            return ErrorHandler::handle($e);  // ← 에러 처리 (エラー処理)
        }
    }
}
```

### 5단계: 라우트 등록 (5ステップ: ルート登録)

```php
<?php
// routes/modules/product.php

global $router;

$router->get('/api/products', 'App\Modules\Product\Controllers\ProductController@index');
$router->post('/api/products', 'App\Modules\Product\Controllers\ProductController@store');
$router->get('/api/products/[i:id]', 'App\Modules\Product\Controllers\ProductController@show');
```

````php
<?php
// routes/modules/product.php

global $router;

$router->get('/api/products', 'App\Modules\Product\Controllers\ProductController@index');
$router->post('/api/products', 'App\Modules\Product\Controllers\ProductController@store');
$router->get('/api/products/[i:id]', 'App\Modules\Product\Controllers\ProductController@show');
```php
<?php
// routes/api.php에 추가 (routes/api.phpに追加)

require __DIR__ . '/modules/product.php';
````

---

## AltoRouter 라우트 패턴 (AltoRouterルートパターン)

```php
// 숫자 파라미터 (数値パラメータ)
$router->get('/api/users/[i:id]', 'Controller@method');
// → /api/users/123 (숫자만 / 数字のみ)

// 문자 파라미터 (文字パラメータ)
$router->get('/api/posts/[a:slug]', 'Controller@method');
// → /api/posts/hello-world (영문자만 / 英字のみ)

// 모든 문자 (全ての文字)
$router->get('/api/files/[*:path]', 'Controller@method');
// → /api/files/folder/subfolder/file.jpg

// 슬러그 (スラッグ)
$router->get('/api/blog/[slug:slug]', 'Controller@method');
// → /api/blog/my-post-123 (영숫자 + 하이픈 / 英数字 + ハイフン)
```

---

## API 엔드포인트 (APIエンドポイント)

### Health Check (ヘルスチェック)

```
GET /api/health
```

### User 모듈 (Userモジュール)

```
GET    /api/users           # 목록 (一覧)
GET    /api/users/[i:id]    # 상세 (詳細)
POST   /api/users           # 생성 (作成)
PUT    /api/users/[i:id]    # 수정 (更新)
DELETE /api/users/[i:id]    # 삭제 (削除)
```

### Auth 모듈 (Authモジュール)

```
POST /api/auth/register     # 회원가입 (会員登録)
POST /api/auth/login        # 로그인 (ログイン)
POST /api/auth/logout       # 로그아웃 (ログアウト)
```

### Omamori 모듈 (Omamoriモジュール)

```
GET    /api/omamori
POST   /api/omamori
GET    /api/omamori/[i:id]
PUT    /api/omamori/[i:id]
DELETE /api/omamori/[i:id]
```

---

## 개발 명령어 (開発コマンド)

```bash
# 컨테이너 관리 (コンテナ管理)
docker compose up -d          # 시작 (開始)
docker compose down           # 중지 (停止)
docker compose restart        # 재시작 (再起動)
docker compose ps             # 상태 확인 (状態確認)

# Composer (컨테이너 명: omamori_php_fpm)
docker exec -it omamori_php_fpm composer install
docker exec -it omamori_php_fpm composer require package/name

# 데이터베이스 (データベース)
docker compose exec postgres psql -U omamori_user -d omamori_db

# PHP 컨테이너 접속 (PHPコンテナ接続)
docker exec -it omamori_php_fpm bash

# 로그 확인 (ログ確認)
docker compose logs -f
docker compose logs -f php
docker compose logs -f nginx
```

---

## 학습 포인트 (学習ポイント)

### 1. Base 클래스 패턴 (Baseクラスパターン)

- 공통 기능을 Base에 두고 상속 (共通機能をBaseに置いて継承)
- 중복 코드 최소화 (重複コードの最小化)
- 일관성 있는 코드 (一貫性のあるコード)

### 2. 모듈 구조 (モジュール構造)

- 기능별로 코드 분리 (機能別にコードを分離)
- 독립적 개발 가능 (独立した開発が可能)
- 재사용성 증가 (再利用性の向上)

### 3. Try-Catch 패턴 (Try-Catchパターン)

- 모든 예외를 ErrorHandler로 처리 (全ての例外をErrorHandlerで処理)
- 자동 로그 기록 (自動ログ記録)
- 사용자 친화적 에러 메시지 (ユーザーフレンドリーなエラーメッセージ)

### 4. AltoRouter

- RESTful 라우팅 (RESTfulルーティング)
- 동적 파라미터 (動的パラメータ)
- 이름 있는 라우트 (名前付きルート)

---

## 체크리스트 (チェックリスト)

새 모듈 만들 때: (新しいモジュール作成時:)

- [ ] **Repository**
  - [ ] `BaseRepository` 상속 (継承)
  - [ ] `protected string $table` 설정 (設定)

- [ ] **Service**
  - [ ] `BaseService` 상속 (継承)
  - [ ] Repository 의존성 주입 (依存性注入)
  - [ ] `validateRequired()`, `now()` 활용 (活用)

- [ ] **Controller**
  - [ ] `BaseController` 상속 (継承)
  - [ ] Service 의존성 주입 (依存性注入)
  - [ ] `validate()`, `success()` 활용 (活用)
  - [ ] `try-catch` + `ErrorHandler::handle()`

- [ ] **Routes**
  - [ ] `routes/modules/{모듈}.php` 생성 (作成)
  - [ ] `routes/api.php`에서 require (require)

---

## 문제 해결 (問題解決)

### Composer 패키지가 안 보여요 (Composerパッケージが見えません)

```bash
docker exec -it omamori_php_fpm composer dump-autoload
```

### 라우트가 동작 안 해요 (ルートが動作しません)

```php
// routes/api.php에서 require 확인 (routes/api.phpでrequire確認)
require __DIR__ . '/modules/yourmodule.php';
```

### 에러 로그는 어디에? (エラーログはどこですか)

```
storage/logs/error-YYYY-MM-DD.log
```

---

## 다음 단계 (次のステップ)

1. **[docs/HOW_TO_USE.md](docs/HOW_TO_USE.md)** 읽기 ← 중요! (を読む ← 重要!)
1. User 모듈 코드 분석하기 (Userモジュールのコード分析)
1. 새 모듈 만들어보기 (Product, Post 등) (新しいモジュールを作ってみる)
1. Base 클래스 메서드 익히기 (Baseクラスメソッドを学ぶ)

---
