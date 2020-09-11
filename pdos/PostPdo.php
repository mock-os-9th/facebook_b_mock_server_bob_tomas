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

function putCheckIn($checkIn,$mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "update  posts set pageId=?, updateAt=current_timestamp where id=?;";

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
    $query = "update  posts set emotion=?,updateAt=current_timestamp where id=?;";

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
    $query = "update  posts set content=?,updateAt=current_timestamp where id=?;";

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
function updateIsOpen($isOpen,$mainPostId,$bannedUser,$showedUser)
{
    if($isOpen==1)
    {
        $pdo = pdoSqlConnect();
        $query = "update posts set isOpen=?,updateAt=current_timestamp  where id=? or child=?;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$isOpen,$mainPostId,$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;

        $query = "update postShowed set status=2,updateAt=current_timestamp  where postId=? ;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;

        $query = "update postBanned set status=2,updateAt=current_timestamp  where postId=? ;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;
        $pdo = null;
    }
    else if($isOpen==2)
    {
        $pdo = pdoSqlConnect();
        $query = "update posts set isOpen=?,updateAt=current_timestamp  where id=? or child=?;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$isOpen,$mainPostId,$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;
        $query = "update postShowed set status=2,updateAt=current_timestamp  where postId=? ;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;

        $query = "update postBanned set status=2,updateAt=current_timestamp  where postId=? ;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;
        $pdo = null;

    }
    else if($isOpen==3)
    {
        $pdo = pdoSqlConnect();
        $query = "update posts set isOpen=?,updateAt=current_timestamp  where id=? or child=?;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$isOpen,$mainPostId,$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;
        $query = "update postBanned set status=2,updateAt=current_timestamp  where postId=? ;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;
        for($i=0;$i<count($bannedUser);$i++)
        {
            $query = "insert into postBanned (postId,userId) value(?,?);";

            $st = $pdo->prepare($query);
            //    $st->execute([$param,$param]);
            $st->execute([$mainPostId,$bannedUser[$i]]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

            $st=null;
        }

        $query = "update postShowed set status=2,updateAt=current_timestamp  where postId=? ;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;
        $pdo = null;
    }
    else if((int)$isOpen==4)
    {
        $pdo = pdoSqlConnect();
        $query = "update posts set isOpen=?,updateAt=current_timestamp  where id=? or child=?;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$isOpen,$mainPostId,$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;

        $query = "update postShowed set status=2,updateAt=current_timestamp  where postId=? ;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;
        for($i=0; $i<count($showedUser); $i++)
        {
            $query = "insert into postShowed (postId,userId) value(?,?);";

            $st = $pdo->prepare($query);
            //    $st->execute([$param,$param]);
            $st->execute([$mainPostId,$showedUser[$i]]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
            $st=null;
        }

        $query = "update postBanned set status=2,updateAt=current_timestamp  where postId=? ;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;
        $pdo = null;
    }else{
        $pdo = pdoSqlConnect();
        $query = "update posts set isOpen=5,updateAt=current_timestamp  where id=? or child=?;";

        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->execute([$mainPostId,$mainPostId]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();

        $st=null;$pdo = null;
    }
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
function CheckIsOpen2($writer,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "
select exists(select * from friends where user1Id=? and user2Id=? and status=1) as exist1, 
       exists(select * from friends where user2Id=? and user1Id=? and status=1) as exist2;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$writer,$userId,$writer,$userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return array($res[0]["exist1"],$res[0]["exist2"]);


}

function CheckIsOpen3($mainPostId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from postBanned where postId=? and userId=?) as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId,$userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["exist"];
}

function CheckIsOpen4($mainPostId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from postShowed where postId=? and userId=?) as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId,$userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["exist"];
}

function getIsOpen($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select isOpen from posts where id =?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["isOpen"];
}

function getWriter($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select userId from posts where id =?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["userId"];
}

function getWriterInfo($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select users.id,CONCAT(firstName, lastName) name,
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
                            LIMIT 1), '프로필사진없음') profilePhoto
from (select userId,id,status from posts) as posts

join users
on users.id = posts.userId

WHERE posts.id=?
and posts.status=1;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res;
}

function getPostInfo($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select (
    case
               when timestampdiff(DAY, getAt, now()) > 7
                   then time_format(getAt, '%Y년 %m월 %d일')
               when timestampdiff(DAY, getAt, now()) >= 1 and timestampdiff(DAY, now(), getAt) <= 7
                   then concat(timestampdiff(DAY, getAt, now()), '일 전')
               when timestampdiff(HOUR, getAt, now()) <= 24 and timestampdiff(HOUR, now(), getAt) >= 1
                   then concat(timestampdiff(HOUR, getAt, now()), '시간 전')
               when timestampdiff(MINUTE, getAt, now()) <= 60 and timestampdiff(MINUTE, now(), getAt) >= 1
                   then concat(timestampdiff(MINUTE, getAt, now()), '분 전')
               else '방금전' end
    ) getAt,
       isOpen

from posts
where posts.id=?;
";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res;
}

function hasMainContent($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from posts where id=? and !ISNULL(NULLIF(content,''))) as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["exist"];
}

function getMainContent($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select content from posts where id=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["content"];
}

function hasFiles($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from postFiles where postId=c.id and status=1) as exist
from
(select id from posts where child = ?) as c;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    for($i=0;$i<count($res);$i++)
    {
        if($res[$i]["exist"]==1)
        {
            return $res[$i]["exist"];
        }
    }
    return 0;
}

function getFiles($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select if(!ISNULL(NULLIF(content,'')),content,null) content,
       if(postFiles.videoId!=-1,(select video
from videos where postFiles.videoId=videos.id),null) video,
       if(postFiles.imageId!=-1,(select image
from photos where postFiles.imageId=photos.id),null) image
from
    (select id,content from posts where child = ?) as c

join postFiles
on postFiles.postId=c.id

where postFiles.status=1
;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res;
}

function getUserLikeThis($mainPostId,$userId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from postLike where postId=? and userId=? and status=1) as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId,$userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["exist"];
}

function getShareNum($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT count(*) as count
from posts
where posts.status=1
group by sharedPostId
having sharedPostId=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["count"];
}

function getLikeNum($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT count(*) as count
from postLike
where postLike.status=1
group by postId
having postId=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["count"];
}

function getReply($mainPostId,$offset)
{
    $pdo = pdoSqlConnect();
    $query = "select users.id as userId,
       CONCAT(firstName, lastName) name,
       IF(EXISTS(SELECT *
                 FROM profileImage P
                 WHERE reply.userId=P.userId
                 LIMIT 1),(select P.image
                            from photos P
                            join profileImage F
                              ON P.id = F.photoId
                            where reply.userId=P.userId
                            ORDER BY F.getAt DESC
                            LIMIT 1), '프로필사진없음') profilePhoto,
       reply.id,
       reply.content,
       (SELECT count(*) as count
from replyLike
group by replyLike.replyId
having reply.id=replyId
and reply.status=1) replyLikeCount,
       (SELECT count(*) as count
from reply
group by reply.isReply
having reply.id=isReply
and reply.status=1) replyCount,
       reply.isReply,
       if(replyFiles.imageId!=-1,(select image from photos where replyFiles.imageId=photos.id),null) image,
       if(replyFiles.videoId!=-1,(select video from videos where replyFiles.videoId=videos.id),null) video,
    (case
               when timestampdiff(DAY, reply.getAt, now()) > 7
                   then time_format(reply.getAt, '%Y년 %m월 %d일')
               when timestampdiff(DAY, reply.getAt, now()) >= 1 and timestampdiff(DAY, now(), reply.getAt) <= 7
                   then concat(timestampdiff(DAY, reply.getAt, now()), '일 전')
               when timestampdiff(HOUR, reply.getAt, now()) >= 24 and timestampdiff(HOUR, now(), reply.getAt) <= 1
                   then concat(timestampdiff(HOUR, reply.getAt, now()), '시간 전')
               when timestampdiff(MINUTE, reply.getAt, now()) <= 60 and timestampdiff(MINUTE, now(), reply.getAt) >= 1
                   then concat(timestampdiff(MINUTE, reply.getAt, now()), '분 전')
               else '방금전' end) getAt

from (select id from posts) as posts,reply

LEFT JOIN replyFiles
on replyFiles.replyId=reply.id
join users
on users.id = reply.userId

WHERE posts.id=?
and reply.postId=?
and reply.status=1
and reply.isReply=-1
and users.status=1

order by reply.getAt DESC
limit 10 offset $offset
;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId,$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res;
}

function isDeletedPost($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from posts where posts.status!=1 and posts.id=?) as exist";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["exist"];
}

function getReplyNum($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT count(*) as count
from reply
where reply.status=1
group by postId
having postId=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["count"];
}

function getReReply($mainPostId)
{
    $pdo = pdoSqlConnect();
    $query = "select users.id as userId,
       CONCAT(firstName, lastName) name,
       IF(EXISTS(SELECT *
                 FROM profileImage P
                 WHERE reply.userId=P.userId
                 LIMIT 1),(select P.image
                            from photos P
                            join profileImage F
                              ON P.id = F.photoId
                            where reply.userId=P.userId
                            ORDER BY F.getAt DESC
                            LIMIT 1), '프로필사진없음') profilePhoto,
       reply.id,
       reply.content,
       (SELECT count(*) as count
from replyLike
group by replyLike.replyId
having reply.id=replyId
and reply.status=1) replyLikeCount,
       reply.isReply,
       if(replyFiles.imageId!=-1,(select image from photos where replyFiles.imageId=photos.id),null) image,
       if(replyFiles.videoId!=-1,(select video from videos where replyFiles.videoId=videos.id),null) video,
    (case
               when timestampdiff(DAY, reply.getAt, now()) > 7
                   then time_format(reply.getAt, '%Y년 %m월 %d일')
               when timestampdiff(DAY, reply.getAt, now()) >= 1 and timestampdiff(DAY, now(), reply.getAt) <= 7
                   then concat(timestampdiff(DAY, reply.getAt, now()), '일 전')
               when timestampdiff(HOUR, reply.getAt, now()) <= 24 and timestampdiff(HOUR, now(), reply.getAt) >= 1
                   then concat(timestampdiff(HOUR, reply.getAt, now()), '시간 전')
               when timestampdiff(MINUTE, reply.getAt, now()) <= 60 and timestampdiff(MINUTE, now(), reply.getAt) >= 1
                   then concat(timestampdiff(MINUTE, reply.getAt, now()), '분 전')
               else '방금전' end) getAt

from (select id from posts) as posts,reply

LEFT JOIN replyFiles
on replyFiles.replyId=reply.id
join users
on users.id = reply.userId

WHERE posts.id=?
and reply.postId=?
and reply.status=1
and reply.isReply!=-1
and users.status=1

order by reply.getAt DESC
;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$mainPostId,$mainPostId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res;
}

function isDeletedUser($writer)
{

    $pdo = pdoSqlConnect();
    $query = "select exists(select * from users where id=? and status!=1) as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$writer]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res[0]["exist"];
}