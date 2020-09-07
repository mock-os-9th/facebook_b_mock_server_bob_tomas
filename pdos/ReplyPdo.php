<?php

function createReply($mainPostId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "insert into reply (postId, userId) value (?,?);";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId,$userId]);
    $st=null;
    $query = "select
	*
from
	reply
where userId=?
order by id desc

limit 1;";
    $st = $pdo->prepare($query);
    $st->execute([$userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return $res[0]["id"];
}

function saveFile1($file, $data)
{
    $ext_str = "pdf,jpg,gif,png,mp4";
    $ext_str_image = "pdf,jpg,gif,png,jpeg";
    $ext_str_video = "mp4";

    $ext = substr($file['name'], strrpos($file['name'], '.') + 1);
    if(in_array($ext, explode(',', $ext_str_image)))
    {
        $uploadBase = "./photos/";
        $name =$file['name'];
        $uploadFile = $uploadBase.$name;
        $file_calling = '54.180.85.194/photos/'.$name;
        move_uploaded_file($file['tmp_name'], $uploadFile);
        $pdo = pdoSqlConnect();
        $query = "insert into photos (userId, image) value (?,?);";
        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$data->userId,$file_calling]);
        $st=null;

        $query = "select
                                *
                            from
                                photos
                            where userId=?
                            order by id desc
                            
                            limit 1;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$data->userId]);

        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();
        $st=null;
        $pdo = null;

        return $res[0]["id"];

    }

    if(in_array($ext,explode(',', $ext_str_video))) {
        $uploadBase = './videos/';
        $name = $file['name'];
        $uploadFile = $uploadBase . $name;
        $file_calling = '3.35.3.242/videos/' . $name;
        move_uploaded_file($file['tmp_name'], $uploadFile);
        $pdo = pdoSqlConnect();
        $query = "insert into videos (userId, video) value (?,?);";
        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$data->userId, $file_calling]);
        $st = null;

        $query = "select
                                *
                            from
                                videos
                            where userId=?
                            order by id desc
                            
                            limit 1;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$data->userId]);

        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();
        $st = null;
        $pdo = null;

        return $res[0]["id"];
    }
}

function saveReplyFile($thisReplyId,$saveFilesId,$file)
{
    $ext_str_image = "pdf,jpg,gif,png,jpeg";
    $ext_str_video = "mp4";

    $ext = substr($file['name'], strrpos($file['name'], '.') + 1);
    if(in_array($ext,explode(',', $ext_str_image)))
    {
        $pdo = pdoSqlConnect();
        $query = "insert into replyFiles( replyId,imageId) value (?,?);";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$thisReplyId,$saveFilesId]);
        $st=null;
        $pdo=null;
    }elseif (in_array($ext,explode(',', $ext_str_video)))
    {
        $pdo = pdoSqlConnect();
        $query = "insert into replyFiles( replyId,videoId) value (?,?);";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);œ
        $st->execute([$thisReplyId,$saveFilesId]);
        $st=null;
        $pdo=null;
    }
}

function putReplyContent($replyContent,$thisReplyId)
{
    $pdo = pdoSqlConnect();
    $query = "update  reply set content=? where id=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$replyContent,$thisReplyId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

    $st=null;$pdo = null;
}

function createReReply($mainPostId,$userId,$mainReplyId)
{
    $pdo = pdoSqlConnect();
    $query = "insert into reply (postId, userId,isReply) value (?,?,?);";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId,$userId,$mainReplyId]);
    $st=null;
    $query = "select
	*
from
	reply
where userId=?
and isReply=?
order by id desc

limit 1;";
    $st = $pdo->prepare($query);
    $st->execute([$userId,$mainReplyId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return $res[0]["id"];
}

function deleteReply($replyId)
{
    $pdo = pdoSqlConnect();
    $query = "update reply set status = 2, updateAt=CURRENT_TIMESTAMP where id=?";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$replyId]);
    $st=null;
    $pdo = null;
}

function isReplyWriter($replyId,$data)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from reply where userId=? and id=?) as exist";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$data->userId,$replyId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;
    return $res[0]["exist"];
}

function updateReply($changeContent,$replyId)
{
    $pdo = pdoSqlConnect();
    $query = "update reply set content = ?,updateAt=CURRENT_TIMESTAMP where id=?";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$changeContent,$replyId]);
    $st=null;
    $pdo = null;
}