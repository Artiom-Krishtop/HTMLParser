<?php

namespace ORM;

use PDO;
use PDOStatement;
use Configs\DataBaseConfig;
use ORM\Connection;
use Utilits\Logger;

require_once ROOT . '/utilits/autoloader.php';

abstract class SQL{

    protected $DB;
    protected $tableName;

    function __construct($DBconfig, $tableName)
    {        
        $DBconnection = new Connection($DBconfig);

        $this->DB = $DBconnection->getConnection();

        $this->tableName = $tableName;

        $this->createTable();
    }

    public function deleteTable()
    {
        $query = 'DROP TABLE '.$this->tableName;

        $state = $this->executeQuery($query);
        
        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[2]);
        }
    }

    public function structTable()
    {
        $query = 'SHOW CREATE TABLE `'.$this->tableName.'`';

        $state = $this->executeQuery($query);
        
        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[2]);
        }else {
            return $state->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function getFieldsTable()
    {
        $fields = [];

        $query = 'SHOW COLUMNS FROM `'.$this->tableName.'`';

        $state = $this->executeQuery($query);
        
        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[2]);
        }else {

            while ($rs = $state->fetch(PDO::FETCH_ASSOC)) {
                $fields[] = $rs['Field'];      
            }
        }

        return $fields;
    }

    protected function createTable(){}

    protected function executeQuery($queryStr,array $params = []):PDOStatement
    {
        $query = $this->DB->prepare($queryStr);
        $query->execute($params);

        return $query;
    }

    public function add(array $data, $fields)
    {
        $fields = implode('`,`', $fields);
        $query = "INSERT INTO {$this->tableName} (`{$fields}`) VALUES ";
        $params = [];

        if (!empty($data)) {

            $queryParamArr = [];

            foreach ($data as $key => $value) {
                
                $queryParamArr[] = '("'.implode(',', $value).'")';
            }

            $query.= implode(', ',$queryParamArr);

        }else {
            Logger::errorLog('DB', 'Data not add. Empty array');

            return false;
        }

        $state = $this->executeQuery($query, $params);

        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[2]);
        }
    }

    public function get(array $select = [], array $filter = [], $order = 'ASC', $limit = '')
    {
        $query = 'SELECT ';

        $params = [];

        if(!empty($select)){
            
            $this->setSelect($query, $select);
        }else {
            $query.= '*';
        }

        $query.= ' FROM ' . $this->tableName;

        if (!empty($filter)) {
            
            $query.= ' WHERE ';

            $this->setQueryParam($query, $params, $filter, ' AND ');
        }

        if(!empty($limit))
        {
            $limit = intval($limit);

            $query.= ' LIMIT ' . $limit;
        }

        $state = $this->executeQuery($query, $params);

        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[2]);
        }else {
            return $state->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function delete($data)
    {
        $query = 'DELETE FROM '.$this->tableName.' WHERE ';

        $params = [];

        if (!empty($data)) {
            
            $this->setQueryParam($query, $params, $data, ' AND ');

        }else {
            Logger::errorLog('DB', 'Data not delete. Empty array');

            return false;
        }

        $state = $this->executeQuery($query, $params);

        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[2]);
        }
    }

    public function update($data, $filter, $limit = '')
    {
        $query = "UPDATE {$this->tableName} SET ";

        $params = [];

        if (!empty($data)) {
            
            $this->setQueryParam($query, $params, $data);
        }else {

            Logger::errorLog('DB', 'Data not update. Empty array');

            return false;
        }

        if (!empty($filter)) {

            $query.= ' WHERE ';
            
            $this->setQueryParam($query, $params, $filter, ' AND ');
        }else {
            
            Logger::errorLog('DB', 'Data not update. Empty filter array');

            return false;
        }

        if(!empty($limit))
        {
            $limit = intval($limit);

            $query.= ' LIMIT '.$limit;
        }

        $state = $this->executeQuery($query, $params);

        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[2]);
        }
    }

    protected function setQueryParam(&$query, &$params, $data,  $separator = ', ')
    {
        $lastKey = array_key_last($data);

        foreach ($data as $fieldName => $value) {
            
            $query.= strtoupper($fieldName) . ' = ?';
            
            $params[]  = $value;

            if ($lastKey != $fieldName) {
                $query.= $separator;
            }
        }
    }

    protected function setSelect(&$query, $select)
    {
        $lastKey = array_key_last($select);

        foreach ($select as $key => $fieldName) {
            
            $query .= strtoupper($fieldName);

            if ($lastKey != $key) {
                $query .= ', ';
            }
        }
    }
}



