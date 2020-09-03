<?php

function saveFile($files,$data,$req,$i)
{
    $sumSize=0;
    $maxSize=52428800;
    $ext_str = "pdf,jpg,gif,png,mp4";
    $ext_str_image = "pdf,jpg,gif,png";
    $ext_str_video = "mp4";

    $allowed_extensions = explode(',', $ext_str);

    $ext = substr($files['name'], strrpos($files['name'], '.') + 1);
    if(!in_array($ext, $allowed_extensions)) {
        return array(false, 200, "올바르지 않은 파일 확장자");
    }


    $fileSize = $files['size'][$i];
    $sumSize=$sumSize+$fileSize;
    if($sumSize>=$maxSize)
    {
        return array(false,201,"파일은 500MB 까지 업로드 할 수 있습니다");
    }


//        $fileType = $_FILES['upload']['type'][$f];

    if(is_array($ext,$ext_str_image))
    {
        $uploadBase = './photos';
        $name =$files['name'][$i];
        $uploadFile = $uploadBase.$name;

        if(move_uploaded_file($files['tmp_name'][$i], $uploadFile)){
            if($i==0)
            {
                $postContent=$req["content"][$i];
                $postIsOpen=$req["isOpen"][$i];
                $pdo = pdoSqlConnect();
                $query = "insert into posts ( userId,isOpen) value (?,?,?);";

                $st = $pdo->prepare($query);
                //    $st->execute([$param,$param]);
                $st->execute([$data->userId,$postContent,$postIsOpen]);

                $st=null;$pdo = null;$pdo = pdoSqlConnect();
            }

            savePhoto();
            savePostFile();
            echo 'success';
        }else{
            $res->isSuccess = false;
            $res->code = 202;
            $res->message = "파일 업로드 실패";
            echo json_encode($res);
            break;
        }
    }elseif (!is_array($ext,$ext_str_video))
    {
        $uploadBase = './videos';
        $name = $_FILES['upload']['name'][$f];
        $uploadName = explode('.', $name);
        $uploadname = time().$f.'.'.$uploadName[1];
        $uploadFile = $uploadBase.$uploadname;

        if(move_uploaded_file($_FILES['upload']['tmp_name'][$f], $uploadFile)){

            echo 'success';
        }else{
            $res->isSuccess = false;
            $res->code = 202;
            $res->message = "파일 업로드 실패";
            echo json_encode($res);
            break;
        }
    }else{
        $res=(object)Array();
        $res->isSuccess = false;
        $res->code = 203;
        $res->message = "올바르지 않은 파일 확장자";
        echo json_encode($res);
        break;
    }
}

function createPost($userId,$isOpen)
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

function putCheckIn($checkIn)
{

}

function putEmotion($emotion)
{

}

function putMainContent($mainContent)
{

}

function createPostWithFiles($userId,$mainPostId)
{

}

function savePostFiles($thisPostId,$saveFilesId)
{
    
}