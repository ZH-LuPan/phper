<?php
/**
 * Created by PhpStorm.
 * User: LP
 * Date: 2020/3/22
 * Time: 21:45
 */

$action = $_GET['action'];

if($action == 'test'){
//    print_r(PDO::getAvailableDrivers());
    phpinfo();
}