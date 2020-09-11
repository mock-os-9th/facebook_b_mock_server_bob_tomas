<?php


function requestFriend($userIdx, $reqFriIdx){
    $pdo = pdoSqlConnect();
    $query = "insert into friendsRequest(user1Id, user2Id, getAt)
                values (?, ?, current_timestamp);";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $reqFriIdx]);

    $st = null;
    $pdo = null;
}

function isFriend($userIdx, $reqFriIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT *
                FROM (SELECT *
                      FROM users U1
                      WHERE U1.id=?) U
                JOIN friends F
                  ON (U.id = F.user1Id
                      AND F.user2Id=?)
                  OR (U.id = F.user2Id
                      AND F.user1Id=?)) exist";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $reqFriIdx, $reqFriIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function isExistUser($isExistUser){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT *
                FROM users U
                WHERE id = ?) exist";

    $st = $pdo->prepare($query);
    $st->execute([$isExistUser]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function isValidRequest($userIdx, $reqFriIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT *
                FROM friendsRequest
                WHERE user1Id = ?
                  AND user2Id = ?) exist;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $reqFriIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function getRequestStatus($userIdx, $reqFriIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT status
                FROM friendsRequest
                WHERE user1Id = ?
                  AND user2Id = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $reqFriIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['status'];
}



function requestFriendPage($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT U.id userId
                     , F.id requestId
                     , CONCAT(U.firstName, U.lastName) name
                     , CASE WHEN TIMESTAMPDIFF(MINUTE , F.getAt, CURRENT_TIMESTAMP())<1
                                THEN '방금'
                            WHEN TIMESTAMPDIFF(MINUTE , F.getAt, CURRENT_TIMESTAMP())<60
                                THEN CONCAT(TIMESTAMPDIFF(MINUTE,F.getAt, CURRENT_TIMESTAMP), '분전')
                            WHEN TIMESTAMPDIFF(MINUTE , F.getAt, CURRENT_TIMESTAMP()) BETWEEN 60 AND 1440
                                THEN CONCAT(TIMESTAMPDIFF(HOUR, F.getAt, CURRENT_TIMESTAMP), '시간전')
                            WHEN TIMESTAMPDIFF(DAY , F.getAt, CURRENT_TIMESTAMP()) BETWEEN 1 AND 30
                                THEN CONCAT(TIMESTAMPDIFF(DAY, F.getAt, CURRENT_TIMESTAMP), '일전')
                            WHEN TIMESTAMPDIFF(MONTH , F.getAt, CURRENT_TIMESTAMP()) BETWEEN 1 AND 11
                                THEN CONCAT(TIMESTAMPDIFF(MONTH, F.getAt, CURRENT_TIMESTAMP), '달전')
                            WHEN TIMESTAMPDIFF(YEAR , F.getAt, CURRENT_TIMESTAMP()) BETWEEN 1 AND 100
                                THEN CONCAT(TIMESTAMPDIFF(YEAR, F.getAt, CURRENT_TIMESTAMP), '년전')
                            ELSE DATE_FORMAT(F.getAt, '%c월 %e일')
                       END 작성일
                     , IF(ISNULL(photo.image), '프로필없음', photo.image) profileImage
                FROM users U
                JOIN friendsRequest F
                  ON F.user2Id = ?
                 AND U.id = F.user1Id
                LEFT JOIN (SELECT PHO.*
                      FROM profileImage PRO
                      JOIN photos PHO
                        ON PRO.photoId = PHO.id
                      ORDER BY PRO.getAt DESC
                      LIMIT 1) photo
                  ON F.user1Id = photo.userId
                WHERE F.status=0;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function updateRequestStatus($status, $requestId, $requestUserId, $userId){
    $pdo = pdoSqlConnect();

    try {
        $pdo->beginTransaction();

        $query = "UPDATE friendsRequest t
              SET t.status = ?
              WHERE t.id = ?;";

        $st = $pdo->prepare($query);
        $st->execute([$status, $requestId]);

        $query = "INSERT INTO friends(user1Id, user2Id, status)
              VALUES (?,?,1);";

        $st = $pdo->prepare($query);
        $st->execute([$requestUserId, $userId]);

        $pdo->commit();

    }catch (Exception $e){
        $pdo->rollBack();
        $res = (Object)Array();
        echo $e->getMessage();
        $res->isSuccess = FALSE;
        $res->code = 250;
        $res->message = "트랜잭션 실패 롤백.";
        return $res;
    }

    $st = null;
    $pdo = null;
}
//
//function setFriend($requestUserId, $userId){
//    $pdo = pdoSqlConnect();
//
//    $query = "INSERT INTO friends(user1Id, user2Id, status)
//              VALUES (?,?,1);";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$requestUserId, $userId]);
//
//    $st = null;
//    $pdo = null;
//}


function whoIsDeleter($deleter, $deleted){
    $pdo = pdoSqlConnect();
    $query = "SELECT IF((user1Id = ?), 'user1Idisdeleter', 'user2Idisdeleter') deleter
              FROM friends
              WHERE (user1Id=? AND user2Id = ?)OR(user2Id=? AND user1Id = ?);";

    $st = $pdo->prepare($query);
    $st->execute([$deleter, $deleter, $deleted, $deleter, $deleted]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['deleter'];
}


function getFriendId($deleter, $deleted){
    $pdo = pdoSqlConnect();
    $query = "SELECT id
              FROM friends
              WHERE (user1Id=? AND user2Id = ?)OR(user2Id=? AND user1Id = ?);";

    $st = $pdo->prepare($query);
    $st->execute([$deleter, $deleted, $deleter, $deleted]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['id'];
}


function updateFriendStatus($status, $friendId){
    $pdo = pdoSqlConnect();
    $query = "UPDATE friends
              SET status = ?
              WHERE id = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$status, $friendId]);

    $st = null;
    $pdo = null;
}


function whoIsDeleterId($deleter, $deleted){
    $pdo = pdoSqlConnect();
    $query = "SELECT IF((user1Id = ?), user1Id, user2Id) deleterId
              FROM friends
              WHERE (user1Id=? AND user2Id = ?)OR(user2Id=? AND user1Id = ?);";

    $st = $pdo->prepare($query);
    $st->execute([$deleter, $deleter, $deleted, $deleter, $deleted]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['deleterId'];
}

function getUser1Id($friendId){
    $pdo = pdoSqlConnect();
    $query = "SELECT user1Id
              FROM friends
              WHERE id=?;";

    $st = $pdo->prepare($query);
    $st->execute([$friendId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['user1Id'];
}

function getUser2Id($friendId){
    $pdo = pdoSqlConnect();
    $query = "SELECT user2Id
              FROM friends
              WHERE id=?;";

    $st = $pdo->prepare($query);
    $st->execute([$friendId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['user2Id'];
}

function getFriendStatus($friendId){
    $pdo = pdoSqlConnect();
    $query = "SELECT status
              FROM friends
              WHERE id=?;";

    $st = $pdo->prepare($query);
    $st->execute([$friendId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['status'];
}

function isValidUserid($id){
    $pdo = pdoSqlConnect();
    $query = "select EXISTS(select *
                            from users
                            where id = ?) exist";

    $st = $pdo->prepare($query);
    $st->execute([$id]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function isExistRequest($id){
    $pdo = pdoSqlConnect();
    $query = "select EXISTS(select *
                            from friendsRequest
                            where id = ?) exist";

    $st = $pdo->prepare($query);
    $st->execute([$id]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function getReqStatus($id){
    $pdo = pdoSqlConnect();
    $query = "select status
                            from friendsRequest
                            where id = ?";

    $st = $pdo->prepare($query);
    $st->execute([$id]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['status'];
}