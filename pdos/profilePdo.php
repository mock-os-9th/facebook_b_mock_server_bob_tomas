<?php


function userProfile($pageUserIdx, $userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT CONCAT(firstName, lastName) name,
       IF(isOpenResister=0, '가입날짜 비공개', CONCAT(DATE_FORMAT(U.getAt, '%Y'),'년 ', DATE_FORMAT(U.getAt, '%m'), '월에 가입')) createDate,
       IF(ISNULL(hobby), '등록된 취미 없음', hobby) hobby,
       IF(ISNULL(living)||isOpenLiving=0, '정보없음', living) living,
       IF(ISNULL(U.from)||isOpenFrom=0, '정보없음', U.from) hometown,
       IF(EXISTS(select P.image
                            from photos P
                            join profileImage F
                              ON P.id = F.photoId
                             AND P.userId = ?
                            ORDER BY F.getAt DESC
                            LIMIT 1), (select P.image
                            from photos P
                            join profileImage F
                              ON P.id = F.photoId
                             AND P.userId = ?
                            ORDER BY F.getAt DESC
                            LIMIT 1), '프로필사진없음') profilePhoto,
       IF(EXISTS(select P.image
                            from photos P
                            join coverImage C
                              ON P.id = C.photoId
                            WHERE P.userId = ?
                            ORDER BY C.getAt DESC
                            LIMIT 1), (select P.image
                            from photos P
                            join coverImage C
                              ON P.id = C.photoId
                            WHERE P.userId = ?
                            ORDER BY C.getAt DESC
                            LIMIT 1), '커버사진없음') coverPhoto,
       IF(?=?, 0, IF(EXISTS(SELECT *
                            FROM friends
                            WHERE ((user1Id=? AND user2Id=?)
                               OR (user2Id=? AND user1Id=?))
                            AND status=1), 1, IF(EXISTS(SELECT *
                                                FROM friends
                                                WHERE ((user2Id=? AND status=2)
                                                   OR (user1Id=? AND status=3))), 3, IF(EXISTS(SELECT *
FROM friends
WHERE ((user1Id=? AND status=2)
   OR (user2Id=? AND status=3))), 4, IF(EXISTS(SELECT *
FROM friendsRequest
WHERE user1Id=? AND user2Id = ? AND status=0),5,IF(EXISTS(SELECT *
FROM friendsRequest
WHERE user2Id=? AND user1Id = ? AND status=0), 6, IF(EXISTS(SELECT *
FROM friendsRequest
WHERE user1Id=? AND user2Id = ? AND status=2), 7, IF(EXISTS(SELECT *
                                        FROM friendsRequest
                                        WHERE user2Id=? AND user1Id = ? AND status=2), 8, IF(NOT EXISTS(SELECT *
FROM friends
WHERE ((user1Id=? AND user2Id=?)
   OR (user2Id=? AND user1Id=?))), 2, '예외'))))))))) IsFriend
FROM users U
WHERE U.id = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$pageUserIdx, $pageUserIdx, $pageUserIdx, $pageUserIdx,
        $userIdx, $pageUserIdx,$userIdx, $pageUserIdx,$userIdx, $pageUserIdx,$userIdx, $userIdx,
        $userIdx, $userIdx,$userIdx, $pageUserIdx,$userIdx, $pageUserIdx,$userIdx, $pageUserIdx,
        $userIdx, $pageUserIdx,$userIdx, $pageUserIdx, $userIdx, $pageUserIdx,
        $pageUserIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function getUserStatus($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT status
                FROM users U
                WHERE U.id = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['status'];
}

function userProfileFriendsCount($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT COUNT(*) friendsCount
                FROM users U1
                WHERE U1.id IN (SELECT IF((U2.id=F.user1Id), F.user2Id, F.user1Id)
                             FROM users U2
                             JOIN friends F
                               ON U2.id = F.user1Id
                               OR U2.id = F.user2Id
                              AND F.status = 1
                             WHERE U2.id = ?)
                AND U1.status = 1";

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
                      ON PH.id = P.photoId
                      ORDER BY P.getAt DESC
                      limit 1) PRO
                  ON U1.id = PRO.userId
                WHERE U1.id IN (SELECT IF((U2.id=F.user1Id), F.user2Id, F.user1Id)
                             FROM users U2
                             JOIN friends F
                               ON U2.id = F.user1Id
                               OR U2.id = F.user2Id
                              AND F.status = 1
                             WHERE U2.id = ?)
                AND U1.status = 1
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

    return $res[0];
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
                      ON PH.id = P.photoId
                      LIMIT 1) PRO
                  ON U1.id = PRO.userId
                WHERE U1.id IN (SELECT IF((U2.id=F.user1Id), F.user2Id, F.user1Id)
                             FROM users U2
                             JOIN friends F
                               ON U2.id = F.user1Id
                               OR U2.id = F.user2Id
                              AND F.status = 1
                             WHERE U2.id = ?)
                  AND U1.status = 1
                limit 50 offset $offset;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


function getSearchedFriend($userIdx, $search){
    $pdo = pdoSqlConnect();
    $query = "SELECT CONCAT(firstName, lastName) name, U1.id, IF(ISNULL(PRO.image), '프로필없음', PRO.image) profileImage
                FROM users U1
                LEFT JOIN (select PH.*
                     FROM profileImage P
                      JOIN photos PH
                      ON PH.id = P.photoId
                      LIMIT 1) PRO
                  ON U1.id = PRO.userId
                WHERE U1.id IN (SELECT IF((U2.id=F.user1Id), F.user2Id, F.user1Id)
                             FROM users U2
                             JOIN friends F
                               ON U2.id = F.user1Id
                               OR U2.id = F.user2Id
                              AND F.status = 1
                             WHERE U2.id = ?)
                  AND CONCAT(firstName, lastName) LIKE CONCAT('%', ?, '%')
                  AND U1.status = 1;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $search]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


function getMyDetailWork($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT P.name, W.startAt, IF(ISNULL(W.endAt), '현재', W.endAt) endAt, P.id pageIdx, IF(ISNULL(PH.image), '프로필없음', PH.image) image
                FROM work W
                LEFT JOIN pages P
                  ON W.workId = P.id
                LEFT JOIN pagephotos PH
                  ON P.profileId = PH.id
                WHERE W.userId = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getMyDetailSchool($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT P.name, S.startAt, IF(ISNULL(S.endAt), '현재', S.endAt) endAt, P.id pageIdx, IF(ISNULL(PH.image), '프로필없음', PH.image) image
                FROM school S
                LEFT JOIN pages P
                  ON S.schoolId = P.id
                LEFT JOIN pagephotos PH
                  ON P.profileId = PH.id
                WHERE S.userId = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


function getMyDetail($userIdx){
    $pdo = pdoSqlConnect();
    $query = "SELECT phone, sex, birth, 
       IF(ISNULL(hobby), '등록된 취미 없음', hobby) hobby,
       IF(ISNULL(living), '정보없음', living) living,
       IF(ISNULL(users.from), '정보없음', users.from) hometown,
       IF(ISNULL(couple), '정보없음', couple) couple,
       IF(ISNULL(couple), '정보없음', coupleAnni) coupleAnni
                FROM users
                WHERE id = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function postPhotos($userIdx, $img){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO photos (USERID, IMAGE, GETAT) 
              VALUES(?, ?, CURRENT_TIMESTAMP);";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $img]);

    $st = null;
    $pdo = null;
}

function getPhotoId($img){
    $pdo = pdoSqlConnect();
    $query = "SELECT id
              FROM photos
              WHERE image = ?";

    $st = $pdo->prepare($query);
    $st->execute([$img]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]["id"];
}

function postProfileImage($userIdx, $photoId){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO profileImage (USERID, photoId, GETAT)
              VALUES(?, ?, CURRENT_TIMESTAMP);";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $photoId]);

    $st = null;
    $pdo = null;
}

function postImgUrl($userIdx, $url){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO photos(userId, image, getAt) 
              VALUES (?, ?, CURRENT_TIMESTAMP);";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $url]);

    $st = null;
    $pdo = null;
}

function isValidFileName($filename){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT *
                             FROM photos
                             WHERE image = ?) exist";

    $st = $pdo->prepare($query);
    $st->execute([$filename]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function setProfileImage($userId, $photoId){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO profileImage(userId, photoId, getAt)
              VALUES (?, ?, CURRENT_TIMESTAMP);";

    $st = $pdo->prepare($query);
    $st->execute([$userId, $photoId]);

    $st = null;
    $pdo = null;
}


function setCoverImage($userId, $photoId){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO coverImage(userId, photoId, getAt)
              VALUES (?, ?, CURRENT_TIMESTAMP);";

    $st = $pdo->prepare($query);
    $st->execute([$userId, $photoId]);

    $st = null;
    $pdo = null;
}