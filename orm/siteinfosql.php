<?php

namespace ORM;

use ORM\SQL;
use Utilits\Logger;

require_once 'utilits/autoloader.php';

class SiteInfoSQL extends SQL{

    private $tableName = 'site_info_list';

    public function createTable()
    {
        $query = 'CREATE TABLE IF NOT EXISTS `'.$this->tableName.'` (ID INT PRIMARY KEY AUTO_INCREMENT, SITE_NAME VARCHAR(30) NOT NULL, SITE_CMS VARCHAR(15) DEFAULT "undefined", VERSION_CMS VARCHAR(15), WIDGET BOOLEAN)ENGINE=innoDB DEFAULT CHARSET=utf8';

        $state = $this->executeQuery($query);
        
        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[3]);
        }else {
            Logger::log('Create table', $this->tableName);
        }
    }
}
