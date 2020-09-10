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
        /*
         * API No. 1
         * API Name : 친구 요청 보내기 API
         * 마지막 수정 날짜 : 20.09.04
         */
        case "requestFriend":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }


            $friendId = getFriendId($data->userId, $vars['reqFriIdx']);

            if(getUserStatus($data->userId)==2){
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "로그인 한 계정은 비활성된된 유저입니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if (!isExistUser($vars["reqFriIdx"])||(getUserStatus($vars["reqFriIdx"])==2)) {
                $res->isSuccess = FALSE;
                $res->code = 210;
                $res->message = "존재하지 않는 상대입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }
            if (isFriend($data->userId, $vars["reqFriIdx"])&&getFriendStatus($friendId)==1) {
                $res->isSuccess = FALSE;
                $res->code = 211;
                $res->message = "이미 친구상태입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }
            if (isValidRequest($data->userId, $vars["reqFriIdx"])&&getRequestStatus($data->userId, $vars["reqFriIdx"])==0) {
                $res->isSuccess = FALSE;
                $res->code = 212;
                $res->message = "이미 요청을 보냈습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }
            if(isValidRequest($data->userId, $vars["reqFriIdx"])&&getRequestStatus($data->userId, $vars["reqFriIdx"])==2){
                $res->isSuccess = FALSE;
                $res->code = 213;
                $res->message = "요청을 거절당했습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }
            if($data->userId==$vars["reqFriIdx"]){
                $res->isSuccess = FALSE;
                $res->code = 214;
                $res->message = "자신을 친구추가 할 수는 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }


            if(((getUser1Id($friendId)==$data->userId)&&(getFriendStatus($friendId)==2))||((getUser2Id($friendId)==$data->userId)&&(getFriendStatus($friendId)==3))){
                $res->isSuccess = FALSE;
                $res->code = 215;
                $res->message = "삭제하신 친구입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }
            if(isValidRequest($vars["reqFriIdx"], $data->userId)&&getRequestStatus($vars["reqFriIdx"], $data->userId)==0){
                $res->isSuccess = FALSE;
                $res->code = 216;
                $res->message = "상대에게서 친구요청을 받은 상태입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }




            requestFriend($data->userId, $vars["reqFriIdx"]);


            $username = getUserName($data->userId);
            $fcmToken = getFcmToken($vars["reqFriIdx"]);
            if(!getFCMStatus($fcmToken)) {
                sendFcm($fcmToken, $username . "님이 친구요청을 보냈습니다!", "게시물을 공유하시려면 친구수락을 눌러주세요!", FCM_SERVER_KEY);
            }

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "친구요청 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 2
         * API Name : 친구 요청 확인 페이지 API
         * 마지막 수정 날짜 : 20.09.04
         */
        case "requestFriendPage":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }
            http_response_code(200);
            $res->result = requestFriendPage($data->userId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "친구 요청 페이지 호출 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
        * API No. 3
        * API Name : 친구 요청 수락 API
        * 마지막 수정 날짜 : 20.09.04
        */
        case "responseFriendOk":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }
            if(!isExistRequest($req->requestId)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "존재하지 않는 친구요청.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            http_response_code(200);
            $result = updateRequestStatus(1,$req->requestId, $req->requestUserId, $data->userId);
//            setFriend($req->requestUserId, $data->userId);
            if(!is_null($result)){
                $res->isSuccess = $result['isSuccess'];
                $res->code = $result['code'];
                $res->message = $result['message'];

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "친구 수락 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
        * API No. 4
        * API Name : 친구 요청 거절 API
        * 마지막 수정 날짜 : 20.09.04
        */
        case "responseFriendNo":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }
            if(!isExistRequest($req->requestId)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "존재하지 않는 친구요청.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            http_response_code(200);
            updateRequestStatus(2, $req->requestId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "친구 거절 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 5
         * API Name : 친구 요청 취소 API
         * 마지막 수정 날짜 : 20.09.04
         */
        case "requestFriendCancel":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }
            http_response_code(200);
            if(!isExistRequest($req->requestId)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "존재하지 않는 친구요청.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(getReqStatus($req->requestId)!=0){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "요청중인 요청만 취소 가능.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            updateRequestStatus(-1, $req->requestId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "친구요청 취소 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
        * API No. 6
        * API Name : 친구 삭제 API
        * 마지막 수정 날짜 : 20.09.04
        */
        case "cancelFriend":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }
            http_response_code(200);

            if(!isFriend($data->userId, $vars["reqFriIdx"])){
                $res->isSuccess = FALSE;
                $res->code = 210;
                $res->message = "친구가 아닙니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $friendId = getFriendId($data->userId, $vars['reqFriIdx']);
            $deleter = whoIsDeleter($data->userId, $vars['reqFriIdx']);


            if(((getUser1Id($friendId)==$data->userId)&&(getFriendStatus($friendId)==2))||((getUser2Id($friendId)==$data->userId)&&(getFriendStatus($friendId)==3))){
                $res->isSuccess = FALSE;
                $res->code = 211;
                $res->message = "이미 삭제하신 친구입니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(((getUser2Id($friendId)==$data->userId)&&(getFriendStatus($friendId)==2))||((getUser1Id($friendId)==$data->userId)&&(getFriendStatus($friendId)==3))){
                $res->isSuccess = FALSE;
                $res->code = 212;
                $res->message = "이분에게 이미 삭제당하셨습니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }



            if($deleter=='user1Idisdeleter')
                updateFriendStatus(2, $friendId);
            else
                updateFriendStatus(3, $friendId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "친구 삭제 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
