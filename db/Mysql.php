<?php

/**
 * 使用单例模式+pdo封装mysql连接类
 * Class Mysql
 * @date 20200322
 */
class Mysql{

    private $host = '127.0.0.1';
    private $dbName = 'test';
    private $user = 'root';
    private $pwd = 'root';
    private $dbType = 'mysql';
    private static $pdo;  //私有静态属性

    private function __construct()
    {
        try{
            $user = $this->user;
            $pwd = $this->pwd;
            $dsn = $this->dbType.':host='.$this->host.';dbname='.$this->dbName;
            $pdo = new PDO($dsn,$user,$pwd);
            $pdo->query('SET NAMES utf8');
            self::$pdo = $pdo;
        }catch (Exception $exception){
            echo $exception->getMessage();
        }
    }



    public static function getInstance()
    {
        if(self::$pdo === ''){
            self::$pdo = new self();
        }
        return self::$pdo;
    }

    /**
     * 防止克隆
     */
    private function __clone()
    {

    }

    public function query($sql)
    {
        return mysqli_query($sql);
    }


}