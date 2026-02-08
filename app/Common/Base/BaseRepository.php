<?php

namespace App\Common\Base;

use App\Core\Database;

/**
 * BaseRepository
 * 
 * 모든 리포지토리가 상속받는 베이스 리포지토리
 * 
 * 제공 기능:
 * - 기본 CRUD (Create, Read, Update, Delete)
 * - 검색/필터링
 * - 페이지네이션
 * - 트랜잭션 지원
 * 
 * 사용 예시:
 * class UserRepository extends BaseRepository
 * {
 *     protected string $table = 'users';
 *     
 *     public function findByEmail(string $email): ?array
 *     {
 *         return $this->findOneBy(['email' => $email]);
 *     }
 * }
 */
abstract class BaseRepository
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    
    public function __construct(Database $db)
    {
        $this->db = $db;
    }
    
    /**
     * 전체 조회
     */
    public function findAll(array $orderBy = []): array
    {
        $order = '';
        if (!empty($orderBy)) {
            $orderParts = [];
            foreach ($orderBy as $column => $direction) {
                $orderParts[] = "$column $direction";
            }
            $order = 'ORDER BY ' . implode(', ', $orderParts);
        } else {
            $order = "ORDER BY {$this->primaryKey} DESC";
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL {$order}";
        return $this->db->query($sql);
    }
    
    /**
     * ID로 조회
     */
    public function findById($id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? AND deleted_at IS NULL";
        return $this->db->queryOne($sql, [$id]);
    }
    
    /**
     * 조건으로 조회 (여러 개)
     */
    public function findBy(array $criteria, array $orderBy = []): array
    {
        $conditions = [];
        $params = [];
        
        foreach ($criteria as $key => $value) {
            $conditions[] = "$key = ?";
            $params[] = $value;
        }
        
        $where = implode(' AND ', $conditions);
        
        $order = '';
        if (!empty($orderBy)) {
            $orderParts = [];
            foreach ($orderBy as $column => $direction) {
                $orderParts[] = "$column $direction";
            }
            $order = 'ORDER BY ' . implode(', ', $orderParts);
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE $where AND deleted_at IS NULL {$order}";
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * 조건으로 조회 (한 개)
     */
    public function findOneBy(array $criteria): ?array
    {
        $conditions = [];
        $params = [];
        
        foreach ($criteria as $key => $value) {
            $conditions[] = "$key = ?";
            $params[] = $value;
        }
        
        $where = implode(' AND ', $conditions);
        $sql = "SELECT * FROM {$this->table} WHERE $where AND deleted_at IS NULL LIMIT 1";
        
        return $this->db->queryOne($sql, $params);
    }
    
    /**
     * 생성
     */
    public function create(array $data): string
    {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s) RETURNING %s",
            $this->table,
            implode(', ', $fields),
            implode(', ', $placeholders),
            $this->primaryKey
        );
        
        $result = $this->db->queryOne($sql, array_values($data));
        return $result[$this->primaryKey];
    }
    
    /**
     * 수정
     */
    public function update($id, array $data): bool
    {
        $sets = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $sets[] = "$key = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s = ?",
            $this->table,
            implode(', ', $sets),
            $this->primaryKey
        );
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * 삭제 (소프트 삭제)
     */
    public function delete($id): bool
    {
        $sql = "UPDATE {$this->table} SET deleted_at = ? WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, [date('Y-m-d H:i:s'), $id]);
    }
    
    /**
     * 하드 삭제 (물리적 삭제)
     */
    public function hardDelete($id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    /**
     * 개수 세기
     */
    public function count(array $criteria = []): int
    {
        if (empty($criteria)) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE deleted_at IS NULL";
            $result = $this->db->queryOne($sql);
        } else {
            $conditions = [];
            $params = [];
            
            foreach ($criteria as $key => $value) {
                $conditions[] = "$key = ?";
                $params[] = $value;
            }
            
            $where = implode(' AND ', $conditions);
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE $where AND deleted_at IS NULL";
            $result = $this->db->queryOne($sql, $params);
        }
        
        return (int) $result['count'];
    }
    
    /**
     * 존재 여부 확인
     */
    public function exists(array $criteria): bool
    {
        return $this->count($criteria) > 0;
    }
    
    /**
     * 페이지네이션
     */
    public function paginate(int $page = 1, int $perPage = 15, array $criteria = []): array
    {
        $offset = ($page - 1) * $perPage;
        
        $where = '';
        $params = [];
        
        if (!empty($criteria)) {
            $conditions = [];
            foreach ($criteria as $key => $value) {
                $conditions[] = "$key = ?";
                $params[] = $value;
            }
            $where = 'AND ' . implode(' AND ', $conditions);
        }
        
        // 데이터 조회
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL {$where} 
                ORDER BY {$this->primaryKey} DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $items = $this->db->query($sql, $params);
        
        // 전체 개수
        $total = $this->count($criteria);
        
        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => (int) ceil($total / $perPage)
        ];
    }
}
