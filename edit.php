<?php
/**
 * Created by PhpStorm.
 * User: Dmitry
 * Date: 23.04.2017
 * Time: 18:50
 */

function get($filename){
    $arr=array();
    if (file_exists($filename) && is_readable ($filename)) {
        $fh = fopen($filename, "r");

        while (!feof($fh)) {
            $line = fgets($fh);
            $arr[]=explode(',',$line);

        }
        fclose($fh);
    }
    return json_encode($arr,JSON_FORCE_OBJECT);
}
function save($filename){
    $csv=json_decode($_POST['table']);
    $fh = fopen($filename, "w");
    $i=0;
    $count=count($csv);
    foreach ($csv as $row){

        $str=implode(',',$row).',';
        if ($i<$count-1) {
            $str .= "\n";
            $i++;
        }

        fputs($fh,$str);
    }
    fclose($fh);
    //todo вернуть файл
}

$dir='./storage/';
$postType=$_POST['type'];
$postName=$_POST['name'];

//$postType='get';
//$postName='q.txt';


if ($postType=='get' and isset($postName)){
    echo get($dir.$postName);
    exit();
}



if ($postType=='save' and isset($postName)){
    save($dir.$postName);
    exit();
}
throw new Exception('Не найдено подходящего действия');