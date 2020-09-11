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
        case "getReply":
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

            $mainPostId=$vars['mainPostId'];
            $offset=$_GET['offset']*10;

            $writer=getWriter($mainPostId);
            $isOpen=getIsOpen($mainPostId);
            if(isDeletedPost($mainPostId))
            {
                $res->isSuccess = FALSE;
                $res->code = 299;
                $res->message = "삭제된 게시글 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(isDeletedUser($writer))
            {
                $res->isSuccess = FALSE;
                $res->code = 299;
                $res->message = "삭제된 게시글 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if($isOpen==2)
            {
                $isOpenChecked=CheckIsOpen2($writer,$data->userId);
                if($isOpenChecked[0]==0 and $isOpenChecked[1]==0)
                {
                    $res->isSuccess = FALSE;
                    $res->code = 210;
                    $res->message = "조회 권한이 없습니다";
                    echo json_encode($res);
                    break;
                }
            }
            if($isOpen==3)
            {

                if(CheckIsOpen3($mainPostId,$data->userId))
                {
                    $res->isSuccess = FALSE;
                    $res->code = 210;
                    $res->message = "조회 권한이 없습니다";
                    echo json_encode($res);
                    break;
                }
            }
            if($isOpen==4)
            {

                if(!CheckIsOpen4($mainPostId,$data->userId))
                {
                    $res->isSuccess = FALSE;
                    $res->code = 210;
                    $res->message = "조회 권한이 없습니다";
                    echo json_encode($res);
                    break;
                }
            }
            if($isOpen==5)
            {
                if(!isPostWriter($mainPostId,$data->userId))  //
                {
                    $res->isSuccess = FALSE;
                    $res->code = 210;
                    $res->message = "조회 권한이 없습니다";
                    echo json_encode($res);
                    break;
                }
            }

            $res->reply = getReply($mainPostId,$offset); //댓글 조회(10개씩 페이징), 대댓글은 (3개 페이징 한번 후 나머지는 전체 출력), 메인 댓글 당 대댓글 수
            $res->rereply = getReReply($mainPostId); //대댓글 조회
            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "댓글 조회 완료";
            echo json_encode($res);
            addErrorLogs($errorLogs, $res, $req);
            break;

        case "createReply":
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
            $mainPostId=$vars['mainPostId'];
            $file = $_FILES['uploaded_file'];
            if(isDeletedPost($mainPostId))
            {
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "삭제된 게시글 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isset($_POST['replyContent']) && !isset($_FILES['uploaded_file']))
            {
                $res->isSuccess = false;
                $res->code = 202;
                $res->message = "입력 값이 존재하지 않습니다";
                echo json_encode($res);
                break;
            }

            $thisReplyId=createReply($mainPostId,$data->userId);
            if(isset($file))
            {
                $ext_str = "pdf,jpg,gif,png,mp4,jpeg";
                $ext_str_image = "pdf,jpg,gif,png";
                $ext_str_video = "mp4";
                $allowed_extensions = explode(',', $ext_str);

                $ext = substr($file['name'], strrpos($file['name'], '.') + 1);
                if (!in_array($ext, $allowed_extensions)) {

                    $res->isSuccess = false;
                    $res->code = 200;
                    $res->message = "올바르지 않은 확장자";
                    echo json_encode($res);
                    break;
                }
                $saveFilesId = saveFile1($file, $data);
                saveReplyFile($thisReplyId,$saveFilesId,$file);
            }

            if (isset($_POST["replyContent"]))
            {
                $replyContent=$_POST['replyContent'];
                putReplyContent($_POST["replyContent"],$thisReplyId);
            }


            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "댓글 생성 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            addErrorLogs($errorLogs, $res, $req);
            break;

        case "createReReply":
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
            $mainPostId=$vars['mainPostId'];
            $mainReplyId=$_POST['mainReplyId'];
            $file = $_FILES['uploaded_file'];
            if(isDeletedPost($mainPostId))
            {
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "삭제된 게시글 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isset($mainReplyId))
            {
                $res->isSuccess = false;
                $res->code = 204;
                $res->message = "메인 댓글 인덱스가 존재하지 않습니다";
                echo json_encode($res);
                break;
            }
            if(!isset($_POST['replyContent']) && !isset($_FILES['uploaded_file']))
            {
                $res->isSuccess = false;
                $res->code = 202;
                $res->message = "입력 값이 존재하지 않습니다";
                echo json_encode($res);
                break;
            }

            $thisReplyId=createReReply($mainPostId,$data->userId,$mainReplyId);
            if(isset($file))
            {
                $ext_str = "pdf,jpg,gif,png,mp4,jpeg";
                $ext_str_image = "pdf,jpg,gif,png";
                $ext_str_video = "mp4";
                $allowed_extensions = explode(',', $ext_str);

                $ext = substr($file['name'], strrpos($file['name'], '.') + 1);
                if (!in_array($ext, $allowed_extensions)) {

                    $res->isSuccess = false;
                    $res->code = 200;
                    $res->message = "올바르지 않은 확장자";
                    echo json_encode($res);
                    break;
                }
                $saveFilesId = saveFile1($file, $data);
                saveReplyFile($thisReplyId,$saveFilesId,$file);
            }

            if (isset($_POST["replyContent"]))
            {
                $replyContent=$_POST['replyContent'];
                putReplyContent($_POST["replyContent"],$thisReplyId);
            }


            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "대댓글 생성 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            addErrorLogs($errorLogs, $res, $req);
            break;

        case "deleteReply":
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

            $postId=$vars['mainPostId'];
            if(isDeletedPost($postId))
            {
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "삭제된 게시글 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $replyId=$vars['replyId'];
            if(!isset($postId))
            {
                $res->isSuccess = false;
                $res->code = 202;
                $res->message = "메인 포스트 인덱스가 존재하지 않습니다";
                echo json_encode($res);
                break;
            }
            if(!isset($replyId))
            {
                $res->isSuccess = false;
                $res->code = 205;
                $res->message = "메인 댓글 인덱스가 존재하지 않습니다";
                echo json_encode($res);
                break;
            }
            if(!isReplyWriter($replyId,$data))
            {
                $res->isSuccess = false;
                $res->code = 204;
                $res->message = "삭제 권한이 존재하지 않습니다";
                echo json_encode($res);
                break;
            }
            deleteReply($replyId);
            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "댓글 삭제 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            addErrorLogs($errorLogs, $res, $req);
            break;

        case "updateReply":
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
            $postId=$vars['mainPostId'];
            if(isDeletedPost($postId))
            {
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "삭제된 게시글 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $replyId=$vars['replyId'];
            $changeContent=$req->content;
            if(!isset($postId))
            {
                $res->isSuccess = false;
                $res->code = 202;
                $res->message = "메인 포스트 인덱스가 존재하지 않습니다";
                echo json_encode($res);
                break;
            }
            if(!isset($replyId))
            {
                $res->isSuccess = false;
                $res->code = 205;
                $res->message = "메인 댓글 인덱스가 존재하지 않습니다";
                echo json_encode($res);
                break;
            }
            if(!isReplyWriter($replyId,$data))
            {
                $res->isSuccess = false;
                $res->code = 204;
                $res->message = "수정 권한이 존재하지 않습니다";
                echo json_encode($res);
                break;
            }
            updateReply($changeContent,$replyId);
            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "댓글 수정 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            addErrorLogs($errorLogs, $res, $req);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}

