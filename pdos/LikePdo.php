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


function recreateReplyLike($mainReplyId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "update replyLike set status=1, updateAt=current_timestamp where userId=? and replyId=?; ";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId,$mainReplyId]);
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

function createReplyLike($mainReplyId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "insert into replyLike (postId, userId) value (?,?);";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainReplyId,$userId]);

    $st=null;
    $pdo = null;
}