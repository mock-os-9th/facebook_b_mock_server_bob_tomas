<?php

function createLike($mainPostId,$userId,$likeKind)
{
    $pdo = pdoSqlConnect();
    $query = "insert into postLike (postId, userId, kindOf) value (?,?,?);";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId,$userId,$likeKind]);

    $st=null;
    $pdo = null;
}

function recreateLike($mainPostId,$userId,$likeKind)
{
    $pdo = pdoSqlConnect();
    $query = "update postLike set status=1, updateAt=current_timestamp,kindOf=? where userId=? and postId=?; ";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$likeKind,$userId,$mainPostId]);
    $st=null;
    $pdo = null;
}
function isDuplicatedLike($mainPostId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from postLike where userId=? and postId=? and status=1) as exist;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId,$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return $res[0]["exist"];
}

function isDeletedLike($mainPostId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from postLike where userId=? and postId=? and status!=1) as exist;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId,$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return $res[0]["exist"];
}

function deleteLike($mainPostId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "update postLike set status=2, updateAt=current_timestamp where userId=? and postId=?; ";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId,$mainPostId]);
    $st=null;
    $pdo = null;
}

function isDuplicatedReplyLike($mainReplyId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from replyLike where userId=? and replyId=? and status=1) as exist;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId,$mainReplyId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return $res[0]["exist"];
}


function recreateReplyLike($mainReplyId,$userId,$likeKind)
{
    $pdo = pdoSqlConnect();
    $query = "update replyLike set status=1, kindOf=?,updateAt=current_timestamp where userId=? and replyId=?; ";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$likeKind,$userId,$mainReplyId]);
    $st=null;
    $pdo = null;
}

function isDeletedReplyLike($mainReplyId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from replyLike where userId=? and replyId=? and status!=1) as exist;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId,$mainReplyId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return $res[0]["exist"];
}

function isDeletedReply($mainReplyId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from reply where status!=1 and id=?) as exist";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainReplyId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["exist"];
}

function createReplyLike($mainReplyId,$userId,$likeKind)
{
    $pdo = pdoSqlConnect();
    $query = "insert into replyLike (replyId, userId,kindOf) value (?,?,?);";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainReplyId,$userId,$likeKind]);

    $st=null;
    $pdo = null;
}

function deleteReplyLike($mainReplyId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "update replyLike set status=2, updateAt=current_timestamp where userId=? and replyId=?";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId,$mainReplyId]);

    $st=null;
    $pdo = null;
}

function isReplyLikeUser($replyId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from replyLike where replyId=? and userId=?) as exist";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$replyId,$userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["exist"];
}

function isPostLikeUser($postId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from postLike where postId=? and userId=?) as exist";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$postId,$userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["exist"];
}

function updateLike($mainPostId,$userId,$kindOf)
{
    $pdo = pdoSqlConnect();
    $query = "update postLike set status=1, updateAt=current_timestamp, kindOf=? where userId=? and postId=?; ";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$kindOf,$userId,$mainPostId]);
    $st=null;
    $pdo = null;
}

function updateReplyLike($mainReplyId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "update replyLike set status=1, updateAt=current_timestamp where userId=? and replyId=?; ";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId,$mainReplyId]);
    $st=null;
    $pdo = null;
}
function getTotalLike($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select concat(users.firstName,users.lastName) as name,
       photos.image,
       users.id,
       postLike.kindOf

from postLike

join users
on users.id=postLike.userId

left join profileImage
on profileImage.userId=postLike.userId

left join photos
on photos.id=profileImage.id

where postLike.postId=?
and postLike.status=1
and users.status=1;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res;
}

function getTotalLikeNum($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT count(*) as count
from postLike
where status=1
group by postId
having postId=?
;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["count"];
}

function getLikeKindOf($mainPostId,$kindOf,$offset)
{
    $pdo = pdoSqlConnect();
    $query = "select concat(users.firstName,users.lastName) as name,
       photos.image,
       users.id,
       postLike.kindOf

from postLike

join users
on users.id=postLike.userId

left join profileImage
on profileImage.userId=postLike.userId

left join photos
on photos.id=profileImage.id

where postLike.postId=?
and postLike.status=1
and users.status=1
and postLike.kindOf=?

limit 50 offset $offset;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId,$kindOf]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res;
}

function getLikeKindOfNum($mainPostId,$kindOf)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT count(*) as count
from postLike
where status=1
and kindOf=?
group by postId
having postId=?
;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$kindOf,$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["count"];
}