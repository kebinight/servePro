<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2016-1-10 14:46:41 by allen <blog.rc5j.cn> , caowenpeng1990@126.com
 */

namespace Wpadmin\Utils;

class Export {

    /**
     * 导出csv
     * @param type $columnArr
     * @param type $data
     * @param type $filename 
     * @param type $name Description
     */
    public static function exportCsv($columnArr, $data, $filename,$debug =false) {
        if(!$debug){
//        header( 'Content-Type: text/csv' );
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
            
        }
        // 从数据库中获取数据，为了节省内存，不要把数据一次性读到内存，从句柄中一行一行读即可  
        // 打开PHP文件句柄，php://output 表示直接输出到浏览器  
        $fp = fopen('php://output', 'w');
        // 输出Excel列名信息  
        $head = $columnArr;
        foreach ($head as $i => $v) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码  
            $head[$i] = iconv('utf-8', 'gbk', $v);
        }
        // 将数据通过fputcsv写到文件句柄  
        fputcsv($fp, $head);
        // 计数器  
        $cnt = 0;
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小  
        $limit = 100000;
        // 逐行取出数据，不浪费内存  
        foreach ($data as $key => $value) {
            $cnt ++;
            if ($limit == $cnt) {
                //刷新一下输出buffer，防止由于数据过多造成问题  
                ob_flush();
                flush();
                $cnt = 0;
            }
            foreach ($value as $i => $v) {
                $value[$i] = iconv('utf-8', 'gbk//ignore', $v);
            }
            fputcsv($fp, $value);
        }
        fclose($fp);
        exit();
    }

}
