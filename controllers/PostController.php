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
        case "createPost":
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
            $files = $_FILES['upload'];
            if(count($files["name"])>80)
            {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "파일의 개수는 80개까지";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            http_response_code(200);
            $mainPostId = createPost($data->userId,$req->isOpen);
            if(isset($req->chekIn) && is_int($req->chekIn) && $req->chekIn>0)
            {
                putCheckIn($req->checkIn);
            }
            if(isset($req->emotion) && is_int($req->emotion) && $req->emotion>0)
            {
                putEmotion($req->emotion);
            }
            if (isset($req->mainContent))
            {
                putMainContent($req->mainContent);
            }

            if(isset($_FILES['upload']) && $_FILES['upload']['name'] != "") {

                for($i=0;$i<count($files["name"]);$i++)
                {
                    $saveFilesId=saveFile($files,$data,$req,$i); //파일 저장 아이디
                    if($saveFilesId[0]==false)
                    {
                        $res->isSuccess = $saveFilesId[0];
                        $res->code = $saveFilesId[1];
                        $res->message = $saveFilesId[2];
                        break;
                    }
                    $thisPostId=createPostWithFiles($data->userId,$mainPostId);  //현재 포스트 아이디
                    if($thisPostId[0]==false)
                    {
                        $res->isSuccess = $thisPostId[0];
                        $res->code = $thisPostId[1];
                        $res->message = $thisPostId[2];
                        break;
                    }
                    savePostFiles($thisPostId,$saveFilesId);
                }
            }
        /*
         * API No. 0
         * API Name : 테스트 Path Variable API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "updatePost":
            http_response_code(200);
            $res->result = testDetail($vars["testNo"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : 테스트 Body & Insert API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "updatePostOpen":
            http_response_code(200);
            $res->result = testPost($req->name);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "deletePost":
            http_response_code(200);
            $res->result = testPost($req->name);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
