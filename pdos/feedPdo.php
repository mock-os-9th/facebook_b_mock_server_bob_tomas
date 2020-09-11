<?php


function getRecentPostId()
{
    $pdo = pdoSqlConnect();
    $query = "select id
              from posts
              order by getAt desc
              limit 1;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["id"];
}


function getPostWriterAndOpen($postId){
    $pdo = pdoSqlConnect();
    $query = "select userId, isOpen
              from posts
              where id = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$postId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0];
}

function getPostInfo2($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select posts.userId,CONCAT(firstName, lastName) name,
       IF(EXISTS(SELECT *
                 FROM profileImage P
                 WHERE posts.userId=P.userId
                 LIMIT 1),(select P.image
                            from photos P
                            join profileImage F
                              ON P.id = F.photoId
                             AND P.userId = F.userId
                            where posts.userId=P.userId
                            ORDER BY F.getAt DESC
                            LIMIT 1), '프로필사진없음') profilePhoto,
 (
    case
               when timestampdiff(DAY, posts.getAt, now()) > 7
                   then time_format(posts.getAt, '%Y년 %m월 %d일')
               when timestampdiff(DAY, posts.getAt, now()) >= 1 and timestampdiff(DAY, now(), posts.getAt) <= 7
                   then concat(timestampdiff(DAY, posts.getAt, now()), '일 전')
               when timestampdiff(HOUR, posts.getAt, now()) <= 24 and timestampdiff(HOUR, now(), posts.getAt) >= 1
                   then concat(timestampdiff(HOUR, posts.getAt, now()), '시간 전')
               when timestampdiff(MINUTE, posts.getAt, now()) <= 60 and timestampdiff(MINUTE, now(), posts.getAt) >= 1
                   then concat(timestampdiff(MINUTE, posts.getAt, now()), '분 전')
               else '방금전' end
    ) getAt,
       isOpen,
       emotion
from posts posts

LEFT join users
on users.id = posts.userId

WHERE posts.id=?
and posts.status=1;
";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res;
}

function getIsChild($toReadPostId){
    $pdo = pdoSqlConnect();
    $query = "select IF(child=-1, 1, 0) child
            from posts
            where id = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$toReadPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]['child'];
}

function CheckIsOpen41($mainPostId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from postShowed where postId=? and userId=?) as exist;";

    $st = $pdo->prepare($query);

    $st->execute([$mainPostId,$userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["exist"];
}

function getMainContent2($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select IF(ISNULL(content), '내용없음', content) content from posts where id=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["content"];
}