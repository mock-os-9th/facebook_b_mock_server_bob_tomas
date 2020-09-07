<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
        /*
         * API No. 0
         * API Name : 테스트 API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "mainFeed":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }


            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);

            if($_GET['offset']==0){
                $toReadPostId = getRecentPostId();
            }else {
                $toReadPostId = $_GET['offset'];
            }

            $cnt = 0;
            $postList = array();

            while($cnt < 5) {
                $post = (Object)Array();
                $writerAopen = getPostWriterAndOpen($toReadPostId);

                if(!getIsChild($toReadPostId)){
                    $toReadPostId = $toReadPostId-1;
                    continue;
                }

                if($data->userId==$writerAopen['userId']){
                    $post->mainPostId=$toReadPostId;

                    $post->headInfo=getPostInfo2($toReadPostId);
                    $post->mainContent=getMainContent2($toReadPostId); //메인 내용 조회
                    if(hasFiles($toReadPostId))
                    {
                        $post->files=getFiles($toReadPostId); //파일 경로, 내용
                    }
                    $post->userLikeThis=getUserLikeThis($toReadPostId,$data->userId); //좋아요 여부
                    $post->shareNum=getShareNum($toReadPostId); //공유 개수
                    $post->likeNum=getLikeNum($toReadPostId); //좋아요 개수
                    $post->replyNum=getReplyNum($toReadPostId); //댓글 수

                    array_push($postList, $post);

                    $toReadPostId = $toReadPostId-1;
                    $cnt = $cnt+1;
                    continue;
                }

                if($writerAopen['isOpen']==1&&$data->userId!=$writerAopen['userId']){
                    $post->mainPostId=$toReadPostId;

                    $post->headInfo=getPostInfo2($toReadPostId);
                    $post->mainContent=getMainContent2($toReadPostId); //메인 내용 조회
                    if(hasFiles($toReadPostId))
                    {
                        $post->files=getFiles($toReadPostId); //파일 경로, 내용
                    }
                    $post->userLikeThis=getUserLikeThis($toReadPostId,$data->userId); //좋아요 여부
                    $post->shareNum=getShareNum($toReadPostId); //공유 개수
                    $post->likeNum=getLikeNum($toReadPostId); //좋아요 개수
                    $post->replyNum=getReplyNum($toReadPostId); //댓글 수

                    array_push($postList, $post);

                    $toReadPostId = $toReadPostId-1;
                    $cnt = $cnt+1;
                    continue;
                }

                if($writerAopen['isOpen']==2&&$data->userId!=$writerAopen['userId']){
                    $isOpenChecked=CheckIsOpen2($writerAopen['userId'],$data->userId);
                    if($isOpenChecked[0]==1 || $isOpenChecked[1]==1)
                    {
                        $post->mainPostId=$toReadPostId;

                        $post->headInfo=getPostInfo2($toReadPostId);
                        $post->mainContent=getMainContent2($toReadPostId); //메인 내용 조회
                        if(hasFiles($toReadPostId))
                        {
                            $post->files=getFiles($toReadPostId); //파일 경로, 내용
                        }
                        $post->userLikeThis=getUserLikeThis($toReadPostId,$data->userId); //좋아요 여부
                        $post->shareNum=getShareNum($toReadPostId); //공유 개수
                        $post->likeNum=getLikeNum($toReadPostId); //좋아요 개수
                        $post->replyNum=getReplyNum($toReadPostId); //댓글 수

                        array_push($postList, $post);

                        $toReadPostId = $toReadPostId-1;
                        $cnt = $cnt+1;
                        continue;
                    }else{
                        $toReadPostId = $toReadPostId-1;
                        continue;
                    }
                }

                if($writerAopen['isOpen']==3&&$data->userId!=$writerAopen['userId']){
                    if(!CheckIsOpen3($toReadPostId,$data->userId))
                    {
                        $post->mainPostId=$toReadPostId;

                        $post->headInfo=getPostInfo2($toReadPostId);
                        $post->mainContent=getMainContent2($toReadPostId); //메인 내용 조회
                        if(hasFiles($toReadPostId))
                        {
                            $post->files=getFiles($toReadPostId); //파일 경로, 내용
                        }
                        $post->userLikeThis=getUserLikeThis($toReadPostId,$data->userId); //좋아요 여부
                        $post->shareNum=getShareNum($toReadPostId); //공유 개수
                        $post->likeNum=getLikeNum($toReadPostId); //좋아요 개수
                        $post->replyNum=getReplyNum($toReadPostId); //댓글 수

                        array_push($postList, $post);

                        $toReadPostId = $toReadPostId-1;
                        $cnt = $cnt+1;
                        continue;
                    }else{
                        $toReadPostId = $toReadPostId-1;
                        continue;
                    }
                }

                if($writerAopen['isOpen']==4&&$data->userId!=$writerAopen['userId']){
                    if(CheckIsOpen41($toReadPostId,$data->userId))
                    {
                        $post->mainPostId=$toReadPostId;

                        $post->headInfo=getPostInfo2($toReadPostId);
                        $post->mainContent=getMainContent2($toReadPostId); //메인 내용 조회
                        if(hasFiles($toReadPostId))
                        {
                            $post->files=getFiles($toReadPostId); //파일 경로, 내용
                        }
                        $post->userLikeThis=getUserLikeThis($toReadPostId,$data->userId); //좋아요 여부
                        $post->shareNum=getShareNum($toReadPostId); //공유 개수
                        $post->likeNum=getLikeNum($toReadPostId); //좋아요 개수
                        $post->replyNum=getReplyNum($toReadPostId); //댓글 수

                        array_push($postList, $post);

                        $toReadPostId = $toReadPostId-1;
                        $cnt = $cnt+1;
                        continue;
                    }else{
                        $toReadPostId = $toReadPostId-1;
                        continue;
                    }
                }

                if($writerAopen['isOpen']==5&&$data->userId==$writerAopen['userId']){
                    $post->mainPostId=$toReadPostId;

                    $post->headInfo=getPostInfo2($toReadPostId);
                    $post->mainContent=getMainContent2($toReadPostId); //메인 내용 조회
                    if(hasFiles($toReadPostId))
                    {
                        $post->files=getFiles($toReadPostId); //파일 경로, 내용
                    }
                    $post->userLikeThis=getUserLikeThis($toReadPostId,$data->userId); //좋아요 여부
                    $post->shareNum=getShareNum($toReadPostId); //공유 개수
                    $post->likeNum=getLikeNum($toReadPostId); //좋아요 개수
                    $post->replyNum=getReplyNum($toReadPostId); //댓글 수

                    array_push($postList, $post);

                    $toReadPostId = $toReadPostId-1;
                    $cnt = $cnt+1;
                    continue;
                }
            }
            $res->result = $postList;
            $res->offset = $toReadPostId;
            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "피드 조회 성공";
            echo json_encode($res);
            break;


        /*
        * API No. 7
        * API Name : 디테일페이지 API
        * 마지막 수정 날짜 : 20.09.01
        */
        case "myFeed":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }


            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);

            if($_GET['offset']==0){
                $toReadPostId = getRecentPostId();
            }else {
                $toReadPostId = $_GET['offset'];
            }

            $cnt = 0;
            $postList = array();

            while($cnt < 5) {
                $myPost = (Object)Array();
                $writerAopen = getPostWriterAndOpen($toReadPostId);

                if(!getIsChild($toReadPostId)){
                    $toReadPostId = $toReadPostId-1;
                    continue;
                }

                if($data->userId==$writerAopen['userId']){
                    $myPost->mainPostId=$toReadPostId;

                    $myPost->headInfo=getPostInfo2($toReadPostId);
                    $myPost->mainContent=getMainContent2($toReadPostId); //메인 내용 조회
                    if(hasFiles($toReadPostId))
                    {
                        $myPost->files=getFiles($toReadPostId); //파일 경로, 내용
                    }
                    $myPost->userLikeThis=getUserLikeThis($toReadPostId,$data->userId); //좋아요 여부
                    $myPost->shareNum=getShareNum($toReadPostId); //공유 개수
                    $myPost->likeNum=getLikeNum($toReadPostId); //좋아요 개수
                    $myPost->replyNum=getReplyNum($toReadPostId); //댓글 수

                    array_push($postList, $myPost);

                    $toReadPostId = $toReadPostId-1;
                    $cnt = $cnt+1;
                    continue;
                }
                $toReadPostId = $toReadPostId-1;
                if($toReadPostId){
                    $res->result = $postList;
                    $res->isSuccess = true;
                    $res->code = 105;
                    $res->message = "글을 모두 불러왔습니다.";
                    echo json_encode($res);
                    break;
                }
            }
            if(!is_null($res->isSuccess)){
                break;
            }
            $res->result = $postList;
            $res->offset = $toReadPostId;
            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "내 피드 조회 성공";
            echo json_encode($res);
            break;


    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}

