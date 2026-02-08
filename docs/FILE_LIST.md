# 오마모리 PHP 프로젝트 - 전체 파일 목록

## 완전한 폴더 구조

```
omamori-php-final/
│
├── README.md                                    프로젝트 전체 가이드
├── composer.json                                Composer 설정 (AltoRouter 포함)
├── docker-compose.yml                           Docker 설정
├── .env.example                                 환경 변수 템플릿
├── .gitignore                                   Git 무시 파일
│
├── app/                                         애플리케이션 코어
│   │
│   ├── Core/                                    프레임워크 핵심 시스템
│   │   ├── Application.php                      애플리케이션 컨테이너
│   │   ├── Router.php                           AltoRouter 기반 라우팅
│   │   ├── Request.php                          HTTP 요청 처리
│   │   ├── Response.php                         HTTP 응답 생성
│   │   └── Database.php                         PDO PostgreSQL 연결
│   │
│   ├── Common/                                  공통 기능
│   │   │
│   │   ├── Base/                                Base 클래스들
│   │   │   ├── BaseController.php               모든 Controller의 부모
│   │   │   │                                       - validate() 입력 검증
│   │   │   │                                       - success() 성공 응답
│   │   │   │                                       - error() 에러 응답
│   │   │   │                                       - notFound() 404 응답
│   │   │   │
│   │   │   ├── BaseService.php                  모든 Service의 부모
│   │   │   │                                       - validateRequired()
│   │   │   │                                       - now() 타임스탬프
│   │   │   │                                       - only() 키 추출
│   │   │   │                                       - except() 키 제외
│   │   │   │
│   │   │   └── BaseRepository.php               모든 Repository의 부모
│   │   │                                           - findAll() 전체 조회
│   │   │                                           - findById() ID 조회
│   │   │                                           - create() 생성
│   │   │                                           - update() 수정
│   │   │                                           - delete() 삭제
│   │   │                                           - paginate() 페이징
│   │   │
│   │   ├── Exceptions/                          에러 처리
│   │   │   └── ErrorHandler.php                 통합 에러 핸들러
│   │   │                                           - handle() 예외 처리
│   │   │                                           - 자동 로그 기록
│   │   │                                           - 타입별 응답
│   │   │
│   │   ├── Helpers/                             유틸리티
│   │   │   └── functions.php                    env(), dd(), dump()
│   │   │
│   │   └── Middlewares/                         미들웨어 (추후 추가)
│   │
│   └── Modules/                                 기능 모듈
│       │
│       ├── User/                                사용자 모듈 완성
│       │   ├── Controllers/
│       │   │   └── UserController.php           HTTP 요청 처리
│       │   │                                       - index() 목록
│       │   │                                       - show() 상세
│       │   │                                       - store() 생성
│       │   │                                       - update() 수정
│       │   │                                       - destroy() 삭제
│       │   │
│       │   ├── Services/
│       │   │   └── UserService.php              비즈니스 로직
│       │   │                                       - getAllUsers()
│       │   │                                       - createUser()
│       │   │                                       - authenticate()
│       │   │
│       │   └── Repositories/
│       │       └── UserRepository.php           DB 접근
│       │                                           - findByEmail()
│       │                                           - emailExists()
│       │                                           - search()
│       │
│       ├── Auth/                                인증 모듈 구현 필요
│       │   ├── Controllers/
│       │   ├── Services/
│       │   └── Repositories/
│       │
│       ├── Omamori/                             오마모리 모듈 구현 필요
│       │   ├── Controllers/
│       │   ├── Services/
│       │   └── Repositories/
│       │
│       └── Community/                           커뮤니티 모듈 구현 필요
│           ├── Controllers/
│           ├── Services/
│           └── Repositories/
│
├── bootstrap/                                   부트스트랩
│   └── app.php                                  애플리케이션 초기화
│                                                   - Autoload 로드
│                                                   - .env 로드
│                                                   - 설정 로드
│
├── config/                                      설정 파일
│   └── database.php                             DB 연결 정보
│
├── database/                                    데이터베이스
│   ├── migrations/                              SQL 마이그레이션 (추후 추가)
│   └── seeds/                                   시드 데이터 (추후 추가)
│
├── docker/                                      Docker 설정
│   ├── nginx/
│   │   └── nginx.conf                           Nginx 웹 서버 설정
│   └── php/
│       └── Dockerfile                           PHP-FPM 컨테이너 이미지
│
├── docs/                                        문서
│   └── HOW_TO_USE.md                            클래스 사용 완벽 가이드
│                                                   - BaseController 사용법
│                                                   - BaseService 사용법
│                                                   - BaseRepository 사용법
│                                                   - ErrorHandler 사용법
│                                                   - 완전한 예시 코드
│
├── public/                                      웹 루트
│   └── index.php                                진입점 (모든 요청)
│
├── routes/                                      라우팅
│   ├── api.php                                  메인 라우트 파일
│   └── modules/                                 모듈별 라우트
│       ├── user.php                             User 라우트
│       ├── auth.php                             Auth 라우트
│       ├── omamori.php                          Omamori 라우트
│       └── community.php                        Community 라우트
│
├── storage/                                     저장소 (자동 생성)
│   ├── omamori/
│   │   ├── layers/                              레이어 이미지
│   │   ├── generated/                           생성된 오마모리
│   │   └── temp/                                임시 파일
│   ├── logs/                                    로그 파일
│   └── cache/                                   캐시
│
├── tests/                                       테스트 (추후 추가)
│
└── vendor/                                      Composer 패키지 (자동 생성)
```

---

## 파일 개수 통계

### Core 시스템 (5개)

- Application.php
- Router.php
- Request.php
- Response.php
- Database.php

### Base 클래스 (3개)

- BaseController.php
- BaseService.php
- BaseRepository.php

### Common (2개)

- ErrorHandler.php
- functions.php

### User 모듈 (3개)

- UserController.php
- UserService.php
- UserRepository.php

### 라우트 (5개)

- api.php
- user.php
- auth.php
- omamori.php
- community.php

### 설정/Docker (5개)

- composer.json
- docker-compose.yml
- nginx.conf
- Dockerfile
- database.php

### 문서 (2개)

- README.md
- HOW_TO_USE.md

### 기타 (4개)

- .env.example
- .gitignore
- app.php (bootstrap)
- index.php (public)

**총 파일: 29개**

---

## 핵심 파일 설명

### 반드시 읽어야 할 문서

| 파일                   | 설명                              |
| ---------------------- | --------------------------------- |
| **README.md**          | 프로젝트 전체 가이드              |
| **docs/HOW_TO_USE.md** | 클래스 사용법 완벽 가이드 (50KB+) |

### Base 클래스들 (공통 기능)

| 파일                   | 역할                   | 주요 메서드                       |
| ---------------------- | ---------------------- | --------------------------------- |
| **BaseController.php** | 모든 Controller의 부모 | validate(), success(), error()    |
| **BaseService.php**    | 모든 Service의 부모    | validateRequired(), now(), only() |
| **BaseRepository.php** | 모든 Repository의 부모 | findAll(), create(), paginate()   |

### 에러 처리

| 파일                 | 역할                                |
| -------------------- | ----------------------------------- |
| **ErrorHandler.php** | Try-Catch 통합 에러 처리, 자동 로깅 |

### 모듈 예시 (User)

| 파일                   | 역할                            |
| ---------------------- | ------------------------------- |
| **UserController.php** | HTTP 요청 → Service 호출        |
| **UserService.php**    | 비즈니스 로직 → Repository 호출 |
| **UserRepository.php** | DB CRUD 작업                    |

### 라우팅

| 파일                      | 역할                         |
| ------------------------- | ---------------------------- |
| **routes/api.php**        | 메인 라우트 (모든 모듈 로드) |
| **routes/modules/\*.php** | 각 모듈별 엔드포인트 정의    |

### 핵심 시스템

| 파일             | 역할                   |
| ---------------- | ---------------------- |
| **Router.php**   | AltoRouter 기반 라우팅 |
| **Database.php** | PDO PostgreSQL 연결    |
| **Request.php**  | HTTP 요청 파싱         |
| **Response.php** | JSON 응답 생성         |

---

## 파일 색상 가이드

- **완성됨** (User 모듈)
- **폴더만 있음** (Auth, Omamori, Community)
- **핵심 파일** (Base 클래스, ErrorHandler)
- **문서** (README, HOW_TO_USE)

---

## 파일 찾기 가이드

### BaseController 어디 있나요?

```
app/Common/Base/BaseController.php
```

### User 모듈은 어디 있나요?

```
app/Modules/User/
├── Controllers/UserController.php
├── Services/UserService.php
└── Repositories/UserRepository.php
```

### 라우트는 어디서 등록하나요?

```
routes/modules/user.php  ← 여기서 등록
routes/api.php           ← 여기서 로드
```

### Docker 설정은 어디 있나요?

```
docker-compose.yml       ← 메인 설정
docker/nginx/nginx.conf  ← Nginx 설정
docker/php/Dockerfile    ← PHP 설정
```

### 사용법 가이드는 어디 있나요?

```
docs/HOW_TO_USE.md  ← ⭐ 여기!!!
```

---

## 다운로드 후 실행 순서

```bash
# 1. 프로젝트 폴더로 이동
cd omamori-php-final

# 2. .env 생성
cp .env.example .env

# 3. Docker 시작
docker-compose up -d --build

# 4. Composer 설치
docker-compose exec php composer install

# 5. 접속 확인
curl http://localhost:8080/api/health
```

---

## 개발 시작 순서

1. **[README.md](README.md)** 읽기
2. **[docs/HOW_TO_USE.md](docs/HOW_TO_USE.md)** 정독하기
3. **User 모듈** 코드 분석하기
4. **새 모듈** 만들어보기

---
