<?php
/**
 * Created by PhpStorm.
 * User: LP
 * Date: 2020/3/31
 * Time: 21:54
 */
namespace Helpers;


class PHPTree
{

    /**
     *  @方法：将数据格式转换成树形结构数组
     * @param array $items 要进行转换的数组
     * @return array       转换完成的数组
     */
    function getCatTree(Array $items) {
        $tree = array();
        foreach ($items as $item)
            if (isset($items[$item['parent_id']])) {
                $items[$item['parent_id']]['son'][] = &$items[$item['id']];
            } else {
                $tree[] = &$items[$item['id']];
            }
        return $tree;
    }

    /**
     * * 将树形结构数组输出
     * @param $items    要输出的数组
     * @param int $deep 顶级父节点id
     * @param int $type_id 已选中项
     * @return string
     */
    function exportTree($items, $deep = 0, $type_id = 0){
        $select = '';
        foreach ($items as $item) {
            $select .= '<option value="' . $item['id'] . '" ';
            $select .= ($type_id == $item['id']) ? 'selected="selected">' : '>';
            if ($deep > 0) $select .= str_repeat('&nbsp;', $deep*4);
            $select .= '&nbsp;&nbsp;'.htmlspecialchars(addslashes($item['name'])).'</option>';
            if (!empty($item['son'])){
                $select .= $this->exportTree($item['son'], $deep+1,$type_id);
            }
        }
        return $select;
    }
}
