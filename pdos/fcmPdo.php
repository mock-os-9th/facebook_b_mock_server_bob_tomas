<?php

function postDeviceUser($deviceToken, $userId){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO fcmToken(Token, userId)
              VALUES (?, ?)";

    $st = $pdo->prepare($query);
    $st->execute([$deviceToken, $userId]);

    $st = null;
    $pdo = null;
}

function isExistDevice($deviceToken, $userId){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM fcmToken WHERE Token=? AND userId=?) exist;";

    $st = $pdo->prepare($query);
    $st->execute([$deviceToken, $userId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function getTokenStatus($deviceToken, $userId){
    $pdo = pdoSqlConnect();
    $query = "SELECT status FROM fcmToken WHERE Token=? AND userId=?;";

    $st = $pdo->prepare($query);
    $st->execute([$deviceToken, $userId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['status'];
}

function updateStatus($deviceToken, $userId){
    $pdo = pdoSqlConnect();
    $query = "UPDATE fcmToken
                SET status = 0
                WHERE Token = ? AND userId=?;";

    $st = $pdo->prepare($query);
    $st->execute([$deviceToken, $userId]);

    $st = null;
    $pdo = null;
}

function deviceDisabled($deviceToken, $userId){
    $pdo = pdoSqlConnect();
    $query = "UPDATE fcmToken
                SET status = 1
                WHERE Token = ? AND userId=?;";

    $st = $pdo->prepare($query);
    $st->execute([$deviceToken, $userId]);

    $st = null;
    $pdo = null;
}

function sendFcm($fcmToken,$username, $data, $key)
{
    $url = 'https://fcm.googleapis.com/fcm/send';

    $headers = array(
        'Authorization:key='. $key,
        'Content-Type:application/json'
    );

    $fields['to'] = $fcmToken;
    $fields['priority'] = "high";

    $notification['title'] = $data;
    $notification['body'] = $username;
    $fields['notification'] = $notification;

    $data12['title'] = $username;
    $data12['message'] = $data;
    $data12['test'] = $data;
    $fields['data'] = $data12;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    //echo json_encode($fields);
    $result = curl_exec($ch);

    echo $result;

    echo curl_error($ch);
    if ($result === FALSE) {
        //die('FCM Send Error: ' . curl_error($ch));
    }

    curl_close($ch);
    return $result;
}

function getUserName($id){
    $pdo = pdoSqlConnect();
    $query = "select concat(firstName, lastName) name
                from users
                where id=?;";

    $st = $pdo->prepare($query);
    $st->execute([$id]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['name'];
}

function getFcmToken($id){
    $pdo = pdoSqlConnect();
    $query = "select Token
                from fcmToken
                where userId=?;
                limit 1";

    $st = $pdo->prepare($query);
    $st->execute([$id]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['Token'];
}

function getFCMStatus($fcmToken){
    $pdo = pdoSqlConnect();
    $query = "select status
                from fcmToken
                where Token=?;
                limit 1";

    $st = $pdo->prepare($query);
    $st->execute([$fcmToken]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['status'];
}