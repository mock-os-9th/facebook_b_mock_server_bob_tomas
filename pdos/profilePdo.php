<?php


function userProfile($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT CONCAT(firstName, lastName) name,
                       IF(isOpenResister=0, '가입날짜 비공개', CONCAT(DATE_FORMAT(U.getAt, '%Y'),'년 ', DATE_FORMAT(U.getAt, '%m'), '월에 가입')) createDate,
                       IF(ISNULL(hobby), '등록된 취미 없음', hobby) hobby,
                       IF(ISNULL(living)||isOpenLiving=0, '정보없음', living) living,
                       IF(ISNULL(U.from)||isOpenFrom=0, '정보없음', U.from) living,
                       IF(EXISTS(SELECT *
                                 FROM profileImage P
                                 WHERE U.id = P.userId
                                 LIMIT 1), (select P.image
                                            from photos P
                                            join profileImage F
                                              ON P.id = F.photoId
                                             AND P.userId = ?
                                            LIMIT 1), '프로필사진없음') profilePhoto,
                       IF(EXISTS(SELECT *
                                 FROM coverImage C
                                 WHERE U.id = C.userId
                                 LIMIT 1), (select P.image
                                            from photos P
                                            join coverImage C
                                              ON P.id = C.photoId
                                             AND P.userId = ?
                                            LIMIT 1), '커버사진없음') coverPhoto
                FROM users U
                WHERE U.id = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function userProfileFriendsCount($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT count(*)
                FROM users U
                JOIN friends F
                  ON U.id = F.user1Id
                  OR U.id = F.user2Id
                 AND F.status = 1
                WHERE U.id = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function userProfileFriends($userIdx){
    $pdo = pdoSqlConnect();
    $query = "";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}