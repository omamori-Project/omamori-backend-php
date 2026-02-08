# クラス使用ガイド

このドキュメントは、**いつ、どのように** Base クラスを継承し、メソッドを使用するかを説明します。

---

## 目次

1. [BaseControllerの使い方](#basecontroller)
2. [BaseServiceの使い方](#baseservice)
3. [BaseRepositoryの使い方](#baserepository)
4. [ErrorHandlerの使い方](#errorhandler)
5. [完全な例](#完全な例)

---

## BaseController

### 場所

```
app/Common/Base/BaseController.php
```

### いつ継承しますか？

**すべての Controller クラス**は BaseController を継承する必要があります。

### なぜ継承しますか？

共通機能の重複を避けるためです:

- 入力検証 (validate)
- 応答ヘルパー (success, error など)

### 継承方法

```php
<?php

namespace App\Modules\User\Controllers;

use App\Common\Base\BaseController;  // ← Base クラスを import
use App\Core\Request;
use App\Core\Response;

class UserController extends BaseController  // ← 継承！
{
    public function store(Request $request): Response
    {
        // BaseController のメソッドを利用可能！
    }
}
```

---

### 使用可能なメソッド

#### 1. `validate()` - 入力検証

```php
public function store(Request $request): Response
{
    // BaseController の validate() メソッドを呼び出し
    $data = $this->validate($request, [
        'email' => 'required|email',
        'name' => 'required|min:2|max:50',
        'password' => 'required|min:6',
        'age' => 'numeric'
    ]);

    // $data は検証済みのクリーンなデータ
    // 検証に失敗すると自動で InvalidArgumentException をスロー
}
```

**サポートするルール:**

- `required` - 必須
- `email` - メール形式
- `min:n` - 最小文字数
- `max:n` - 最大文字数
- `numeric` - 数値のみ

---

#### 2. `success()` - 成功応答

```php
public function index(Request $request): Response
{
    $users = $this->userService->getAllUsers();

    // BaseController の success() メソッド
    return $this->success($users, 'Users retrieved successfully');

    // 応答例:
    // {
    //   "success": true,
    //   "message": "Users retrieved successfully",
    //   "data": [...]
    // }
}
```

**パラメータ:**

- `$data` - 応答データ
- `$message` - メッセージ (デフォルト: 'Success')
- `$statusCode` - HTTP ステータスコード (デフォルト: 200)

---

#### 3. `error()` - エラー応答

```php
public function update(Request $request): Response
{
    // BaseController の error() メソッド
    return $this->error('Something went wrong', 500);

    // 応答例:
    // {
    //   "success": false,
    //   "message": "Something went wrong"
    // }
}
```

---

#### 4. `notFound()` - Not Found 応答

```php
public function show(Request $request): Response
{
    $id = $request->param('id');
    $user = $this->userService->getUserById($id);

    if (!$user) {
        // BaseController の notFound() メソッド
        return $this->notFound('User not found');
    }

    return $this->success($user);
}
```

---

#### 5. `unauthorized()` - 認証失敗応答

```php
public function profile(Request $request): Response
{
    $token = $request->bearerToken();

    if (!$token) {
        // BaseController の unauthorized() メソッド
        return $this->unauthorized('Token required');
    }

    // ...
}
```

---

#### 6. `forbidden()` - 権限なし応答

```php
public function delete(Request $request): Response
{
    if (!$this->isAdmin()) {
        // BaseController の forbidden() メソッド
        return $this->forbidden('Admin only');
    }

    // ...
}
```

---

#### 7. `validationError()` - バリデーションエラー応答

```php
public function store(Request $request): Response
{
    $errors = [
        'email' => ['Email is already taken'],
        'password' => ['Password too weak']
    ];

    // BaseController の validationError() メソッド
    return $this->validationError($errors);

    // 応答例:
    // {
    //   "success": false,
    //   "message": "Validation failed",
    //   "errors": {
    //     "email": ["Email is already taken"],
    //     "password": ["Password too weak"]
    //   }
    // }
}
```

---

### 完全な Controller 例

```php
<?php

namespace App\Modules\User\Controllers;

use App\Common\Base\BaseController;
use App\Core\Request;
use App\Core\Response;
use App\Modules\User\Services\UserService;
use App\Common\Exceptions\ErrorHandler;

class UserController extends BaseController
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function store(Request $request): Response
    {
        try {
            // validate() を使用
            $data = $this->validate($request, [
                'email' => 'required|email',
                'name' => 'required|min:2',
                'password' => 'required|min:6'
            ]);

            $userId = $this->userService->createUser($data);

            // success() を使用
            return $this->success(['id' => $userId], 'Created', 201);

        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function show(Request $request): Response
    {
        try {
            $id = $request->param('id');
            $user = $this->userService->getUserById($id);

            if (!$user) {
                // notFound() を使用
                return $this->notFound('User not found');
            }

            // success() を使用
            return $this->success($user);

        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }
}
```

---

## BaseService

### 場所

```
app/Common/Base/BaseService.php
```

### いつ継承しますか？

**すべての Service クラス**は BaseService を継承する必要があります。

### なぜ継承しますか？

共通のビジネスロジック用メソッドを使うためです:

- 検証 (validate, validateRequired)
- ユーティリティ (now, only, except)

### 継承方法

```php
<?php

namespace App\Modules\User\Services;

use App\Common\Base\BaseService;  // ← Base クラスを import

class UserService extends BaseService  // ← 継承！
{
    public function createUser(array $data): string
    {
        // BaseService のメソッドを利用可能！
    }
}
```

---

### 使用可能なメソッド

#### 1. `validateRequired()` - 必須フィールド検証

```php
public function createUser(array $data): string
{
    // BaseService の validateRequired() メソッド
    $this->validateRequired($data, ['email', 'password', 'name']);

    // email, password, name が無い場合は InvalidArgumentException

    // 検証を通過したら続行
    // ...
}
```

---

#### 2. `validate()` - ルールベース検証

```php
public function updateProfile(array $data): bool
{
    // BaseService の validate() メソッド
    $this->validate($data, [
        'name' => 'required|min:2',
        'email' => 'email',
        'age' => 'numeric'
    ]);

    // 検証を通過したら続行
    // ...
}
```

---

#### 3. `now()` - 現在のタイムスタンプ

```php
public function createUser(array $data): string
{
    // BaseService の now() メソッド
    $data['created_at'] = $this->now();  // '2026-02-08 12:34:56'
    $data['updated_at'] = $this->now();

    return $this->userRepository->create($data);
}
```

---

#### 4. `only()` - 特定キーのみ抽出

```php
public function updateUser(array $data): bool
{
    // BaseService の only() メソッド
    // name, email のみ抽出
    $filtered = $this->only($data, ['name', 'email']);

    // $filtered = ['name' => '...', 'email' => '...']

    return $this->userRepository->update($id, $filtered);
}
```

---

#### 5. `except()` - 特定キーを除外

```php
public function updateUser(array $data): bool
{
    // BaseService の except() メソッド
    // created_at を除外
    $filtered = $this->except($data, ['created_at']);

    return $this->userRepository->update($id, $filtered);
}
```

---

### 完全な Service 例

```php
<?php

namespace App\Modules\User\Services;

use App\Common\Base\BaseService;
use App\Modules\User\Repositories\UserRepository;
use App\Core\Database;

class UserService extends BaseService
{
    protected UserRepository $userRepository;

    public function __construct()
    {
        $db = new Database();
        $this->userRepository = new UserRepository($db);
    }

    public function createUser(array $data): string
    {
        // validateRequired() を使用
        $this->validateRequired($data, ['email', 'password', 'name']);

        // メール重複チェック
        if ($this->userRepository->emailExists($data['email'])) {
            throw new \InvalidArgumentException('Email already exists');
        }

        // パスワードをハッシュ化
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // now() を使用
        $data['created_at'] = $this->now();
        $data['updated_at'] = $this->now();

        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data): bool
    {
        // except() を使用 - created_at は更新不可
        $data = $this->except($data, ['created_at', 'id']);

        // now() を使用
        $data['updated_at'] = $this->now();

        return $this->userRepository->update($id, $data);
    }
}
```

---

## BaseRepository

### 場所

```
app/Common/Base/BaseRepository.php
```

### いつ継承しますか？

**すべての Repository クラス**は BaseRepository を継承する必要があります。

### なぜ継承しますか？

基本 CRUD を自動で使うためです:

- findAll, findById, findBy, findOneBy
- create, update, delete
- count, exists, paginate

### 継承方法

```php
<?php

namespace App\Modules\User\Repositories;

use App\Common\Base\BaseRepository;  // ← Base クラスを import

class UserRepository extends BaseRepository  // ← 継承！
{
    protected string $table = 'users';  // ← テーブル名を指定
    protected string $primaryKey = 'id';  // ← Primary Key (デフォルト: 'id')

    // BaseRepository のメソッドを自動で利用可能！
}
```

---

### 自動的に利用できるメソッド

#### 1. `findAll()` - 全件取得

```php
// UserRepository インスタンス
$users = $userRepository->findAll();

// SELECT * FROM users WHERE deleted_at IS NULL ORDER BY id DESC

// ソートオプション
$users = $userRepository->findAll(['created_at' => 'DESC', 'name' => 'ASC']);
```

---

#### 2. `findById()` - ID で取得

```php
$user = $userRepository->findById(1);

// SELECT * FROM users WHERE id = 1 AND deleted_at IS NULL

if ($user) {
    echo $user['name'];
}
```

---

#### 3. `findBy()` - 条件で取得 (複数)

```php
// status が 'active' の全ユーザー
$users = $userRepository->findBy(['status' => 'active']);

// SELECT * FROM users WHERE status = 'active' AND deleted_at IS NULL

// 複数条件
$users = $userRepository->findBy([
    'status' => 'active',
    'role' => 'admin'
]);

// ソート
$users = $userRepository->findBy(
    ['status' => 'active'],
    ['created_at' => 'DESC']
);
```

---

#### 4. `findOneBy()` - 条件で取得 (1 件)

```php
// メールでユーザー検索
$user = $userRepository->findOneBy(['email' => 'user@example.com']);

// SELECT * FROM users WHERE email = 'user@example.com' AND deleted_at IS NULL LIMIT 1

if ($user) {
    echo $user['name'];
}
```

---

#### 5. `create()` - 作成

```php
$data = [
    'name' => 'John',
    'email' => 'john@example.com',
    'password' => 'hashed_password',
    'created_at' => date('Y-m-d H:i:s')
];

$id = $userRepository->create($data);

// INSERT INTO users (name, email, password, created_at)
// VALUES (?, ?, ?, ?) RETURNING id

echo "Created user ID: {$id}";
```

---

#### 6. `update()` - 更新

```php
$data = [
    'name' => 'John Updated',
    'updated_at' => date('Y-m-d H:i:s')
];

$success = $userRepository->update(1, $data);

// UPDATE users SET name = ?, updated_at = ? WHERE id = ?

if ($success) {
    echo "Updated successfully";
}
```

---

#### 7. `delete()` - 削除 (ソフト削除)

```php
$success = $userRepository->delete(1);

// UPDATE users SET deleted_at = ? WHERE id = ?
// 物理削除せずに deleted_at のみ設定！

if ($success) {
    echo "Deleted successfully";
}
```

---

#### 8. `hardDelete()` - ハード削除 (物理削除)

```php
$success = $userRepository->hardDelete(1);

// DELETE FROM users WHERE id = ?
// 本当に削除される！

if ($success) {
    echo "Permanently deleted";
}
```

---

#### 9. `count()` - 件数取得

```php
// 全件数
$total = $userRepository->count();

// SELECT COUNT(*) as count FROM users WHERE deleted_at IS NULL

// 条件付き件数
$activeCount = $userRepository->count(['status' => 'active']);

echo "Total users: {$total}";
```

---

#### 10. `exists()` - 存在判定

```php
// メール重複チェック
$exists = $userRepository->exists(['email' => 'user@example.com']);

if ($exists) {
    echo "Email already taken";
}
```

---

#### 11. `paginate()` - ページネーション

```php
// 1 ページ目、15 件ずつ
$result = $userRepository->paginate(1, 15);

// 結果構造:
// [
//   'data' => [...],           // 実データ
//   'current_page' => 1,       // 現在ページ
//   'per_page' => 15,          // 1 ページあたりの件数
//   'total' => 100,            // 全件数
//   'last_page' => 7           // 最終ページ
// ]

foreach ($result['data'] as $user) {
    echo $user['name'];
}

echo "Page {$result['current_page']} of {$result['last_page']}";
```

---

### カスタムメソッド追加

BaseRepository を継承した後、**カスタムメソッド**を追加できます:

```php
<?php

namespace App\Modules\User\Repositories;

use App\Common\Base\BaseRepository;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    /**
     * メールでユーザー検索
     *
     * BaseRepository の findOneBy() を活用
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * 最近の登録ユーザー取得
     *
     * 直接クエリを作成
     */
    public function findRecentUsers(int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE deleted_at IS NULL
                ORDER BY created_at DESC
                LIMIT ?";

        return $this->db->query($sql, [$limit]);
    }

    /**
     * ユーザー検索
     *
     * 直接クエリを作成
     */
    public function search(string $keyword): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE deleted_at IS NULL
                AND (name LIKE ? OR email LIKE ?)";

        $keyword = "%{$keyword}%";

        return $this->db->query($sql, [$keyword, $keyword]);
    }

    /**
     * メール重複チェック (更新時に自分を除外)
     *
     * BaseRepository の exists() を活用 + カスタムロジック
     */
    public function emailExists(string $email, $excludeId = null): bool
    {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table}
                    WHERE email = ? AND id != ? AND deleted_at IS NULL";

            $result = $this->db->queryOne($sql, [$email, $excludeId]);
            return (int) $result['count'] > 0;
        }

        return $this->exists(['email' => $email]);
    }
}
```

**使用例:**

```php
// BaseRepository メソッドの利用
$allUsers = $userRepository->findAll();
$user = $userRepository->findById(1);

// カスタムメソッドの利用
$user = $userRepository->findByEmail('user@example.com');
$recent = $userRepository->findRecentUsers(5);
$results = $userRepository->search('john');
```

---

## ErrorHandler

### 場所

```
app/Common/Exceptions/ErrorHandler.php
```

### いつ使いますか？

**すべての try-catch ブロック**で ErrorHandler を使用します。

### なぜ使用しますか？

- 共通のエラー処理
- エラーログの自動記録
- ユーザーに優しいエラーメッセージ

### 使い方

#### 1. Controller で使用

```php
<?php

namespace App\Modules\User\Controllers;

use App\Common\Base\BaseController;
use App\Common\Exceptions\ErrorHandler;  // ← import
use App\Core\Request;
use App\Core\Response;

class UserController extends BaseController
{
    public function store(Request $request): Response
    {
        try {
            // ロジック...

        } catch (\Exception $e) {
            // ErrorHandler を使用
            return ErrorHandler::handle($e);
        }
    }
}
```

#### 2. 自動で処理される例外

**InvalidArgumentException** → バリデーションエラー

```php
throw new \InvalidArgumentException('Email already exists');
// → 422 Validation Error 応答
```

**PDOException** → データベースエラー

```php
// DB エラー発生
// → 500 Database Error 応答
```

**"not found" を含む** → Not Found エラー

```php
throw new \Exception('User not found');
// → 404 Not Found 応答
```

**"unauthorized" を含む** → Unauthorized エラー

```php
throw new \Exception('Token unauthorized');
// → 401 Unauthorized 応答
```

**"forbidden" を含む** → Forbidden エラー

```php
throw new \Exception('Admin access forbidden');
// → 403 Forbidden 応答
```

#### 3. カスタムエラー生成

```php
// Not Found エラー
throw ErrorHandler::notFound('User');
// → "User not found" メッセージ

// Unauthorized エラー
throw ErrorHandler::unauthorized('Invalid token');
// → "Invalid token unauthorized" メッセージ

// Forbidden エラー
throw ErrorHandler::forbidden('Admin only');
// → "Admin only forbidden" メッセージ

// Validation エラー
throw ErrorHandler::validation([
    'email' => ['Email is required'],
    'password' => ['Password too short']
]);
```

#### 4. エラーログ

すべてのエラーは自動でログに記録されます:

```
storage/logs/error-2026-02-08.log
```

---

## 完全な例

### 新しいモジュール作成: Product

#### 1. Repository 作成

```php
<?php
// app/Modules/Product/Repositories/ProductRepository.php

namespace App\Modules\Product\Repositories;

use App\Common\Base\BaseRepository;  // ← Base を継承

class ProductRepository extends BaseRepository
{
    protected string $table = 'products';

    // BaseRepository メソッドを自動で利用可能:
    // - findAll(), findById(), findBy(), findOneBy()
    // - create(), update(), delete()
    // - count(), exists(), paginate()

    // カスタムメソッド追加
    public function findByCategory(string $category): array
    {
        // BaseRepository の findBy() を活用
        return $this->findBy(['category' => $category]);
    }

    public function searchByName(string $keyword): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE deleted_at IS NULL
                AND name LIKE ?";

        return $this->db->query($sql, ["%{$keyword}%"]);
    }
}
```

#### 2. Service 作成

```php
<?php
// app/Modules/Product/Services/ProductService.php

namespace App\Modules\Product\Services;

use App\Common\Base\BaseService;  // ← Base を継承
use App\Modules\Product\Repositories\ProductRepository;
use App\Core\Database;

class ProductService extends BaseService
{
    protected ProductRepository $productRepository;

    public function __construct()
    {
        $db = new Database();
        $this->productRepository = new ProductRepository($db);
    }

    public function createProduct(array $data): string
    {
        // BaseService の validateRequired() を使用
        $this->validateRequired($data, ['name', 'price', 'category']);

        // BaseService の validate() を使用
        $this->validate($data, [
            'name' => 'required|min:2',
            'price' => 'required|numeric'
        ]);

        // BaseService の now() を使用
        $data['created_at'] = $this->now();
        $data['updated_at'] = $this->now();

        // BaseRepository の create() を使用
        return $this->productRepository->create($data);
    }

    public function updateProduct(int $id, array $data): bool
    {
        // BaseService の except() を使用
        $data = $this->except($data, ['created_at']);

        // BaseService の now() を使用
        $data['updated_at'] = $this->now();

        // BaseRepository の update() を使用
        return $this->productRepository->update($id, $data);
    }

    public function getAllProducts(): array
    {
        // BaseRepository の findAll() を使用
        return $this->productRepository->findAll();
    }

    public function searchProducts(string $keyword): array
    {
        // カスタムメソッドを使用
        return $this->productRepository->searchByName($keyword);
    }
}
```

#### 3. Controller 作成

```php
<?php
// app/Modules/Product/Controllers/ProductController.php

namespace App\Modules\Product\Controllers;

use App\Common\Base\BaseController;  // ← Base を継承
use App\Common\Exceptions\ErrorHandler;  // ← ErrorHandler を import
use App\Core\Request;
use App\Core\Response;
use App\Modules\Product\Services\ProductService;

class ProductController extends BaseController
{
    protected ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function index(Request $request): Response
    {
        try {
            $products = $this->productService->getAllProducts();

            // BaseController の success() を使用
            return $this->success($products);

        } catch (\Exception $e) {
            // ErrorHandler を使用
            return ErrorHandler::handle($e);
        }
    }

    public function store(Request $request): Response
    {
        try {
            // BaseController の validate() を使用
            $data = $this->validate($request, [
                'name' => 'required|min:2',
                'price' => 'required|numeric',
                'category' => 'required'
            ]);

            $productId = $this->productService->createProduct($data);

            // BaseController の success() を使用
            return $this->success(['id' => $productId], 'Created', 201);

        } catch (\Exception $e) {
            // ErrorHandler を使用
            return ErrorHandler::handle($e);
        }
    }

    public function show(Request $request): Response
    {
        try {
            $id = $request->param('id');
            $product = $this->productService->getProductById($id);

            if (!$product) {
                // BaseController の notFound() を使用
                return $this->notFound('Product not found');
            }

            // BaseController の success() を使用
            return $this->success($product);

        } catch (\Exception $e) {
            // ErrorHandler を使用
            return ErrorHandler::handle($e);
        }
    }

    public function search(Request $request): Response
    {
        try {
            $keyword = $request->query('q');

            if (!$keyword) {
                // BaseController の error() を使用
                return $this->error('Search keyword is required', 400);
            }

            $products = $this->productService->searchProducts($keyword);

            // BaseController の success() を使用
            return $this->success($products);

        } catch (\Exception $e) {
            // ErrorHandler を使用
            return ErrorHandler::handle($e);
        }
    }
}
```

#### 4. ルート登録

```php
<?php
// routes/modules/product.php

global $router;

$router->get('/api/products', 'App\Modules\Product\Controllers\ProductController@index');
$router->post('/api/products', 'App\Modules\Product\Controllers\ProductController@store');
$router->get('/api/products/[i:id]', 'App\Modules\Product\Controllers\ProductController@show');
$router->get('/api/products/search', 'App\Modules\Product\Controllers\ProductController@search');
```

```php
<?php
// routes/api.php に追加

require __DIR__ . '/modules/product.php';
```

---

## チェックリスト

### 新しいモジュールを作るとき:

- [ ] **Repository 作成**
  - [ ] `BaseRepository` を継承
  - [ ] `protected string $table` を設定
  - [ ] 必要に応じてカスタムメソッドを追加

- [ ] **Service 作成**
  - [ ] `BaseService` を継承
  - [ ] Repository の依存性注入
  - [ ] `validateRequired()`, `validate()` を使用
  - [ ] `now()`, `only()`, `except()` を活用

- [ ] **Controller 作成**
  - [ ] `BaseController` を継承
  - [ ] Service の依存性注入
  - [ ] `validate()` で入力検証
  - [ ] `success()`, `error()` などで応答
  - [ ] `try-catch` で `ErrorHandler::handle()` を使用

- [ ] **ルート登録**
  - [ ] `routes/modules/{モジュール}.php` を作成
  - [ ] `routes/api.php` で require

---

## 重要ポイント

### 1. 常に Base クラスを継承！

```php
// 正しい方法
class UserController extends BaseController { }
class UserService extends BaseService { }
class UserRepository extends BaseRepository { }

// 間違った方法
class UserController { }  // Base を継承していない！
```

### 2. ErrorHandler で統一的にエラー処理！

```php
// 正しい方法
try {
    // ロジック
} catch (\Exception $e) {
    return ErrorHandler::handle($e);
}

// 間違った方法
try {
    // ロジック
} catch (\Exception $e) {
    return Response::error($e->getMessage());  // ログが残らない！
}
```

### 3. BaseRepository メソッドを最大限活用！

```php
// 正しい方法
$users = $this->userRepository->findAll();
$user = $this->userRepository->findById(1);

// 間違った方法 (不要な重複)
public function findAll(): array
{
    $sql = "SELECT * FROM users";
    return $this->db->query($sql);
}
```
