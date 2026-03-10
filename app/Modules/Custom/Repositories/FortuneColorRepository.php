<?php

namespace App\Modules\Custom\Repositories;

// import
use App\Common\Base\BaseRepository;
use App\Core\Database;


// 상속
class FortuneColorRepository extends BaseRepository{
    // table명
    protected string $table = 'fortune_colors';

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }
}