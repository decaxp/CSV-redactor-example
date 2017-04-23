<?php
/**
 * Created by PhpStorm.
 * User: Dmitry
 * Date: 23.04.2017
 * Time: 17:01
 */

$uploaddir = './storage/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "Файл был успешно загружен.\n";
} else {
    echo "Произошла ошибка\n";
}

