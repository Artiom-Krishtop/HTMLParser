<?

namespace ORM;

use PDO;
use PDOStatement;
use Configs\DataBaseConfig;
use Utilits\Logger;

require_once 'utilits/autoloader.php';

abstract class SQL{

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


    /* DEBUG */
    public function tableList():bool
    {

        $query = 'SHOW TABLES FROM `'.DataBaseConfig::getDBName().'`';

        $state = $this->executeQuery($query);
        
        print_r($state->fetchAll(PDO::FETCH_ASSOC));

        return false;
    }

    /* DEBUG */
    public function DBlist()
    {
        $query = 'SHOW DATABASES';

        $state = $this->executeQuery($query);

        dd($state->fetchAll(PDO::FETCH_ASSOC));
    }

    /* DEBUG */
    public function deleteDB()
    {
        $query = 'DROP DATABASE `:db_name`';
        $params = [
            ':db_name' => DataBaseConfig::getDBName()
        ];

        $res = $this->executeQuery($query, $params);
        print_r($res->errorInfo());
    }

    /* DEBUG */
    public function deleteTable($tableName)
    {
        $query = 'DROP TABLE `'.$tableName.'`';

        $res = $this->executeQuery($query);
        print_r($res->errorInfo());
    }

    /* DEBUG */
    public function structTable()
    {
        $query = 'SHOW CREATE TABLE `'.$this->tableName.'`';

        $state = $this->executeQuery($query);
        
        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[3]);
            dd($res, false);
        }else {
            dd($state->fetchAll(PDO::FETCH_ASSOC), false);
        }
    }

    public function createDB()
    {
        $query = 'CREATE DATABASE IF NOT EXISTS `'. $this->DBname .'`';

        $state = $this->executeQuery($query);

        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[3]);
        }else {
            Logger::log('Create DB:',$this->DBname);
        }
    }

    public function createTable(){}

    protected function executeQuery($queryStr,array $params = []):PDOStatement
    {
        $query = $this->DB->prepare($queryStr);
        $query->execute($params);

        return $query;
    }

    public function add(array $data)
    {
        $query = 'INSERT INTO '.$this->tableName;
        $params = [];

        if (!empty($data)) {
            
            $lastKey = array_key_last($data);

            foreach ($data as $fieldName => $value) {
                
                $query.= strtoupper($fieldName) . ' = ?';

                if ($lastKey != $fieldName) {
                    $query.= ', ';
                }

                $params[]  = $value;
            }
        }else {
            Logger::errorLog('DB', 'Data not add. Empty array');

            return false;
        }

        $state = $this->executeQuery($query, $params);

        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[3]);
            dd($res, false);
        }
    }

    public function get($tableName ,array $select, array $filter = [], $order = 'ASC')
    {
        if (empty($tableName)) {
            Logger::errorLog('DB', 'Empty table');
        }

        $query = 'SELECT ';

        $params = [];

        if(!empty($select)){

            $lastKey = array_key_last($select);

            foreach ($select as $key => $fieldName) {
                
                $query .= strtoupper($fieldName);

                if ($lastKey != $key) {
                    $query .= ',';
                }
            }
        }else {
            $query.= '*';
        }

        $query.= ' FROM ' . $tableName;

        if (!empty($filter)) {
            
            $query.= ' WHERE ';

            $lastKey = array_key_last($filter);

            foreach ($filter as $fieldName => $value) {
                
                $query.= strtoupper($fieldName) . ' = ?';

                if ($lastKey != $fieldName) {
                    $query.= ' AND ';
                }

                $params[]  = $value;
            }
        }

        $state = $this->executeQuery($query, $params);

        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[3]);
            dd($res, false);
        }else {
            dd($state->fetchAll(PDO::FETCH_ASSOC));
            return $state->fetchAll(PDO::FETCH_ASSOC);
        }
        
        dd($query, false);
    }

    public function delete($data)
    {
        $query = 'DELETE FROM '.$this->tableName;

        $params = [];

        if (!empty($data)) {
            
            $lastKey = array_key_last($data);

            foreach ($data as $fieldName => $value) {
                
                $query.= strtoupper($fieldName) . ' = ?';

                if ($lastKey != $fieldName) {
                    $query.= ' AND ';
                }

                $params[]  = $value;
            }
        }else {
            Logger::errorLog('DB', 'Data not delete. Empty array');

            return false;
        }

        $state = $this->executeQuery($query, $params);

        $res = $state->errorInfo();

        if ($res[0] != 00000) {
            Logger::errorLog('DB', $res[3]);
            dd($res, false);
        }
    }

    public function update($data, $filter)
    {
        $query = 'UPDATE '.$this->tableName;

        $param = [];

        if (!empty($data)) {
            
            $lastKey = array_key_last($data);

            foreach ($data as $fieldName => $value) {
                
                $query.= strtoupper($fieldName) . ' = ?';

                if ($lastKey != $fieldName) {
                    $query.= ', ';
                }

                $params[]  = $value;
            }
        }else {
            Logger::errorLog('DB', 'Data not update. Empty array');

            return false;
        }

        if (!empty($filter)) {

            $query.= ' WHERE ';
            
            $lastKey = array_key_last($data);

            foreach ($data as $fieldName => $value) {
                
                $query.= strtoupper($fieldName) . ' = ?';

                if ($lastKey != $fieldName) {
                    $query.= ' AND ';
                }

                $params[]  = $value;
            }
        }
    }

    protected function queryBuilder(&$query, &$param, $separator = ', ', $select = false)
    {

    }
}