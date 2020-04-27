<?php
/**
 * Created by PhpStorm.
 * User: LP
 * Date: 2020/3/31
 * Time: 22:12
 */

namespace Helpers;


class Tool
{

    /**
     * php获取中文字符拼音首字母
     * @param $str
     * @return null|string
     */
    function getFirstCharter($str){
        if(empty($str))
        {
            return '';
        }
        $fchar=ord($str{0});
        if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
        $s1=iconv('UTF-8','gb2312//TRANSLIT//IGNORE',$str);
        $s2=iconv('gb2312','UTF-8//TRANSLIT//IGNORE',$s1);
        $s=$s2==$str?$s1:$str;
        $asc=ord($s{0})*256+ord($s{1})-65536;
        if($asc>=-20319&&$asc<=-20284) return 'A';
        if($asc>=-20283&&$asc<=-19776) return 'B';
        if($asc>=-19775&&$asc<=-19219) return 'C';
        if($asc>=-19218&&$asc<=-18711) return 'D';
        if($asc>=-18710&&$asc<=-18527) return 'E';
        if($asc>=-18526&&$asc<=-18240) return 'F';
        if($asc>=-18239&&$asc<=-17923) return 'G';
        if($asc>=-17922&&$asc<=-17418) return 'H';
        if($asc>=-17417&&$asc<=-16475) return 'J';
        if($asc>=-16474&&$asc<=-16213) return 'K';
        if($asc>=-16212&&$asc<=-15641) return 'L';
        if($asc>=-15640&&$asc<=-15166) return 'M';
        if($asc>=-15165&&$asc<=-14923) return 'N';
        if($asc>=-14922&&$asc<=-14915) return 'O';
        if($asc>=-14914&&$asc<=-14631) return 'P';
        if($asc>=-14630&&$asc<=-14150) return 'Q';
        if($asc>=-14149&&$asc<=-14091) return 'R';
        if($asc>=-14090&&$asc<=-13319) return 'S';
        if($asc>=-13318&&$asc<=-12839) return 'T';
        if($asc>=-12838&&$asc<=-12557) return 'W';
        if($asc>=-12556&&$asc<=-11848) return 'X';
        if($asc>=-11847&&$asc<=-11056) return 'Y';
        if($asc>=-11055&&$asc<=-10247) return 'Z';
        return null;
    }


    /**
     * 二维数组根据多个字段进行排序
     *
     * sortArrByManyField($array1,'id',SORT_ASC,'name',SORT_ASC,'age',SORT_DESC);
     * @return mixed|null
     */
    function sortArrByManyField(){
        $args = func_get_args();
        if(empty($args)){
            return null;
        }
        $arr = array_shift($args);
        if(!is_array($arr)){
            throw new Exception("第一个参数不为数组");
        }
        foreach($args as $key => $field){
            if(is_string($field)){
                $temp = array();
                foreach($arr as $index=> $val){
                    $temp[$index] = $val[$field];
                }
                $args[$key] = $temp;
            }
        }
        $args[] = &$arr;//引用值
        call_user_func_array('array_multisort',$args);
        return array_pop($args);
    }


    /**
     * 方法说明：对二维数组按照某个字段进行排序
     * @param array $arr  二维数组
     * @param string $str   字段名称
     * @param string $sortType
     * @return array   返回排序后的数组
     * @create by lp
     */
    function arraySortByField($arr = array(),$str="",$sortType='SORT_ASC')
    {
        $sort = array(
            'direction' => $sortType, //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => $str,       //排序字段
        );
        $arrSort = array();
        if(is_array($arr))foreach($arr AS $uniqid => $row){
            if(is_array($row))foreach($row AS $key=>$value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction'] && is_array($arrSort[$sort['field']])){
            @array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arr);
        }
        return $arr;
    }


    /**
     * 二维对象转数组
     *
     * @param $array
     * @return array
     */
    function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        } if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = object_array($value);
            }
        }
        return $array;
    }

}