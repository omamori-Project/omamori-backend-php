<?php

namespace App\Modules\Custom\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class FrameRepository extends BaseRepository{
    protected string $table = 'frames';
    protected string $primaryKey = 'id';

    public function __construct(Database $db){
        parent::__construct($db);
    }
}