<?php

namespace App\Modules\Omamori\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;

class FileRepository extends BaseRepository{
    protected string $table = 'files';

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }

    public function findByFileKey(string $fileKey): ?array{
        $sql = "SELECT id, file_key
                FROM {$this -> table}
                WHERE file_key = ?
                    AND deleted_at IS NULL";

        return $this -> db -> queryOne($sql, [$fileKey]) ?: null;
    }
}