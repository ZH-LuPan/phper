<?php

use db\RedisDb;

require_once '../db/RedisDb.php';
/**
 * Created by PhpStorm.
 * User: LP
 * Date: 2020/3/22
 * Time: 21:45
 */
require_once '../config.php';


$action = $_GET['action'];


if($action && $action == 'redis'){
    $redis = RedisDb::getInstance();
    $redis->set('test',231);
    $redis->expire('test',10);
    print_r($redis->get('test'));
}

if($action == 'miaosha'){  //秒杀demo

    //1.商品入列
    $redis = RedisDb::getInstance();
    $goodNum = 100;
    for($i = 0 ; $i < 100 ; $i++){

    }


    //用户入列



}