<?php

use db\RedisDb;
/**
 * Created by PhpStorm.
 * User: LP
 * Date: 2020/3/22
 * Time: 21:45
 */
require_once '../config.php';

$action = $_GET['action'];

if($action == 'test'){
    phpinfo();
}

if($action == 'export'){

    $data = array(
              array(
                   'dataList' => array(array()),  //数据
                   'titleArr' => array(),         //大标题
                   'groupName' => 'sheet1',        //sheetName
                   'extendData'=> array(          //扩展数据
                            'header' => array(array()),
                            'dataList' => array(array()),
                            'titleArr' => array(),         //大标题
                       ),
                   'headerArr' => array(
                       array(
                            'field' => '',
                            'name'  => '姓名',
                            'width' => '20',
                            'mergeCol' => 2
                       ),
                       array(
                           'field' => '',
                           'name'  => '学号',
                           'width' => '20',
                           'mergeCol' => 2
                       ),
                       array(
                           'field' => '',
                           'name'  => '总分',
                           'width' => '20',
                           'mergeCol' => 2
                       ),
                       array(
                           'field' => '',
                           'name'  => '一.英汉互译',
                           'width' => '20',
                           'mergeRow' => 4,
                           'sonRow' => array(
                               array(
                                   'field' => '',
                                   'name'  => '第2题,3分',
                                   'width' => '12'
                               ),
                               array(
                                   'field' => '',
                                   'name'  => '第1题,2分',
                                   'width' => '12'
                               ),
                               array(
                                   'field' => '',
                                   'name'  => '第2题,3分',
                                   'width' => '12'
                               ),
                               array(
                                   'field' => '',
                                   'name'  => '第1题,2分',
                                   'width' => '12'
                               )
                           )
                       ),
                       array(
                           'field' => '',
                           'name'  => '二.阅读理解',
                           'width' => '20',
                           'mergeRow' => 3,
                           'sonRow' => array(
                               array(
                                   'field' => '',
                                   'name'  => '第1题,2分',
                                   'width' => '12'
                               ),
                               array(
                                   'field' => '',
                                   'name'  => '第2题,3分',
                                   'width' => '12'
                               ),
                               array(
                                   'field' => '',
                                   'name'  => '第3题,2分',
                                   'width' => '12'
                               )
                           )
                       ),
                       array(
                           'field' => '',
                           'name'  => '三.作文题',
                           'width' => '20',
                           'mergeRow' => 2,
                           'sonRow' => array(
                               array(
                                   'field' => '',
                                   'name'  => '第7题,2分',
                                   'width' => '12'
                               ),
                               array(
                                   'field' => '',
                                   'name'  => '第8题,3分',
                                   'width' => '12'
                               )
                           )
                       ),
                   )
            )
    );


    include_once '../helper/PHPExcel.php';
    $phpExcel = new \Helpers\PHPExcel();
    $phpExcel->exportExcel('123',$data);

}

if($action == 'redis'){

    require_once '../db/RedisDb.php';
    $config = array('host'=>'127.0.0.1');
    $redis = RedisDb::getInstance($config);
    print_r($redis);
}