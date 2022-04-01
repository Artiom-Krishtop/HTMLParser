<?php

namespace Orm;

use Configs\DataBaseConfig;
use Utilits\Logger;
use PDO;
use PDOException;
use PDOStatement;

class Connection{

    protected $DB;
    protected $DBname;

    function __construct($DBconfig)
    {        
        try {
        $this->DB = new PDO('mysql:dbname='.$DBconfig['database'].';host='.$DBconfig['hostname'], $DBconfig['username'], $DBconfig['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        } catch (\PDOException $e) {
            
            Logger::errorLog('DB', $e->getMessage());
            die($e->getMessage());
        }

        $this->DBname = $DBconfig['database'];
    }

    public function getConnection()
    {
        return $this->DB;
    }

    public static function createDB($DBconfig)
    {
        try {
            $DB = new PDO('mysql:host='.$DBconfig['hostname'], $DBconfig['username'], $DBconfig['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        } catch (\PDOException $e) {
            
            Logger::errorLog('DB', $e->getMessage());
            die($e->getMessage());
        }

        $query = 'CREATE DATABASE IF NOT EXISTS `'. $DBconfig['database'] .'`';

        $state = $DB->query($query);

        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[2]);
        }else {
            Logger::log('Create DB:',$DBconfig['database']);
        }
    }

    public function DBlist()
    {
        $query = 'SHOW DATABASES';

        $state = $query($query);

        return $state->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteDB()
    {
        $query = 'DROP DATABASE `'. DataBaseConfig::getDBName().'`';

        $state = $query($query);

        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[2]);
        }else {
            Logger::log('Delete DB:', DataBaseConfig::getDBName());
        }
    }
}