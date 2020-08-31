<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 0
         * API Name : 프로필 불러오기
         * 마지막 수정 날짜 : 20.08.31
         */
        case "getProfile":

//            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
//                $res->isSuccess = FALSE;
//                $res->code = 201;
//                $res->message = "유효하지 않은 토큰입니다";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                addErrorLogs($errorLogs, $res, $req);
//                return;
//            }

            http_response_code(200);
            $res->result->profile = userProfile($vars["userIdx"]);
            $res->result->friendsCount = userProfileFriendsCount($vars["userIdx"]);
            $res->result->friends = userProfileFriends($vars["userIdx"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "프로필 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
