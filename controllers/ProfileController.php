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

            if(!isValidUserid($vars["userIdx"])){
                $res->isSuccess = FALSE;
                $res->code = 210;
                $res->message = "없는 계정입니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(getUserStatus($data->userId)==2) {
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "로그인 한 계정은 비활성된된 유저입니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(getUserStatus($vars["userIdx"])==2){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "이 프로필은 비활성된된 유저입니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result->profile = userProfile($vars["userIdx"], $data->userId);
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

            if(getUserStatus($data->userId)==2){
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "로그인 한 계정은 비활성된된 유저입니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            http_response_code(200);
            $res->result = openModifyPage($data->userId);
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

            if(is_null($req->contents)||$req->contents==""){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "값을 입력해야 합니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(mb_strlen($req->contents, "UTF-8")>100){
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "100자 이내로 입력해주세요.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            modifyIntroduce($req->contents,$data->userId); // 수정필요
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
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            http_response_code(200);

            if(getUserStatus($data->userId)==2){
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "로그인 한 계정은 비활성된된 유저입니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }


            deleteIntroduce($data->userId); // 수정필요
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "소개 삭제 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 4
         * API Name : 취미 수정
         * 마지막 수정 날짜 : 20.08.31
         */
        case "modifyHobby":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            http_response_code(200);

            if(getUserStatus($data->userId)==2){
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "로그인 한 계정은 비활성된된 유저입니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            modifyHobby($req->contents,$data->userId); // 수정필요
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
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $offset = $_GET['offset']*50;

            if(getUserStatus($data->userId)==2){
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "로그인 한 계정은 비활성된된 유저입니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(getUserStatus($vars["userIdx"])==2){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "이 프로필은 비활성된된 유저입니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(userProfileFriendsCount($vars['userIdx'])['friendsCount']<$offset){
                $res->isSuccess = TRUE;
                $res->code = 210;
                $res->message = "더 이상 친구가 없습니다.";

                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;

            }

            $res->result = getAllFriends($vars['userIdx'], $offset);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "친구목록 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 6
        * API Name : 친구 검색
        * 마지막 수정 날짜 : 20.09.01
        */
        case "getSearchedFriend":
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

            $res->result = getSearchedFriend($vars['userIdx'], $_GET['search']);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "친구 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
        * API No. 7
        * API Name : 디테일페이지 API
        * 마지막 수정 날짜 : 20.09.01
        */
        case "getMyDetailPage":
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

            $res->result->work = getMyDetailWork($data->userId); // 수정필요
            $res->result->School = getMyDetailSchool($data->userId); // 수정필요
            $res->result->info = getMyDetail($data->userId); // 수정필요
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "상세정보 페이지 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        case "insertProfileImage":
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

            $name = $_POST['name'];

            $file_path = './photos/' . $name . '.jpg';
            $file_calling = '3.35.3.242/photos/' . $name . '.jpg';
            move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path);
            if (isValidFileName($file_calling)) {
                $res->isSuccess = FALSE;
                $res->code = 206;
                $res->message = "파일이름이 중복입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            postImgUrl($data->userId, $file_calling);
            $photoId = getPhotoId($file_calling);
            setProfileImage($data->userId, $photoId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "프로필사진 등록 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "insertCoverImage":
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

            $name = $_POST['name'];

            $file_path = './photos/' . $name . '.jpg';
            $file_calling = '3.35.3.242/photos/' . $name . '.jpg';
            move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path);


            if (isValidFileName($file_calling)) {
                $res->isSuccess = FALSE;
                $res->code = 206;
                $res->message = "파일이름이 중복입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            postImgUrl($data->userId, $file_calling);
            $photoId = getPhotoId($file_calling);
            setCoverImage($data->userId, $photoId);

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "커버사진 등록 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
