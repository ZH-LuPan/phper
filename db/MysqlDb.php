<?php

/**
 * 使用单例模式+pdo封装mysql连接类
 * Class Mysql
 * @date 20200322
 */

namespace db;

class MysqlDb{

    private $host = '127.0.0.1'; //sql.l211.vhostgo.com
    private $dbName = 'test';    //123456lupan
    private $user = 'root';      //123456lupan
    private $pwd = 'root';       //1234lupan
    private $dbType = 'mysql';
    private $table = '';
    private $pdo;  //私有静态属性
    private $fields = '';
    private $where = '';
    private $order = '';
    private $limit = '';
    private $count = 'COUNT(*)';

    private static $_instance = null;

    private function __construct($table = '')
    {
        try{
            $user = $this->user;
            $pwd = $this->pwd;
            $dsn = $this->dbType.':host='.$this->host.';dbname='.$this->dbName;
            $pdo = new PDO($dsn,$user,$pwd);
            $pdo  ->query('SET NAMES utf8');
            $this ->pdo = $pdo;
            $this ->table = '`' . $table . '`';
        }catch (PDOException $e){
            echo $e->getMessage();
        }
    }



    public static function getInstance($table = '')
    {
        if(self::$_instance === null){
            self::$_instance = new self($table);
        }
        return self::$_instance;
    }

    /**
     * 防止克隆
     */
    private function __clone()
    {

    }

    /**
     * 执行原生sql语句
     * @param $sql
     * @param string $queryMode
     * @param bool $deBug
     * @return array|mixed|null
     * @throws Exception
     */
    public function query($sql,$queryMode = 'ALL' , $deBug = false)
    {
        $result = null;
        if($deBug === true) $this->debug($sql);
        $record = $this->pdo->query($sql);
        $this->throwPDOError();
        if ($record) {
            $record->setFetchMode(PDO::FETCH_ASSOC);
            if ($queryMode == 'All') {
                $result = $record->fetchAll();
            } elseif ($queryMode == 'Row') {
                $result = $record->fetch();
            }
        }
        return $result;
    }

    /**
     * 插入数据
     * @param $data
     * @return string
     * @throws
     */
    public function insert($data)
    {
        $keys = array_keys($data);
        $this->checkFields($this->table,$keys);
        foreach ($keys as $key => $value) {
            $keys[$key] = '`' . $value . '`';
        }
        foreach ($data as $key => $value) {
            $data[$key] = "'" . $value . "'";
        }
        $values = implode(',', $data);
        $sql = 'INSERT INTO' . $this->table . '(' . $this->fields . ')' . 'VALUES' . '(' . $values .')';
        $this->pdo->prepare($sql)->execute();
        $this->throwPDOError();
        return $this->pdo->lastInsertId();
    }

    /**
     * @param $data
     * @param $debug
     * @return int
     * @throws Exception
     */
    public function update($data,$debug = false)
    {
        $updateKey = array();
        $update = '';
        unset($data['id']);
        foreach ($data as $key => $value) {
            $updateKey[] = $key;
            $update .= "`$key`='$value',";
        }
        $this->checkFields($this->table,$updateKey);
        $update = substr($update, 0, -1);
        $sql = 'UPDATE' . $this->table . 'SET' . $update . $this->where;
        if($debug === true) $this->debug($sql);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * 删除数据
     * @return int
     * @throws
     */
    public function delete()
    {
        if($this->where === '') $this->outputError("'WHERE' is Null");
        $sql = 'DELETE FROM' . $this->table . $this->where;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $this->throwPDOError();
        return $stmt->rowCount();
    }

    /**
     * 查询单条数据
     * @return array
     */
    public function find()
    {
        $sql = 'SELECT' . $this->fields . 'FROM' . $this->table . $this->where . 'LIMIT  1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $this->object_array($stmt->fetchObject());

    }

    /**
     * 查詢数据
     * @return array
     */
    public function select()
    {
        $sql = 'SELECT' . $this->fields . 'FROM' . $this->table . $this->where . $this->order . $this->limit;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $objects = array();
        while ($rows = $stmt->fetchObject()) {
            $objects[] = $this->object_array($rows);
        }
        $this->count = $stmt->rowCount();
        return $objects;
    }


    /**
     * @param $field_name
     * @param bool $debug
     * @return int|mixed
     * @throws Exception
     */
    public function getMaxValue($field_name, $debug = false)
    {
        $strSql = "SELECT MAX(".$field_name.") AS MAX_VALUE FROM $this->table";
        if ($this->where != '') $strSql .= " WHERE $this->where";
        if ($debug === true) $this->debug($strSql);
        $arrTemp = $this->query($strSql, 'Row');
        $maxValue = $arrTemp["MAX_VALUE"];
        if ($maxValue == "" || $maxValue == null) {
            $maxValue = 0;
        }
        return $maxValue;
    }

    /**
     * beginTransaction 事务开始
     */
    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    /**
     * commit 事务提交
     */
    public function commit()
    {
        $this->pdo->commit();
    }

    /**
     * rollback 事务回滚
     */
    public function rollback()
    {
        $this->pdo->rollback();
    }

    /**
     * @param $arraySql
     * @return bool
     * @throws Exception
     */
    public function execTransaction($arraySql)
    {
        $retval = 1;
        $this->beginTransaction();
        foreach ($arraySql as $strSql) {
            if ($this->query($strSql) == 0) $retval = 0;
        }
        if ($retval == 0) {
            $this->rollback();
            return false;
        } else {
            $this->commit();
            return true;
        }
    }

    /**
     * destruct 关闭数据库连接
     */
    public function __destruct()
    {
        $this->pdo = null;
    }


    /**
     * @param $param
     * @return $this
     */
    public function where($param)
    {
        $key = key($param);
        $value = current($param);
        $sign = empty($param[0]) ? '=' : $param[0];
        $this->where = "WHERE (`$key`$sign'$value')";
        return $this;
    }

    /**
     * @param $param
     * @return $this
     */
    public function wheres($param)
    {
        $this->where = "WHERE ($param)";
        return $this;
    }

    /**
     * @param $fields
     * @return $this
     */
    public function fields($fields)
    {
        $fields = explode(',', $fields);
        foreach ($fields as $key => $value) {
            $fields[$key] = "`$value`";
        }
        $this->fields = implode(',', $fields);
        return $this;
    }

    /**
     * @param $start
     * @param $end
     * @return $this
     */
    public function limit($start, $end)
    {
        $this->limit = "LIMIT $start, $end";
        return $this;
    }

    /**
     * @param $param
     * @return $this
     */
    public function order($param)
    {
        $this->order = 'ORDER BY ' .$param;
        return $this;
    }

    /**
     * @return string
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * @return mixed
     */
    public function counts()
    {
        $sql = 'SELECT' . $this->count . 'FROM' . $this->table . $this->where;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * @param $table
     * @param $arrayFields
     * @throws Exception
     */
    public function checkFields($table, $arrayFields)
    {
        $fields = $this->getFields($table);
        foreach ($arrayFields as $key => $value) {
            if (!in_array($key, $fields)) {
                $this->outputError("Unknown column `$key` in field list.");
            }
        }
    }

    /**
     * @param $table
     * @return array
     * @throws Exception
     */
    private function getFields($table)
    {
        $fields = array();
        $record = $this->pdo->query("SHOW COLUMNS FROM $table");
        $this->throwPDOError();
        $record->setFetchMode(PDO::FETCH_ASSOC);
        $result = $record->fetchAll();
        foreach ($result as $rows) {
            $fields[] = $rows['Field'];
        }
        return $fields;
    }




    /**
     * @param $array
     * @return array
     */
    private function object_array($array)
    {
        if(is_object($array)) {
            $array = (array)$array;
        } if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }

    /**
     * @param $sql
     */
    private function debug($sql)
    {
        var_dump($sql);die;
    }

    /**
     * @throws Exception
     */
    private function throwPDOError()
    {
        if($this->pdo->errorCode() != '0000'){
            $errorArray = $this->pdo->errorInfo();
            $this->outputError($errorArray[2]);
        }
    }

    /**
     * @param string $errorInfo
     * @throws Exception
     */
    private function outputError($errorInfo = '')
    {
        throw new Exception('MySQL Error: '.$errorInfo);
    }


}