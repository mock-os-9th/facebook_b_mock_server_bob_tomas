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
                    $jwt = getJWTokenUser($email, $password, $userId, JWT_SECRET_KEY);
                    saveLogin($email,$password);
                }elseif(!is_null($phone))
                {
                    createUserWithPhone($lastName,$firstName,$birth,$phone,$sex,$password);
                    $userId=getUserIdfromPhone($phone);
                    $jwt = getJWTokenUser( $phone, $password, $userId, JWT_SECRET_KEY);
                    saveLogin($phone,$password);
                }
                $res->jwt = $jwt;
                saveJWT($jwt,1);  //macAddress
                $res->isSuccess = TRUE;
                $res->code = 102;
                $res->message = "회원가입 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

        case "login":
            http_response_code(200);
            $password=$req->password;
            $passwordChecked=passwordCheck($password);
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
                } elseif (!isLoginSign($email)) {
                    $res->isSuccess = false;
                    $res->code = 217;
                    $res->message = "유효하지 않은 이메일";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
            }elseif(isPhoneOrEmail($req->sign)=="phone") {
                $phone = $req->sign;
                $email = null;
                $phoneChecked = phoneCheck($phone);
                if ($phoneChecked[0] == false) {
                    $res->isSuccess = false;
                    $res->code = $phoneChecked[1];
                    $res->message = $phoneChecked[2];
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                } elseif (!isLoginSign($phone)) {
                    $res->isSuccess = false;
                    $res->code = 218;
                    $res->message = "유효하지 않은 전화번호";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
            }
                if(!is_null($email)) {
                    if (!isValidUser($email,$password))
                    {
                        $res->isSuccess = false;
                        $res->code = 220;
                        $res->message = "비밀번호가 틀렸습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        break;
                    }
                    $userId=getUserIdfromEmail($email);
                    $jwt = getJWTokenUser($email, $password, $userId, JWT_SECRET_KEY);
                }elseif(!is_null($phone))
                {
                    if (!isValidUser($phone,$password))
                    {
                        $res->isSuccess = false;
                        $res->code = 220;
                        $res->message = "비밀번호가 틀렸습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        break;
                    }
                    $userId=getUserIdfromPhone($phone);
                    $jwt = getJWTokenUser( $phone, $password, $userId, JWT_SECRET_KEY);
                }
                $res->jwt= $jwt;
                saveJWT($jwt,1);  //macAddress
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "로그인 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;



        case "logout":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }

            http_response_code(200);
            if(!isJwtSaved($jwt,"1")) //macAddress
            {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "토큰이 이미 만료되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }else{
                deleteJwt($jwt,"1"); //macAddress
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "로그아웃 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }


        case "changePassword":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $wasPassword=$req->wasPassword;
            $newPassword=$req->newPassword;
            $rePassword=$req->rePassword;
            $newPasswordChecked=passwordCheck($newPassword);
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }elseif ($newPasswordChecked[0]==false)
            {
                $res->isSuccess = FALSE;
                $res->code = $newPasswordChecked[1];
                $res->message = $newPasswordChecked[2];
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }elseif($wasPassword!=$data->password)
            {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "이전 비밀번호가 일치하지 않습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }elseif($newPassword!=$rePassword)
            {
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "확인용 비밀번호가 일치하지 않습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }else{
                http_response_code(200);

                changePassword($newPassword,$data->userId);
                changeLoginTable($newPassword,$data->sign);
                $newJwt=getJWTokenUser($data->sign,$newPassword,$data->userId,JWT_SECRET_KEY);
                changeJWT($newJwt,$jwt,"1"); //macAddress
                $res->newJwt = $newJwt;
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "비밀번호 변경 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
        case "deleteUser":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY) || !isJwtSaved($jwt,1)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res);
                addErrorLogs($errorLogs, $res, $req);
                break;
            }
            $data=getDataByJWToken($jwt,JWT_SECRET_KEY);
            deleteUser($data->userId,$jwt,$data->sign);
            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "계정 삭제 성공";
            echo json_encode($res);
            break;


    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}

