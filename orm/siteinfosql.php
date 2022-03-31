<?php

namespace ORM;

use ORM\SQL;
use Utilits\Logger;

require_once 'utilits/autoloader.php';

class SiteInfoSQL extends SQL{

    protected function createTable()
    {
        $query = 'CREATE TABLE IF NOT EXISTS `'.$this->tableName.'` (SITE_NAME VARCHAR(30) PRIMARY KEY NOT NULL UNIQUE, SITE_CMS VARCHAR(15) DEFAULT "undefined", VERSION_CMS VARCHAR(15) DEFAULT NULL, WIDGET BOOLEAN DEFAULT false)ENGINE=innoDB DEFAULT CHARSET=utf8';

        $state = $this->executeQuery($query);
        
        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[3]);
        }else {
            Logger::log('Create table', $this->tableName);
        }
    }
}
