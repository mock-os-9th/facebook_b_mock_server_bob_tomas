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
        case "createUser":
            http_response_code(200);

            $firstName = $req->firstName;
            $lastName = $req->lastName;
            $password = $req->password;
            $birth = $req->birth;
            $sex = $req->sex;

            $firstNameChecked = nameCheck($firstName);
            $lastNameChecked = nameCheck($lastName);

            $passwordChecked = passwordCheck($password);

            $birthDateChecked = birthDateCheck($birth);
            $sexChecked = sexCheck($sex);

            if(isPhoneOrEmail($req->sign)=="email") {
                $email = $req->sign;
                $phone = null;
                $emailChecked = emailCheck($email);
                if ($emailChecked[0] == false) {
                    $res->isSuccess = false;
                    $res->code = $emailChecked[1];
                    $res->message = $emailChecked[2];
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                } elseif (duplicatedEmail($email)) {
                    $res->isSuccess = false;
                    $res->code = 217;
                    $res->message = "중복된 이메일";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
            }elseif(isPhoneOrEmail($req->sign)=="phone")
            {
                $phone = $req->sign;
                $email = null;
                $phoneChecked = phoneCheck($phone);
                if ($phoneChecked[0] == false) {
                    $res->isSuccess = false;
                    $res->code = $phoneChecked[1];
                    $res->message = $phoneChecked[2];
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                } elseif (duplicatedPhone($phone)) {
                    $res->isSuccess = false;
                    $res->code = 218;
                    $res->message = "중복된 전화번호";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
            }else{
                $res->isSuccess = false;
                $res->code = 240;
                $res->message = "전화번호나 이메일이 유효하지 않습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if ($firstNameChecked[0] == false) {
                $res->isSuccess = false;
                $res->code = $firstNameChecked[1];
                $res->message = $firstNameChecked[2];
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            } elseif ($lastNameChecked[0] == false) {
                $res->isSuccess = false;
                $res->code = $lastNameChecked[1];
                $res->message = $lastNameChecked[2];
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            } elseif ($sexChecked[0] == false) {
                $res->isSuccess = false;
                $res->code = $sexChecked[1];
                $res->message = $sexChecked[2];
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            } elseif ($passwordChecked[0] == false) {
                $res->isSuccess = false;
                $res->code = $passwordChecked[1];
                $res->message = $passwordChecked[2];
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            } elseif ($birthDateChecked[0] == false) {
                $res->isSuccess = false;
                $res->code = $birthDateChecked[1];
                $res->message = $birthDateChecked[2];
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }  else {

                if(!is_null($email)) {
                    createUserWithEmail($lastName,$firstName,$birth,$sex,$email,$password);
                    $userId=getUserIdfromEmail($email);
                    $jwt = getJWTokenUserWithEmail($email, $password, $userId, JWT_SECRET_KEY);
                }elseif(!is_null($phone))
                {
                    createUserWithPhone($lastName,$firstName,$birth,$phone,$sex,$password);
                    $userId=getUserIdfromPhone($phone);
                    $jwt = getJWTokenUserWithPhone( $phone, $password, $userId, JWT_SECRET_KEY);
                }
                $res->jwt = $jwt;
                $res->isSuccess = TRUE;
                $res->code = 102;
                $res->message = "회원가입 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}

