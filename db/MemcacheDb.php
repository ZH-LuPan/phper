<?php

namespace db;

class Cache{


    static $cache = false;
    static $error = false;
    private static $prefix = null;


    private function __construct()
    {
        if(!self::$cache && !self::$error){
            try {
                self::$cache = new \Memcache();
            }
            catch(\Exception $e){
                print_r($e->getMessage());
            }
        }
        return self::$cache;
    }

    public static function exists($keyName = null, $lifetime = null)
    {
        return !self::$cache ? false : self::$cache->exists($keyName, $lifetime);
    }

    public static function get($keyName = null, $lifetime = null)
    {
        return !self::$cache ? false : self::$cache->get($keyName, $lifetime);
    }

    public static function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true)
    {
        return !self::$cache ? false : self::$cache->save($keyName, $content, $lifetime, $stopBuffer);
    }

    public static function delete($keyName)
    {
        return !self::$cache ?: !self::$cache ? false : self::$cache->delete($keyName);
    }

    public static function flush()
    {
        return !self::$cache ? false : self::$cache->flush();
    }

    public static function queryKeys($prefix = null)
    {
        return !self::$cache ? false : self::$cache->queryKeys(self::$prefix ? self::$prefix . $prefix : $prefix);
    }

}