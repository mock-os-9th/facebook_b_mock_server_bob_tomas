<?php

//DB 정보
function pdoSqlConnect()
{
    try {
        $DB_HOST = "facebooktestrds.cbjco5lbvu5u.ap-northeast-2.rds.amazonaws.com";
        $DB_NAME = "facebookTest";
        $DB_USER = "root";
        $DB_PW = "facesoft02!";
        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PW);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}