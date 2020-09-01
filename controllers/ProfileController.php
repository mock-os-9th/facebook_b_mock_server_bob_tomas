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
         * API No. 1
         * API Name : 프로필 불러오기
         * 마지막 수정 날짜 : 20.08.31
         */
        case "getProfile":

            http_response_code(200);
            $res->result->profile = userProfile($vars["userIdx"]);
            $res->result->friendsCount = userProfileFriendsCount($vars["userIdx"]);
            $res->result->friends = userProfileFriends($vars["userIdx"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "프로필 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 1
         * API Name : 프로필 전체공개 수정페이지
         * 마지막 수정 날짜 : 20.08.31
         */
        case "openModifyPage":
            $num = 1;
            http_response_code(200);
            $res->result->openModifyPage = openModifyPage($num); // 수정필요
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "상세 프로필 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 2
         * API Name : 자기소개 수정
         * 마지막 수정 날짜 : 20.08.31
         */
        case "introduceModify":
            $num = 1;
            http_response_code(200);

            if(is_null($req->contents)||$req->contents==""){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "값을 입력해야 합니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(mb_strlen($req->contents, "UTF-8")>100){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "100자 이내로 입력해주세요.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            modifyIntroduce($req->contents,$num); // 수정필요
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "소개 수정 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

            
            /*
         * API No. 3
         * API Name : 자기소개 삭제
         * 마지막 수정 날짜 : 20.08.31
         */
        case "introduceDelete":
            $num = 1;
            http_response_code(200);

            if(is_null($req->contents)||$req->contents==""){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "삭제할 값이 없습니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            deleteIntroduce($num); // 수정필요
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "소개 수정 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 4
         * API Name : 취미 수정
         * 마지막 수정 날짜 : 20.08.31
         */
        case "modifyHobby":
            $num = 1;
            http_response_code(200);

            modifyHobby($req->contents,$num); // 수정필요
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "취미 수정 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
        * API No. 5
        * API Name : 친구목록(페이징)
        * 마지막 수정 날짜 : 20.09.01
        */
        case "getAllFriends":
            http_response_code(200);
            $num = 1;
            $offset = $_GET['offset']*50;

            if(userProfileFriendsCount($num)['friendsCount']<$offset){
                $res->isSuccess = TRUE;
                $res->code = 200;
                $res->message = "더 이상 친구가 없습니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;

            }

            $res->result = getAllFriends($num, $offset); // 수정필요
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "친구목록 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
        * API No. 5
        * API Name : 친구목록(페이징)
        * 마지막 수정 날짜 : 20.09.01
        */
        case "getMyDetailPage":
            http_response_code(200);
            $num = 1;

            $res->result = getMyDetailPage($num); // 수정필요
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "친구목록 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;




    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
