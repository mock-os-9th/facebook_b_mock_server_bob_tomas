<?php
require 'function.php';

include_once('/var/www/html/test/facebook_b_mock_server_bob_tomas/vendor/phpmailer/phpmailer/src/PHPMailer.php');
include_once('/var/www/html/test/facebook_b_mock_server_bob_tomas/vendor/phpmailer/phpmailer/src/SMTP.php');
include_once('/var/www/html/test/facebook_b_mock_server_bob_tomas/vendor/phpmailer/phpmailer/src/Exception.php');
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
        case "findPassword":
            http_response_code(200);
            $sign=$req->sign;
            if(!isLoginSign($sign))
            {
                $res->isSuccess = false;
                $res->code = 200;
                $res->message = "유효하지 않은 이메일";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }


            if(isPhoneOrEmail($sign)=="email")
            {
                try{
                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    $mail->IsSMTP();
                    $mail->Host = "smtp.gmail.com";// email 보낼때 사용할 서버를 지정
//                    $mail->SMTPDebug=1;
                     $mail->SMTPAuth = true; // SMTP 인증을 사용함
                     $mail->Port = 465; //email 보낼때 사용할 포트를 지정
                     $mail->SMTPSecure = "ssl"; // SSL을 사용함
                     $mail->Username = "limms2000@gmail.com"; // Gmail 계정
                     $mail->Password = "sky01015*"; // 패스워드
                    $mail->CharSet = 'utf-8';
                    $mail->Encoding = "base64";
                    $mail->IsHTML(true);
                     $mail->SetFrom('limms2000@gmail.com', 'Facebook'); // 보내는 사람 email 주소와 표시될 이름 (표시될 이름은 생략가능)
                     $mail->AddAddress('limms1217@naver.com', 'YOU'); // 받을 사람 email 주소와 표시될 이름 (표시될 이름은 생략가능)
                     $mail->Subject = 'Email Subject'; // 메일 제목
                    $mail->Body = '비밀번호 찾기 링크입니다';
                    $mail->AltBody = '비밀번호 찾기 링크입니다';
                    $mail -> SMTPOptions = array(
                        "ssl" => array(
                            "verify_peer" => false
                        , "verify_peer_name" => false
                        , "allow_self_signed" => true
                        )
                    );

//                     $mail->MsgHTML(file_get_contents('contents.html')); // 메일 내용 (HTML 형식도 되고 그냥 일반 텍스트도 사용 가능함)
                     $mail->Send();
                }
                catch (phpmailerException $e)
                {
                    $res->isSuccess = false;
                    $res->code = 201;
                    $res->message = $e->ErrorInfo();
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }catch (Exception $e)
                {
                    $res->isSuccess = false;
                    $res->code = 201;
                    $res->message = $e->getMessage();
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
                    $res->isSuccess = true;
                    $res->code = 100;
                    $res->message = "메일 전송 완료";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;

            }
        case "createMailSchedule":
            http_response_code(200);
            $sign=$_POST["sign"];
            if(!isLoginSign($sign))
            {
                $res->isSuccess = false;
                $res->code = 200;
                $res->message = "유효하지 않은 이메일";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }


            if(isPhoneOrEmail($sign)=="email")
            {
                try{
                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    $mail->IsSMTP();
                    $mail->Host = "smtp.gmail.com";// email 보낼때 사용할 서버를 지정
//                    $mail->SMTPDebug=1;
                    $mail->SMTPAuth = true; // SMTP 인증을 사용함
                    $mail->Port = 465; //email 보낼때 사용할 포트를 지정
                    $mail->SMTPSecure = "ssl"; // SSL을 사용함
                    $mail->Username = "limms2000@gmail.com"; // Gmail 계정
                    $mail->Password = "sky01015*"; // 패스워드
                    $mail->CharSet = 'utf-8';
                    $mail->Encoding = "base64";
                    $mail->IsHTML(true);
                    $mail->SetFrom('limms2000@gmail.com', 'Facebook'); // 보내는 사람 email 주소와 표시될 이름 (표시될 이름은 생략가능)
                    $mail->AddAddress('limms1217@naver.com', 'YOU'); // 받을 사람 email 주소와 표시될 이름 (표시될 이름은 생략가능)
                    $mail->Subject = 'Email Subject'; // 메일 제목
                    $mail->Body = '메일 스케줄러 테스트입니다';
                    $mail->AltBody = '메일 스케줄러 테스트입니다';
                    $mail -> SMTPOptions = array(
                        "ssl" => array(
                            "verify_peer" => false
                        , "verify_peer_name" => false
                        , "allow_self_signed" => true
                        )
                    );

//                     $mail->MsgHTML(file_get_contents('contents.html')); // 메일 내용 (HTML 형식도 되고 그냥 일반 텍스트도 사용 가능함)
                    $mail->Send();
                }
                catch (phpmailerException $e)
                {
                    $res->isSuccess = false;
                    $res->code = 201;
                    $res->message = $e->ErrorInfo();
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }catch (Exception $e)
                {
                    $res->isSuccess = false;
                    $res->code = 201;
                    $res->message = $e->getMessage();
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
                $res->isSuccess = true;
                $res->code = 100;
                $res->message = "메일 스케줄 전송 완료";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;

            }


    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}

