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
        case "createLike":
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
            $likeKind=$req->likeKind;
            $mainPostId=$vars['mainPostId'];
            if(isDeletedLike($mainPostId,$data->userId))
            {
                recreateLike($mainPostId,$data->userId,$likeKind);
                $res->isSuccess = true;
                $res->code = 100;
                $res->message = "좋아요 생성 완료";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(isDuplicatedLike($mainPostId,$data->userId))
            {
                $res->isSuccess = false;
                $res->code = 202;
                $res->message = "중복된 좋아요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(isDeletedPost($mainPostId))
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "삭제된 게시글 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            createLike($mainPostId,$data->userId,$likeKind);

            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "좋아요 생성 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "deleteLike":
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
            if(isDeletedLike($mainPostId,$data->userId))
            {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "이미 좋아요가 삭제되었습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(isDeletedPost($mainPostId))
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "삭제된 게시글 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            deleteLike($mainPostId,$data->userId);

            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "좋아요 삭제 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "createReplyLike":
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
            $mainReplyId=$vars['replyId'];
            if(isDeletedReplyLike($mainReplyId,$data->userId))
            {
                recreateReplyLike($mainReplyId,$data->userId);
                $res->isSuccess = true;
                $res->code = 100;
                $res->message = "댓글 좋아요 생성 완료";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(isDuplicatedReplyLike($mainReplyId,$data->userId))
            {
                $res->isSuccess = false;
                $res->code = 202;
                $res->message = "중복된 좋아요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(isDeletedReply($mainReplyId))
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "삭제된 댓글 입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            createReplyLike($mainReplyId,$data->userId);

            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "댓글 좋아요 생성 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "deleteReplyLike":
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
            $mainReplyId=$vars['replyId'];
            if(isDeletedReplyLike($mainReplyId,$data->userId))
            {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "이미 좋아요가 삭제되었습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(isDeletedReply($mainReplyId))
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "삭제된 게시글 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            deleteLike($mainPostId,$data->userId);

            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "댓글 좋아요 삭제 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}

