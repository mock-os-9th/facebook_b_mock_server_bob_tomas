<?php


function userProfile($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT CONCAT(firstName, lastName) name,
       IF(isOpenResister=0, '가입날짜 비공개', CONCAT(DATE_FORMAT(U.getAt, '%Y'),'년 ', DATE_FORMAT(U.getAt, '%m'), '월에 가입')) createDate,
       IF(ISNULL(hobby), '등록된 취미 없음', hobby) hobby,
       IF(ISNULL(living)||isOpenLiving=0, '정보없음', living) living,
       IF(ISNULL(U.from)||isOpenFrom=0, '정보없음', U.from) hometown,
       IF(EXISTS(SELECT *
                 FROM profileImage P
                 WHERE U.id = P.userId
                 LIMIT 1), (select P.image
                            from photos P
                            join profileImage F
                              ON P.id = F.photoId
                             AND P.userId = ?
                            ORDER BY F.getAt DESC
                            LIMIT 1), '프로필사진없음') profilePhoto,
       IF(EXISTS(SELECT *
                 FROM coverImage C
                 WHERE U.id = C.userId
                 LIMIT 1), (select P.image
                            from photos P
                            join coverImage C
                              ON P.id = C.photoId
                             AND P.userId = ?
                            ORDER BY C.getAt DESC
                            LIMIT 1), '커버사진없음') coverPhoto
FROM users U
WHERE U.id = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $userIdx, $userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function userProfileFriendsCount($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT count(*) 'friendsCount'
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

    return $res[0];
}

function userProfileFriends($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT CONCAT(firstName, lastName) name, U1.id, IF(ISNULL(PRO.image), '프로필없음', PRO.image) profileImage
                FROM users U1
                LEFT JOIN (select PH.*
                     FROM profileImage P
                      JOIN photos PH
                      ON PH.id = P.photoId) PRO
                  ON U1.id = PRO.userId
                WHERE U1.id IN (SELECT IF((U2.id=F.user1Id), F.user2Id, F.user1Id)
                             FROM users U2
                             JOIN friends F
                               ON U2.id = F.user1Id
                               OR U2.id = F.user2Id
                              AND F.status = 1
                             WHERE U2.id = ?)
                limit 6;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function openModifyPage($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT IF(isOpenResister=0, '가입날짜 비공개', CONCAT(DATE_FORMAT(U.getAt, '%Y'),'년 ', DATE_FORMAT(U.getAt, '%m'), '월에 가입')) createDate,
       IF(ISNULL(U.introduce), '소개없음', U.introduce) introduce,
       IF(EXISTS(SELECT *
                 FROM work W
                 WHERE U.id = W.userId
                 LIMIT 1), (select P.name
                            from work W
                            join pages P
                              ON W.workId = P.id
                             AND W.userId = ?
                            ORDER BY W.getAt DESC
                            LIMIT 1), '직장등록필요') profileWork,
       IF(EXISTS(SELECT *
                 FROM school S
                 WHERE U.id = S.userId
                 LIMIT 1), (select P.name
                            from school S
                            join pages P
                              ON S.schoolId = P.id
                             AND S.userId = ?
                            ORDER BY S.getAt DESC
                            LIMIT 1), '학교등록필요') profileSchool,
       IF(ISNULL(living)||isOpenLiving=0, '정보없음', living) living,
       IF(ISNULL(U.from)||isOpenFrom=0, '정보없음', U.from) hometown,
       IF(ISNULL(hobby), '등록된 취미 없음', hobby) hobby,
       IF(ISNULL(couple), '등록된 연애/결혼 없음', couple) hobby,
       IF(EXISTS(SELECT *
                 FROM profileImage P
                 WHERE U.id = P.userId
                 LIMIT 1), (select P.image
                            from photos P
                            join profileImage F
                              ON P.id = F.photoId
                             AND P.userId = ?
                            ORDER BY F.getAt DESC
                            LIMIT 1), '프로필사진없음') profilePhoto,
       IF(EXISTS(SELECT *
                 FROM coverImage C
                 WHERE U.id = C.userId
                 LIMIT 1), (select P.image
                            from photos P
                            join coverImage C
                              ON P.id = C.photoId
                             AND P.userId = ?
                            ORDER BY C.getAt DESC
                            LIMIT 1), '커버사진없음') coverPhoto
FROM users U
WHERE U.id = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $userIdx, $userIdx, $userIdx, $userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function modifyIntroduce($contents, $userIdx){
    $pdo = pdoSqlConnect();
    $query = "update users
                set introduce = ?
                where id = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$contents, $userIdx]);

    $st = null;
    $pdo = null;
}

function deleteIntroduce($userIdx){
    $pdo = pdoSqlConnect();
    $query = "update users
                set introduce = null
                where id = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st = null;
    $pdo = null;
}

function modifyHobby($contents, $userIdx){
    $pdo = pdoSqlConnect();
    $query = "update users
                set introduce = ?
                where id = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$contents, $userIdx]);

    $st = null;
    $pdo = null;
}


function getAllFriends($userIdx, $offset){
    $pdo = pdoSqlConnect();
    $query = "SELECT CONCAT(firstName, lastName) name, U1.id, IF(ISNULL(PRO.image), '프로필없음', PRO.image) profileImage
                FROM users U1
                LEFT JOIN (select PH.*
                     FROM profileImage P
                      JOIN photos PH
                      ON PH.id = P.photoId) PRO
                  ON U1.id = PRO.userId
                WHERE U1.id IN (SELECT IF((U2.id=F.user1Id), F.user2Id, F.user1Id)
                             FROM users U2
                             JOIN friends F
                               ON U2.id = F.user1Id
                               OR U2.id = F.user2Id
                              AND F.status = 1
                             WHERE U2.id = ?)
                limit 50 offset $offset;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


function getAllFriendsCount($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT count(*)
                FROM users U1
                LEFT JOIN (select PH.*
                     FROM profileImage P
                      JOIN photos PH
                      ON PH.id = P.photoId) PRO
                  ON U1.id = PRO.userId
                WHERE U1.id IN (SELECT IF((U2.id=F.user1Id), F.user2Id, F.user1Id)
                             FROM users U2
                             JOIN friends F
                               ON U2.id = F.user1Id
                OR U2.id = F.user2Id
                AND F.status = 1
              WHERE U2.id = ?);";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getMyDetailPage($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT count(*)
                FROM users U1
                LEFT JOIN (select PH.*
                     FROM profileImage P
                      JOIN photos PH
                      ON PH.id = P.photoId) PRO
                  ON U1.id = PRO.userId
                WHERE U1.id IN (SELECT IF((U2.id=F.user1Id), F.user2Id, F.user1Id)
                             FROM users U2
                             JOIN friends F
                               ON U2.id = F.user1Id
                OR U2.id = F.user2Id
                AND F.status = 1
              WHERE U2.id = ?);";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}