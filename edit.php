<?php
/**
 * Created by PhpStorm.
 * User: Dmitry
 * Date: 23.04.2017
 * Time: 18:50
 */

include_once "security.php";

function get($filename,$del,$defCharset,$fromCh){
    $arr=array();
    if (file_exists($filename) && is_readable ($filename)) {
        $fh = fopen($filename, "r");

        while (!feof($fh)) {
            $line = fgets($fh);
            $line=iconv($fromCh,$defCharset,$line);
            $arr[]=explode($del,$line);

        }
        fclose($fh);
    }

    return json_encode($arr,JSON_FORCE_OBJECT);
}
function save($filename,$json,$del,$enc,$newfile){
    $csv=json_decode($json);
    $fh = fopen($newfile, "w");
    $i=0;
    $count=count($csv);
    $output="";

    foreach ($csv as $row){
        $str=implode($del,$row).$del;
        if ($i<$count-1) {
            $str .= "\n";
            $i++;
        }
        $output.=$str;
    }

//    $outputConverted=mb_convert_encoding($output,$enc,"UTF-8");
    $outputConverted=iconv("UTF-8",$enc,$output);
    fwrite($fh,$outputConverted);
    fclose($fh);

    copy($newfile,$filename);
}

function delete($oldfile,$newfile){
    $res1= (int)unlink($oldfile);
    $res2= (int)unlink($newfile);
    return (int)($res1+$res2);
}

$dir='./storage/';
$postType=test_input($_POST['type']);
$postName=test_input($_POST['name']);
$delimeter=test_input($_POST['delimeter'])??',';
$toEncoding=test_input($_POST['toEncoding'])??',';
$defaultCharset=test_input($_POST['defaultCharset'])??'utf-8';
$fromCharset=test_input($_POST['fromCharset'])??'utf-8';
$getfile="newfile.txt";
//$postType='get';
//$postName='q.txt';


if ($postType=='get' and isset($postName)){
    echo get($dir.$getfile,$delimeter,$defaultCharset,$fromCharset);
    exit();
}

if ($postType=='delete' and isset($postName)){
    echo delete($dir.$postName,$dir.$getfile);
    exit();
}

if ($postType=='save' and isset($postName)){
    save($dir.$postName,$_POST['table'],$delimeter,$toEncoding,$dir.$getfile);
    exit();
}
throw new Exception('Не найдено подходящего действия');