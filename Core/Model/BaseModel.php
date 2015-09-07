<?php
namespace Core\Model;

use SlimModel\Base;
use Doctrine\DBAL\DriverManager;

class BaseModel extends Base
{

    public function __construct()
    {

        $db = DriverManager::getConnection(require '/config/database.php');
        parent::__construct($db);
    }

}