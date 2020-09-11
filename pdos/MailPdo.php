<?php

function getNameFromSign($sign)
{
    $pdo = pdoSqlConnect();
    $query = "select concat(firstName,lastName) as name from users where email=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$sign]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["name"]);
}