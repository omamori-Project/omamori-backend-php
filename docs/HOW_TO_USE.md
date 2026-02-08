# 클래스 사용 가이드

이 문서는 **언제, 어떻게** Base 클래스들을 상속받고 메서드를 사용하는지 설명합니다.

---

## 목차

1. [BaseController 사용법](#basecontroller)
2. [BaseService 사용법](#baseservice)
3. [BaseRepository 사용법](#baserepository)
4. [ErrorHandler 사용법](#errorhandler)
5. [예시](#예시)

---

## BaseController

### 위치

```
app/Common/Base/BaseController.php
```

### 언제 상속받나요?

**모든 Controller 클래스**는 BaseController를 상속받아야 합니다.

### 왜 상속받나요?

공통 기능을 중복 작성하지 않기 위해:

- 입력 검증 (validate)
- 응답 헬퍼 (success, error 등)

### 상속 방법

```php
<?php

namespace App\Modules\User\Controllers;

use App\Common\Base\BaseController;  // ← Base 클래스 import
use App\Core\Request;
use App\Core\Response;

class UserController extends BaseController  // ← 상속!
{
    public function store(Request $request): Response
    {
        // BaseController의 메서드를 사용할 수 있음!
    }
}
```

---

### 사용 가능한 메서드

#### 1. `validate()` - 입력 검증

```php
public function store(Request $request): Response
{
    // BaseController의 validate() 메서드 호출
    $data = $this->validate($request, [
        'email' => 'required|email',
        'name' => 'required|min:2|max:50',
        'password' => 'required|min:6',
        'age' => 'numeric'
    ]);

    // $data는 검증된 깨끗한 데이터
    // 검증 실패 시 자동으로 InvalidArgumentException 발생
}
```

**지원하는 규칙:**

- `required` - 필수
- `email` - 이메일 형식
- `min:n` - 최소 길이
- `max:n` - 최대 길이
- `numeric` - 숫자만

---

#### 2. `success()` - 성공 응답

```php
public function index(Request $request): Response
{
    $users = $this->userService->getAllUsers();

    // BaseController의 success() 메서드
    return $this->success($users, 'Users retrieved successfully');

    // 응답 예시:
    // {
    //   "success": true,
    //   "message": "Users retrieved successfully",
    //   "data": [...]
    // }
}
```

**파라미터:**

- `$data` - 응답 데이터
- `$message` - 메시지 (기본: 'Success')
- `$statusCode` - HTTP 상태 코드 (기본: 200)

---

#### 3. `error()` - 에러 응답

```php
public function update(Request $request): Response
{
    // BaseController의 error() 메서드
    return $this->error('Something went wrong', 500);

    // 응답 예시:
    // {
    //   "success": false,
    //   "message": "Something went wrong"
    // }
}
```

---

#### 4. `notFound()` - Not Found 응답

```php
public function show(Request $request): Response
{
    $id = $request->param('id');
    $user = $this->userService->getUserById($id);

    if (!$user) {
        // BaseController의 notFound() 메서드
        return $this->notFound('User not found');
    }

    return $this->success($user);
}
```

---

#### 5. `unauthorized()` - 인증 실패 응답

```php
public function profile(Request $request): Response
{
    $token = $request->bearerToken();

    if (!$token) {
        // BaseController의 unauthorized() 메서드
        return $this->unauthorized('Token required');
    }

    // ...
}
```

---

#### 6. `forbidden()` - 권한 없음 응답

```php
public function delete(Request $request): Response
{
    if (!$this->isAdmin()) {
        // BaseController의 forbidden() 메서드
        return $this->forbidden('Admin only');
    }

    // ...
}
```

---

#### 7. `validationError()` - Validation 에러 응답

```php
public function store(Request $request): Response
{
    $errors = [
        'email' => ['Email is already taken'],
        'password' => ['Password too weak']
    ];

    // BaseController의 validationError() 메서드
    return $this->validationError($errors);

    // 응답 예시:
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

### 완전한 Controller 예시

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
            // validate() 사용
            $data = $this->validate($request, [
                'email' => 'required|email',
                'name' => 'required|min:2',
                'password' => 'required|min:6'
            ]);

            $userId = $this->userService->createUser($data);

            // success() 사용
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
                // notFound() 사용
                return $this->notFound('User not found');
            }

            // success() 사용
            return $this->success($user);

        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }
}
```

---

## BaseService

### 위치

```
app/Common/Base/BaseService.php
```

### 언제 상속받나요?

**모든 Service 클래스**는 BaseService를 상속받아야 합니다.

### 왜 상속받나요?

공통 비즈니스 로직 메서드를 사용하기 위해:

- 검증 (validate, validateRequired)
- 유틸리티 (now, only, except)

### 상속 방법

```php
<?php

namespace App\Modules\User\Services;

use App\Common\Base\BaseService;  // ← Base 클래스 import

class UserService extends BaseService  // ← 상속!
{
    public function createUser(array $data): string
    {
        // BaseService의 메서드를 사용할 수 있음!
    }
}
```

---

### 사용 가능한 메서드

#### 1. `validateRequired()` - 필수 필드 검증

```php
public function createUser(array $data): string
{
    // BaseService의 validateRequired() 메서드
    $this->validateRequired($data, ['email', 'password', 'name']);

    // email, password, name이 없으면 InvalidArgumentException 발생

    // 검증 통과하면 계속 진행
    // ...
}
```

---

#### 2. `validate()` - 규칙 기반 검증

```php
public function updateProfile(array $data): bool
{
    // BaseService의 validate() 메서드
    $this->validate($data, [
        'name' => 'required|min:2',
        'email' => 'email',
        'age' => 'numeric'
    ]);

    // 검증 통과하면 계속 진행
    // ...
}
```

---

#### 3. `now()` - 현재 타임스탬프

```php
public function createUser(array $data): string
{
    // BaseService의 now() 메서드
    $data['created_at'] = $this->now();  // '2026-02-08 12:34:56'
    $data['updated_at'] = $this->now();

    return $this->userRepository->create($data);
}
```

---

#### 4. `only()` - 특정 키만 추출

```php
public function updateUser(array $data): bool
{
    // BaseService의 only() 메서드
    // name, email만 추출
    $filtered = $this->only($data, ['name', 'email']);

    // $filtered = ['name' => '...', 'email' => '...']

    return $this->userRepository->update($id, $filtered);
}
```

---

#### 5. `except()` - 특정 키 제외

```php
public function updateUser(array $data): bool
{
    // BaseService의 except() 메서드
    // created_at 제외
    $filtered = $this->except($data, ['created_at']);

    return $this->userRepository->update($id, $filtered);
}
```

---

### 완전한 Service 예시

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
        // validateRequired() 사용
        $this->validateRequired($data, ['email', 'password', 'name']);

        // 이메일 중복 체크
        if ($this->userRepository->emailExists($data['email'])) {
            throw new \InvalidArgumentException('Email already exists');
        }

        // 비밀번호 해싱
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // now() 사용
        $data['created_at'] = $this->now();
        $data['updated_at'] = $this->now();

        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data): bool
    {
        // except() 사용 - created_at은 수정 불가
        $data = $this->except($data, ['created_at', 'id']);

        // now() 사용
        $data['updated_at'] = $this->now();

        return $this->userRepository->update($id, $data);
    }
}
```

---

## BaseRepository

### 위치

```
app/Common/Base/BaseRepository.php
```

### 언제 상속받나요?

**모든 Repository 클래스**는 BaseRepository를 상속받아야 합니다.

### 왜 상속받나요?

기본 CRUD를 자동으로 사용하기 위해:

- findAll, findById, findBy, findOneBy
- create, update, delete
- count, exists, paginate

### 상속 방법

```php
<?php

namespace App\Modules\User\Repositories;

use App\Common\Base\BaseRepository;  // ← Base 클래스 import

class UserRepository extends BaseRepository  // ← 상속!
{
    protected string $table = 'users';  // ← 테이블 이름 지정
    protected string $primaryKey = 'id';  // ← Primary Key (기본: 'id')

    // BaseRepository의 메서드를 자동으로 사용 가능!
}
```

---

### 자동으로 사용 가능한 메서드

#### 1. `findAll()` - 전체 조회

```php
// UserRepository 인스턴스
$users = $userRepository->findAll();

// SELECT * FROM users WHERE deleted_at IS NULL ORDER BY id DESC

// 정렬 옵션
$users = $userRepository->findAll(['created_at' => 'DESC', 'name' => 'ASC']);
```

---

#### 2. `findById()` - ID로 조회

```php
$user = $userRepository->findById(1);

// SELECT * FROM users WHERE id = 1 AND deleted_at IS NULL

if ($user) {
    echo $user['name'];
}
```

---

#### 3. `findBy()` - 조건으로 조회 (여러 개)

```php
// status가 'active'인 모든 사용자
$users = $userRepository->findBy(['status' => 'active']);

// SELECT * FROM users WHERE status = 'active' AND deleted_at IS NULL

// 여러 조건
$users = $userRepository->findBy([
    'status' => 'active',
    'role' => 'admin'
]);

// 정렬
$users = $userRepository->findBy(
    ['status' => 'active'],
    ['created_at' => 'DESC']
);
```

---

#### 4. `findOneBy()` - 조건으로 조회 (한 개)

```php
// 이메일로 사용자 찾기
$user = $userRepository->findOneBy(['email' => 'user@example.com']);

// SELECT * FROM users WHERE email = 'user@example.com' AND deleted_at IS NULL LIMIT 1

if ($user) {
    echo $user['name'];
}
```

---

#### 5. `create()` - 생성

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

#### 6. `update()` - 수정

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

#### 7. `delete()` - 삭제 (소프트 삭제)

```php
$success = $userRepository->delete(1);

// UPDATE users SET deleted_at = ? WHERE id = ?
// 물리적으로 삭제하지 않고 deleted_at만 설정!

if ($success) {
    echo "Deleted successfully";
}
```

---

#### 8. `hardDelete()` - 하드 삭제 (물리적 삭제)

```php
$success = $userRepository->hardDelete(1);

// DELETE FROM users WHERE id = ?
// 진짜 삭제됨!

if ($success) {
    echo "Permanently deleted";
}
```

---

#### 9. `count()` - 개수 세기

```php
// 전체 개수
$total = $userRepository->count();

// SELECT COUNT(*) as count FROM users WHERE deleted_at IS NULL

// 조건부 개수
$activeCount = $userRepository->count(['status' => 'active']);

echo "Total users: {$total}";
```

---

#### 10. `exists()` - 존재 여부

```php
// 이메일 중복 체크
$exists = $userRepository->exists(['email' => 'user@example.com']);

if ($exists) {
    echo "Email already taken";
}
```

---

#### 11. `paginate()` - 페이지네이션

```php
// 1페이지, 15개씩
$result = $userRepository->paginate(1, 15);

// 결과 구조:
// [
//   'data' => [...],           // 실제 데이터
//   'current_page' => 1,       // 현재 페이지
//   'per_page' => 15,          // 페이지당 개수
//   'total' => 100,            // 전체 개수
//   'last_page' => 7           // 마지막 페이지
// ]

foreach ($result['data'] as $user) {
    echo $user['name'];
}

echo "Page {$result['current_page']} of {$result['last_page']}";
```

---

### 커스텀 메서드 추가

BaseRepository를 상속받은 후, **커스텀 메서드**를 추가할 수 있습니다:

```php
<?php

namespace App\Modules\User\Repositories;

use App\Common\Base\BaseRepository;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    /**
     * 이메일로 사용자 찾기
     *
     * BaseRepository의 findOneBy() 활용
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * 최근 가입자 조회
     *
     * 직접 쿼리 작성
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
     * 사용자 검색
     *
     * 직접 쿼리 작성
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
     * 이메일 중복 체크 (수정 시 자신 제외)
     *
     * BaseRepository의 exists() 활용 + 커스텀 로직
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

**사용 예시:**

```php
// BaseRepository 메서드 사용
$allUsers = $userRepository->findAll();
$user = $userRepository->findById(1);

// 커스텀 메서드 사용
$user = $userRepository->findByEmail('user@example.com');
$recent = $userRepository->findRecentUsers(5);
$results = $userRepository->search('john');
```

---

## ErrorHandler

### 위치

```
app/Common/Exceptions/ErrorHandler.php
```

### 언제 사용하나요?

**모든 try-catch 블록**에서 ErrorHandler를 사용합니다.

### 왜 사용하나요?

- 공통 에러 처리
- 에러 로그 자동 기록
- 사용자 친화적 에러 메시지

### 사용 방법

#### 1. Controller에서 사용

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
            // 로직...

        } catch (\Exception $e) {
            // ErrorHandler 사용
            return ErrorHandler::handle($e);
        }
    }
}
```

#### 2. 자동 처리되는 예외들

**InvalidArgumentException** → Validation 에러

```php
throw new \InvalidArgumentException('Email already exists');
// → 422 Validation Error 응답
```

**PDOException** → 데이터베이스 에러

```php
// DB 에러 발생
// → 500 Database Error 응답
```

**"not found" 포함** → Not Found 에러

```php
throw new \Exception('User not found');
// → 404 Not Found 응답
```

**"unauthorized" 포함** → Unauthorized 에러

```php
throw new \Exception('Token unauthorized');
// → 401 Unauthorized 응답
```

**"forbidden" 포함** → Forbidden 에러

```php
throw new \Exception('Admin access forbidden');
// → 403 Forbidden 응답
```

#### 3. 커스텀 에러 생성

```php
// Not Found 에러
throw ErrorHandler::notFound('User');
// → "User not found" 메시지

// Unauthorized 에러
throw ErrorHandler::unauthorized('Invalid token');
// → "Invalid token unauthorized" 메시지

// Forbidden 에러
throw ErrorHandler::forbidden('Admin only');
// → "Admin only forbidden" 메시지

// Validation 에러
throw ErrorHandler::validation([
    'email' => ['Email is required'],
    'password' => ['Password too short']
]);
```

#### 4. 에러 로그

모든 에러는 자동으로 로그에 기록됩니다:

```
storage/logs/error-2026-02-08.log
```

---

## 완전한 예시

### 새 모듈 만들기: Product

#### 1. Repository 작성

```php
<?php
// app/Modules/Product/Repositories/ProductRepository.php

namespace App\Modules\Product\Repositories;

use App\Common\Base\BaseRepository;  // ← Base 상속

class ProductRepository extends BaseRepository
{
    protected string $table = 'products';

    // BaseRepository 메서드 자동 사용 가능:
    // - findAll(), findById(), findBy(), findOneBy()
    // - create(), update(), delete()
    // - count(), exists(), paginate()

    // 커스텀 메서드 추가
    public function findByCategory(string $category): array
    {
        // BaseRepository의 findBy() 활용
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

#### 2. Service 작성

```php
<?php
// app/Modules/Product/Services/ProductService.php

namespace App\Modules\Product\Services;

use App\Common\Base\BaseService;  // ← Base 상속
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
        // BaseService의 validateRequired() 사용
        $this->validateRequired($data, ['name', 'price', 'category']);

        // BaseService의 validate() 사용
        $this->validate($data, [
            'name' => 'required|min:2',
            'price' => 'required|numeric'
        ]);

        // BaseService의 now() 사용
        $data['created_at'] = $this->now();
        $data['updated_at'] = $this->now();

        // BaseRepository의 create() 사용
        return $this->productRepository->create($data);
    }

    public function updateProduct(int $id, array $data): bool
    {
        // BaseService의 except() 사용
        $data = $this->except($data, ['created_at']);

        // BaseService의 now() 사용
        $data['updated_at'] = $this->now();

        // BaseRepository의 update() 사용
        return $this->productRepository->update($id, $data);
    }

    public function getAllProducts(): array
    {
        // BaseRepository의 findAll() 사용
        return $this->productRepository->findAll();
    }

    public function searchProducts(string $keyword): array
    {
        // 커스텀 메서드 사용
        return $this->productRepository->searchByName($keyword);
    }
}
```

#### 3. Controller 작성

```php
<?php
// app/Modules/Product/Controllers/ProductController.php

namespace App\Modules\Product\Controllers;

use App\Common\Base\BaseController;  // ← Base 상속
use App\Common\Exceptions\ErrorHandler;  // ← ErrorHandler import
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

            // BaseController의 success() 사용
            return $this->success($products);

        } catch (\Exception $e) {
            // ErrorHandler 사용
            return ErrorHandler::handle($e);
        }
    }

    public function store(Request $request): Response
    {
        try {
            // BaseController의 validate() 사용
            $data = $this->validate($request, [
                'name' => 'required|min:2',
                'price' => 'required|numeric',
                'category' => 'required'
            ]);

            $productId = $this->productService->createProduct($data);

            // BaseController의 success() 사용
            return $this->success(['id' => $productId], 'Created', 201);

        } catch (\Exception $e) {
            // ErrorHandler 사용
            return ErrorHandler::handle($e);
        }
    }

    public function show(Request $request): Response
    {
        try {
            $id = $request->param('id');
            $product = $this->productService->getProductById($id);

            if (!$product) {
                // BaseController의 notFound() 사용
                return $this->notFound('Product not found');
            }

            // BaseController의 success() 사용
            return $this->success($product);

        } catch (\Exception $e) {
            // ErrorHandler 사용
            return ErrorHandler::handle($e);
        }
    }

    public function search(Request $request): Response
    {
        try {
            $keyword = $request->query('q');

            if (!$keyword) {
                // BaseController의 error() 사용
                return $this->error('Search keyword is required', 400);
            }

            $products = $this->productService->searchProducts($keyword);

            // BaseController의 success() 사용
            return $this->success($products);

        } catch (\Exception $e) {
            // ErrorHandler 사용
            return ErrorHandler::handle($e);
        }
    }
}
```

#### 4. 라우트 등록

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
// routes/api.php에 추가

require __DIR__ . '/modules/product.php';
```

---

## 체크리스트

### 새 모듈 만들 때:

- [ ] **Repository 작성**
  - [ ] `BaseRepository` 상속
  - [ ] `protected string $table` 설정
  - [ ] 필요시 커스텀 메서드 추가

- [ ] **Service 작성**
  - [ ] `BaseService` 상속
  - [ ] Repository 의존성 주입
  - [ ] `validateRequired()`, `validate()` 사용
  - [ ] `now()`, `only()`, `except()` 활용

- [ ] **Controller 작성**
  - [ ] `BaseController` 상속
  - [ ] Service 의존성 주입
  - [ ] `validate()` 로 입력 검증
  - [ ] `success()`, `error()` 등으로 응답
  - [ ] `try-catch`에서 `ErrorHandler::handle()` 사용

- [ ] **라우트 등록**
  - [ ] `routes/modules/{모듈}.php` 생성
  - [ ] `routes/api.php`에서 require

---

## 핵심 포인트

### 1. 항상 Base 클래스 상속!

```php
// 올바른 방법
class UserController extends BaseController { }
class UserService extends BaseService { }
class UserRepository extends BaseRepository { }

// 잘못된 방법
class UserController { }  // Base 상속 안 함!
```

### 2. ErrorHandler로 통합 에러 처리!

```php
// 올바른 방법
try {
    // 로직
} catch (\Exception $e) {
    return ErrorHandler::handle($e);
}

// 잘못된 방법
try {
    // 로직
} catch (\Exception $e) {
    return Response::error($e->getMessage());  // 로그 안 남음!
}
```

### 3. BaseRepository 메서드 최대한 활용!

```php
// 올바른 방법
$users = $this->userRepository->findAll();
$user = $this->userRepository->findById(1);

// 잘못된 방법 (불필요한 중복)
public function findAll(): array
{
    $sql = "SELECT * FROM users";
    return $this->db->query($sql);
}
```
