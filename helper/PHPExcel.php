<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/21
 * Time: 10:22
 */

namespace Helpers;


class PHPExcel
{

    const EXCEL_FORMAT = '.xlsx';

    public function __construct()
    {
        include_once(BASE_PATH.'/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        include_once (BASE_PATH.'/vendor/phpoffice/phpexcel/Classes/PHPExcel/Writer/Excel2007.php');
    }

    /**
     * 导出
     *
     * @param $fileName     --文件名称
     * @param $sheetArr  --sheet标题
     * @param bool $isDown
     * @author lp
     * @throws
     */
    public function exportExcel($fileName,$sheetArr,$isDown = true)
    {
        $excel = new \PHPExcel();
        $objWriter = new \PHPExcel_Writer_Excel2007($excel);
        $arrange = range('A','Z');
        if(is_array($sheetArr)){
            foreach($sheetArr as $key => $value){
                $arr_num     = count($value['dataList']);   //行数
                $field_count = count($value['headerArr']);  //列数
                $excel->createSheet($key);
                $Sheet = $excel->setActiveSheetIndex($key);
                //大标题
                if (is_array($value['titleArr'])) {
                    foreach ($value['titleArr'] as $t => $title) {
                        $Sheet->mergeCells('A' . ($t + 1) . ':' . $arrange[$field_count - 1] . ($t + 1));
                        $Sheet->getStyle('A' . ($t + 1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $Sheet->getStyle('A' . ($t + 1))->getFont()->setBold(true);  //加粗
                        $Sheet->setCellValue('A' . ($t + 1), $title);
                    }
                }
                //设置表头
                $mergeRow = '';
                $start = count($value['titleArr']) + 1;
                if (is_array($value['headerArr'])) foreach ($value['headerArr'] as $k => $v) {
                    if($v['mergeCol']) $Sheet->mergeCells($arrange[$k] . $start.":".$arrange[$k] . ($start+$v['mergeCol']-1));
                    if($v['mergeRow']) $Sheet->mergeCells($arrange[$k] . $start.":".$arrange[$k+$v['mergeRow']-1] . $start);
                    $Sheet->getStyle($arrange[$k] . $start)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $Sheet->getStyle($arrange[$k] . $start)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $Sheet->getStyle($arrange[$k] . $start)->getFont()->setBold(true);  //加粗
                    $v['width'] && $Sheet->getColumnDimension($arrange[$k])->setWidth($v['width']);
                    $Sheet->setCellValue($arrange[$k] . $start, $v['name']);
                    if($v['mergeRow'])  $mergeRow += $v['mergeRow'];
                }
                $start += 1;
                for ($i = 0; $i < $arr_num; $i++) {
                    for ($j = 0; $j <= $field_count; $j++) {
                        $Sheet->getStyle($arrange[$j] . $start)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        @$Sheet->setCellValue($arrange[$j] . ($start), $value['dataList'][$i][$value['headerArr'][$j]['field']]);
                    }
                    $start++;
                }
                //扩展数据
                if($extendData = $value['extendData']){
                    $start = $arr_num + count($value['titleArr']) + 2;      //扩展数据开始行
                    $extendHeaderLen = count($extendData['header']) - 1;    //扩展数据表头长度
                    if($extendData['title']){
                        $Sheet->mergeCells('A' . ($start) . ':' . $arrange[$extendHeaderLen] . ($start));
                        $Sheet->mergeCells('A' . ($start + 1) . ':' . $arrange[$extendHeaderLen] . ($start + 1));
                        $Sheet->getStyle('A' . ($start + 1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $Sheet->getStyle('A' . ($start + 1))->getFont()->setBold(true);  //加粗
                        $Sheet->setCellValue('A' . ($start + 1), $extendData['title']);
                    }

                    if($extendData['header']){
                        $start = $arr_num + 6;
                        if (is_array($extendData['header'])) foreach ($extendData['header'] as $k => $v) {
                            $Sheet->getStyle($arrange[$k] . $start)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $Sheet->getStyle($arrange[$k] . $start)->getFont()->setBold(true);  //加粗
                            $v['width'] && $Sheet->getColumnDimension($arrange[$k])->setWidth($v['width']);
                            $Sheet->setCellValue($arrange[$k] . $start, $v['name']);
                        }
                    }
                    if($extendData['dataList']){
                        $start = $arr_num + 7;
                        for ($i = 0; $i < $arr_num; $i++) {
                            for ($j = 0; $j <= $field_count; $j++) {
                                $Sheet->getStyle($arrange[$j] . $start)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $Sheet->setCellValue($arrange[$j] . ($start), $extendData['dataList'][$i][$extendData['header'][$j]['field']]);
                            }
                            $start++;
                        }
                    }
                }
                $excel->getActiveSheet()->setTitle($value['groupName']);
            }
        }
        if($isDown){
            ob_end_clean();
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type:application/vnd.ms-execl");
            header("Content-Type:application/octet-stream");
            header("Content-Type:application/download");
            header('Content-Disposition:attachment;filename='.$fileName.self::EXCEL_FORMAT);
            header("Content-Transfer-Encoding:binary");
            //导出数据
            $objWriter->save('php://output');
            exit;
        }
    }

    public function export_user()
    {
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">会员ID</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">会员昵称</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">会员等级</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">手机号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">邮箱</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">注册时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">最后登陆</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">余额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">积分</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">累计消费</td>';
        $strTable .= '</tr>';
        $user_ids = I('user_ids');
        if ($user_ids) {
            $condition['user_id'] = ['in', $user_ids];
        } else {
            $mobile = I('mobile');
            $email = I('email');
            $mobile ? $condition['mobile'] = $mobile : false;
            $email ? $condition['email'] = $email : false;
        };
        $count = DB::name('users')->where($condition)->count();
        $p = ceil($count / 5000);
        for ($i = 0; $i < $p; $i++) {
            $start = $i * 5000;
            $end = ($i + 1) * 5000;
            $userList = M('users')->where($condition)->order('user_id')->limit($start,5000)->select();
            if (is_array($userList)) {
                foreach ($userList as $k => $val) {
                    $strTable .= '<tr>';
                    $strTable .= '<td style="text-align:center;font-size:12px;">' . $val['user_id'] . '</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['nickname'] . ' </td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['level'] . '</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['mobile'] . '</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['email'] . '</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">' . date('Y-m-d H:i', $val['reg_time']) . '</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">' . date('Y-m-d H:i', $val['last_login']) . '</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['user_money'] . '</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['pay_points'] . ' </td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['total_amount'] . ' </td>';
                    $strTable .= '</tr>';
                }
                unset($userList);
            }
        }
        $strTable .= '</table>';
        header("Content-type: application/vnd.ms-excel");
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=".$filename."_".date('Y-m-d').".xls");
        header('Expires:0');
        header('Pragma:public');
        echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$strTable.'</html>';

        exit();
    }



}