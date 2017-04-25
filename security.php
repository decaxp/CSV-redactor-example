<?php
/**
 * Created by PhpStorm.
 * User: Dmitry
 * Date: 23.04.2017
 * Time: 23:44
 */

function test_input($data) {
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}