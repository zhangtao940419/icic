<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/26
 * Time: 14:24
 */

namespace App\Handlers;


class ExcelHtml
{

    //XLS导出

    /**
     * $name  string 文件名称
     * $header array 列标题
     * $dataResult  数组
     **/
    public function ExcelPull($name, $header, $dataResult)
    {
        //这一行没啥用,根据具体情况优化下
        $headTitle = "详情";
        $headtitle = "<tr style='height:50px;border-style:none;><td border=\"0\" style='height:90px;width:470px;font-size:22px;' colspan='11' >{$headTitle}</th></tr>";
        $titlename = "<tr>";
        foreach ($header as $v) {
            $titlename .= "<td>$v</td>";
        }
        $titlename .= "</tr>";
        $fileName = date("Y-m-d") . "-" . $name . ".xls";
        $this->excelData($dataResult, $titlename, $headtitle, $fileName);
    }


    public function excelData($data, $titlename, $title, $filename)
    {
        $str = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"\r\nxmlns:x=\"urn:schemas-microsoft-com:office:excel\"\r\nxmlns=\"http://www.w3.org/TR/REC-html40\">\r\n<head>\r\n<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">\r\n</head>\r\n<body>";
        $str .= "<table border=1>" . $titlename;
        $str .= '';
//        for ($i=1;$i<10000;$i++) {
        foreach ($data as $key => $rt) {
            $str .= "<tr>";
            foreach ($rt as $v) {
                if (preg_match('/^\d{12,50}$/',$v)) {
                    $str .= "<td style='vnd.ms-excel.numberformat:@'>{$v}</td>";
                }else{
                    $str .= "<td>{$v}</td>";
                }
            }
            $str .= "</tr>\n";
        }
//        }
        $str .= "</table></body></html>";
//        $str .= "<span>creator:" . "dd" . "</span>";
        header("Content-Type: application/vnd.ms-excel; name='excel'");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        header("Expires: 0");
//        return response()->download($filename);
        exit($str);


    }

}