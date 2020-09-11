<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";
const FCM_SERVER_KEY = "AAAA3uZy8fA:APA91bERlHwI8IpRKNIFgT-bpZQokUJEy-GT8TGtP_9Dht4FZYq6dVTjgOdP0YuS6bTmWdr_WjTf5-m7-XqqSAYV-03ha34CiOIbWJPsDngjQJLC-Uyi0G-3HOQCFckwPtGygdC_OMR8";

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
        * API Name : FCM토큰등록
        * 마지막 수정 날짜 : 20.09.01
        */
        case "postDevice":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }


            if(getUserStatus($data->userId)==2){
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "로그인 한 계정은 비활성된된 유저입니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $tokenExist = isExistDevice($req->deviceToken, $data->userId);
            $tokenStatus = getTokenStatus($req->deviceToken, $data->userId);

            if($tokenExist&&$tokenStatus=0){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "등록된 유저";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($tokenExist&&$tokenStatus=1){
                updateStatus($req->deviceToken, $data->userId);
                $res->isSuccess = TRUE;
                $res->code = 105;
                $res->message = "활성 완료.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            postDeviceUser($req->deviceToken, $data->userId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "디바이스 유저 등록 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
        * API No. 0
        * API Name : FCM토큰등록
        * 마지막 수정 날짜 : 20.09.01
        */
        case "deviceDisabled":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }


            if(getUserStatus($data->userId)==2){
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "로그인 한 계정은 비활성된된 유저입니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $tokenExist = isExistDevice($req->deviceToken, $data->userId);
            $tokenStatus = getTokenStatus($req->deviceToken, $data->userId);

            if(!$tokenExist){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "해당 기기는 등록되어 있지 않습니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($tokenStatus==1){
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "이미 비활성화 되었습니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            deviceDisabled($req->deviceToken, $data->userId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "디바이스 유저 비활성";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;



    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}

