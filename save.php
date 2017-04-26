<?php
/**
 * Created by PhpStorm.
 * User: Dmitry
 * Date: 23.04.2017
 * Time: 17:01
 */
include_once "security.php";

$toEncoding='utf-8';
$delimeter=test_input($_POST['delimeter'])??",";
$fromEncoding=test_input($_POST['fromEncoding'])??"utf-8";


$uploaddir = './storage/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
$newfile = $uploaddir . "newfile.txt";

//читаем
$output="";
$fh=fopen($_FILES['userfile']['tmp_name'],'r');
while (!feof($fh)) {
    $output.= fgets($fh);
    
}
fclose($fh);



$fh=fopen($newfile,'w');
$arr=explode($delimeter,$output);
$output=implode($delimeter,$arr);
//$output=mb_convert_encoding($output,'utf-8','windows-1251');

//$output=mb_convert_encoding($output,$toEncoding,$fromEncoding);
fwrite($fh,$output);
fclose($fh);

echo "Файл был успешно загружен.\n";

//просто закачка файла

//if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
//    echo "Файл был успешно загружен.\n";
//} else {
//    echo "Произошла ошибка\n";
//}

