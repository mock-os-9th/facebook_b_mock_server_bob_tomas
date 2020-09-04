<?php
function saveFile($files,$data,$i)
{
    $ext_str = "pdf,jpg,gif,png,mp4";
    $ext_str_image = "pdf,jpg,gif,png,jpeg";
    $ext_str_video = "mp4";

    $ext = substr($files['name'][$i], strrpos($files['name'][$i], '.') + 1);
    if(in_array($ext, explode(',', $ext_str_image)))
    {
        $uploadBase = "./photos/";
        $name =$files['name'][$i];
        $uploadFile = $uploadBase.$name;
        $file_calling = '54.180.85.194/photos/'.$name;
        move_uploaded_file($files['tmp_name'][$i], $uploadFile);
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
                            order by getAt desc
                            
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
        $name = $files['name'][$i];
        $uploadFile = $uploadBase . $name;
        $file_calling = '3.35.3.242/videos/' . $name;
        move_uploaded_file($_FILES['uploaded_file']['tmp_name'][$i], $uploadFile);
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
                            order by getAt desc
                            
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


function createMainPost($userId,$isOpen)
{
    $pdo = pdoSqlConnect();
    $query = "insert into posts( userId,isOpen) value (?,?);";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId,$isOpen]);
    $st=null;

    $query = "select
	*
from
	posts
where userId=?
order by getAt desc

limit 1;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return $res[0]["id"];
}

function putCheckIn($checkIn,$mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "update  posts set pageId=? where id=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$checkIn,$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

    $st=null;$pdo = null;
}

function putEmotion($emotion,$mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "update  posts set emotion=? where id=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$emotion,$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

    $st=null;$pdo = null;
}

function putContent($content,$postId)
{
    $pdo = pdoSqlConnect();
    $query = "update  posts set content=? where id=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$content,$postId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

    $st=null;$pdo = null;
}

function createPostWithFiles($userId,$mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "insert into posts( userId,child) value (?,?);";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId,$mainPostId]);
    $st=null;

    $query = "select
        *
        from
	posts
where userId=?
order by id desc

limit 1;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userId]);

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return $res[0]["id"];
}

function savePostFiles($thisPostId,$saveFilesId,$files,$i)
{
    $ext_str = "pdf,jpg,gif,png,mp4";
    $ext_str_image = "pdf,jpg,gif,png,jpeg";
    $ext_str_video = "mp4";

    $ext = substr($files['name'][$i], strrpos($files['name'][$i], '.') + 1);
    if(in_array($ext,explode(',', $ext_str_image)))
    {
        $pdo = pdoSqlConnect();
        $query = "insert into postFiles( postId,imageId) value (?,?);";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$thisPostId,$saveFilesId]);
        $st=null;
        $pdo=null;
    }elseif (in_array($ext,explode(',', $ext_str_video)))
    {
        $pdo = pdoSqlConnect();
        $query = "insert into postFiles( postId,videoId) value (?,?);";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);œ
        $st->execute([$thisPostId,$saveFilesId]);
        $st=null;
        $pdo=null;
    }

}

function isPostWriter($mainPostId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from posts where userId=? and id=?) as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);œ
    $st->execute([$userId,$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return $res[0]["exist"];
}

function updateContent($changeFileComment,$changeFilePostId)
{
    $pdo = pdoSqlConnect();
    $query = "update posts set content=?,updateAt=current_timestamp where id=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$changeFileComment,$changeFilePostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

    $st=null;$pdo = null;
}

function deleteFilePost($deleteFileId)
{
    $pdo = pdoSqlConnect();
    $query = "update posts set status=2,updateAt=current_timestamp where id=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$deleteFileId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

    $st=null;$pdo = null;
}

function updateCheckIn($checkIn,$mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "update posts set pageId=?,updateAt=current_timestamp  where id=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$checkIn,$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

    $st=null;$pdo = null;

}
function updateEmotion($emotion,$mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "update posts set emotion=?,updateAt=current_timestamp  where id=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$emotion,$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

    $st=null;$pdo = null;
}
function updateIsOpen($isOpen,$mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "update posts set isOpen=?,updateAt=current_timestamp  where id=? or child=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$isOpen,$mainPostId,$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

    $st=null;$pdo = null;
}

function deletePost($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "update posts set status=2,updateAt=current_timestamp  where id=? or child =?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId,$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

    $st=null;$pdo = null;
}
