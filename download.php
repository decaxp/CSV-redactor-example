<?php
/**
 * Created by PhpStorm.
 * User: Dmitry
 * Date: 23.04.2017
 * Time: 23:39
 */
include_once "security.php";

$url="http://csv:81/index.php";
$dir="./storage/";
$charset=test_input($_GET['charset'])??'utf-8';

function file_force_download($file,$ch) {
    if (file_exists($file)) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
//        header('Content-Type: application/octet-stream');
        header('Content-Type: text/html; charset='.$ch);
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
//        header('Charset: '.$ch);

        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
//        $str=file_get_contents($file);
//        $str=iconv($ch,'utf-8',$str);
//        $str=iconv('utf-8',$ch,$str);


//        $str=iconv($enc,$ch,$str);
//        echo $str;
    }
}

$name=test_input($dir.$_GET['name']);
if (isset($name)){
    file_force_download($name,$charset);
}