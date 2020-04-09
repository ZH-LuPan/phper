<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/9
 * Time: 13:32
 */

namespace db;


class RedisDb
{
    private $redis;
    protected $dbId;
    protected $auth;
    private static $_instance=array();
    private $k;
    protected $attr = array(
        'timeout' => 30,
        'db_id'   => 0
    );
    protected $expireTime;
    protected $host;
    protected $port;


    private function __construct($config,$attr=array())
    {
        $this->attr = array_merge($this->attr,$attr);
        $this->redis = new \Redis();
        $this->port = $config['port'] ? $config['port'] : 6379;
        $this->host = $config['host'];
        $this->redis->connect($this->host,$this->port,$this->attr['timeout']);
        if($config['auth'])
        {
            $this->auth($config['auth']);
            $this->auth = $config['auth'];
        }
        $this->expireTime = time() + $this->attr['timeout'];
    }

    public static function getInstance($config, $attr = [])
    {
        if(!is_array($attr)){
            $dbId = $attr;
            $attr = [];
            $attr['db_id'] = $dbId;
        }
        $attr['db_id'] = $attr['db_id'] ?: 0;
        $k = md5(implode('',$config) . $attr['db_id']);
        if(! self::$_instance[$k] instanceof self){
            self::$_instance[$k] = new self($config, $attr);
            self::$_instance[$k]->k = $k;
            self::$_instance[$k]->db_id = $attr['db_id'];

            if($attr['db_id'] != 0){
                self::$_instance[$k]->select($attr['db_id']);
            }
        }else if( time() > self::$_instance[$k]->expireTime){
            static::$_instance[$k]->close();
            static::$_instance[$k] = new self($config, $attr);
            static::$_instance[$k]->k = $k;
            static::$_instance[$k]->dbId = $attr['db_id'];

            //如果不是0号库，选择一下数据库。
            if($attr['db_id'] != 0) {
                static::$_instance[$k]->select($attr['db_id']);
            }
        }
        return self::$_instance[$k];
    }


    private function __clone(){}


    public function getRedis()
    {
        return $this->redis;
    }

    /*******************hash函数**********************/

    /**
     * hGet 得到hash表中的一个字段的值
     * @param $key
     * @param $field
     * @return string
     */
    public function hGet($key,$field)
    {
        return $this->redis->hGet($key,$field);
    }

    public function hSet($key,$field,$value)
    {
        return $this->redis->hset($key,$field,$value);
    }

    public function hExists($key,$field)
    {
        return $this->redis->hExists($key,$field);
    }

    public function hDel($key,$field)
    {
        $fieldArr = explode(',',$field);
        $delNum = 0;
        foreach ($fieldArr as $row){
            $row = trim($row);
            $delNum += $this->redis->hDel($key,$row);
        }
        return $delNum;
    }

    public function hLen($key)
    {
        return $this->redis->hLen($key);
    }

    public function hSetNx($key,$field,$value)
    {
        return $this->redis->hSetNx($key,$field,$value);
    }

    public function hMset($key,$value)
    {
        if(!is_array($value)) return false;
        return $this->redis->hMSet($key,$value);
    }

    public function hMget($key,$value)
    {
        if(!is_array($value)){
            $value = explode(',', $value);
        }
        return $this->redis->hMget($key, $value);
    }

    public function hIncrBy($key, $field, $value)
    {
        $value = intval($value);
        return $this->redis->hIncrBy($key, $field, $value);
    }

    public function hKeys($key)
    {
        return $this->redis->hKeys($key);
    }

    public function hVals($key)
    {
        return $this->redis->hVals($key);
    }

    public function hGetAll($key)
    {
        return $this->redis->hGetAll($key);
    }

    /********************有序集合操作**********************************/

    public function zAdd($key, $order, $value)
    {
        return $this->redis->zAdd($key, $order, $value);
    }

    public function zIncrBy($key, $num, $value)
    {
        return $this->redis->zIncrBy($key, $num, $value);
    }

    public function zRem($key,$value)
    {
        return $this->redis->zRem($key,$value);
    }

    public function zRange($key, $start, $end)
    {
        return $this->redis->zRange($key, $start, $end);
    }

    public function zRevRange($key, $start, $end)
    {
        return $this->redis->zRevRange($key, $start, $end);
    }

    public function zRangeByScore($key, $start = 0, $end = 0, $option = [])
    {
        return $this->redis->zRangeByScore($key, $start, $end, $option);
    }

    public function zRevRangeByScore($key, $start = 0, $end = 0, $option = [])
    {
        return $this->redis->zRevRangeByScore($key,$start,$end,$option);
    }

    public function zCount($key, $start, $end)
    {
        return $this->redis->zCount($key, $start, $end);
    }

    public function zScore($key, $value)
    {
        return $this->redis->zScore($key, $value);
    }

    public function zRank($key,$value)
    {
        return $this->redis->zRank($key,$value);
    }

    public function zRevRank($key,$value)
    {
        return $this->redis->zRevRank($key,$value);
    }

    public function zRemRangeByScore($key,$start,$end)
    {
        return $this->redis->zRemRangeByScore($key,$start,$end);
    }

    public function zCard($key)
    {
        return $this->redis->zCard($key);
    }

    /*********************队列操作命令************************/

    public function rPush($key,$value)
    {
        return $this->redis->rPush($key,$value);
    }

    public function rPushx($key,$value)
    {
        return $this->redis->rPushx($key,$value);
    }

    public function lPush($key,$value)
    {
        return $this->redis->lPush($key,$value);
    }

    public function lPushx($key,$value)
    {
        return $this->redis->lPushx($key,$value);
    }

    public function lLen($key)
    {
        return $this->redis->lLen($key);
    }

    public function lRange($key,$start,$end)
    {
        return $this->redis->lrange($key,$start,$end);
    }

    public function lIndex($key,$index)
    {
        return $this->redis->lIndex($key,$index);
    }

    public function lSet($key,$index,$value)
    {
        return $this->redis->lSet($key,$index,$value);
    }

    public function lRem($key,$count,$value)
    {
        return $this->redis->lRem($key,$value,$count);
    }

    public function lPop($key)
    {
        return $this->redis->lPop($key);
    }

    public function rPop($key)
    {
        return $this->redis->rPop($key);
    }

    /*************redis字符串操作命令*****************/

    public function set($key,$value)
    {
        return $this->redis->set($key,$value);
    }

    public function get($key,$value)
    {
        return $this->redis->get($key,$value);
    }

    public function setex($key,$expire,$value)
    {
        return $this->redis->setex($key,$expire,$value);
    }

    public function setnx($key,$value)
    {
        return $this->redis->setnx($key,$value);
    }

    public function mset($arr)
    {
        return $this->redis->mset($arr);
    }

    /*************redis　无序集合操作命令*****************/

    public function sMembers($key)
    {
        return $this->redis->sMembers($key);
    }

    public function sDiff($key1,$key2)
    {
        return $this->redis->sDiff($key1,$key2);
    }

    public function sAdd($key,$value)
    {
        if(!is_array($value))
            $arr = array($value);
        else
            $arr = $value;

        foreach($arr as $row)
            $this->redis->sAdd($key,$row);
    }

    public function scard($key)
    {
        return $this->redis->scard($key);
    }

    public function srem($key,$value)
    {
        return $this->redis->srem($key,$value);
    }

    /*************redis管理操作命令*****************/

    public function select($dbId)
    {
        $this->dbId = $dbId;
        return $this->redis->select($dbId);
    }

    public function flushDB()
    {
        return $this->redis->flushDB();
    }

    public function info()
    {
        return $this->redis->info();
    }

    public function save()
    {
        return $this->redis->save();
    }

    public function bgSave()
    {
        return $this->redis->bgSave();
    }

    public function lastSave()
    {
        return $this->redis->lastSave();
    }

    public function keys($key)
    {
        return $this->redis->keys($key);
    }

    public function del($key)
    {
        return $this->redis->del($key);
    }

    public function exists($key)
    {
        return $this->redis->exists($key);
    }

    public function expire($key,$expire)
    {
        return $this->redis->expire($key,$expire);
    }

    public function ttl($key)
    {
        return $this->redis->ttl($key);
    }

    public function expireAt($key,$time)
    {
        return $this->redis->expireAt($key,$time);
    }

    public static function closeAll()
    {
        foreach(static::$_instance as $o)
        {
            if($o instanceof self)
                $o->close();
        }
    }

    public function dbSize()
    {
        return $this->redis->dbSize();
    }

    public function randomKey()
    {
        return $this->redis->randomKey();
    }

    public function getDbId()
    {
        return $this->dbId;
    }

    public function getAuth()
    {
        return $this->auth;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getConnInfo()
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'auth' => $this->auth
        ];
    }

    /*********************事务的相关方法************************/

    public function watch($key)
    {
        return $this->redis->watch($key);
    }

    public function unwatch()
    {
        return $this->redis->unwatch();
    }

    public function multi($type = \Redis::MULTI)
    {
        return $this->redis->multi($type);
    }

    public function exec()
    {
        return $this->redis->exec();
    }

    public function discard()
    {
        return $this->redis->discard();
    }

    /*********************自定义的方法,用于简化操作************************/

    /**
     * hashAll 得到一组的ID号
     * @param $prefix
     * @param $ids
     * @return array|bool
     */
    public function hashAll($prefix,$ids)
    {
        if($ids == false)
            return false;

        if( is_string($ids) )
            $ids = explode(',', $ids);

        $arr = [];
        foreach($ids as $id) {
            $key = $prefix.'.'.$id;
            $res = $this->hGetAll($key);
            if($res != false)
                $arr[] = $res;
        }

        return $arr;
    }

    /**
     * pushMessage 生成一条消息，放在redis数据库中。使用0号库。
     * @param $lkey
     * @param string|array $msg
     * @return string
     */
    public function pushMessage($lkey, $msg)
    {
        if(is_array($msg))
            $msg = json_encode($msg);

        $key = md5($msg);

        //如果消息已经存在，删除旧消息，已当前消息为准
        //echo $n=$this->lRem($lkey, 0, $key)."\n";
        //重新设置新消息
        $this->lPush($lkey, $key);
        $this->setex($key, 3600, $msg);
        return $key;
    }

    /**
     * delKeys 得到条批量删除key的命令
     * @param $keys
     * @param $dbId
     * @return string
     */
    public function delKeys($keys,$dbId)
    {
        $redisInfo = $this->getConnInfo();
        $cmdArr = [
            'redis-cli',
            '-a',
            $redisInfo['auth'],
            '-h',
            $redisInfo['host'],
            '-p',
            $redisInfo['port'],
            '-n',
            $dbId,
        ];
        $redisStr = implode(' ', $cmdArr);
        $cmd = "{$redisStr} KEYS \"{$keys}\" | xargs {$redisStr} del";
        return $cmd;
    }

}