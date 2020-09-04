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
            $files = $_FILES['uploaded_file'];
            if(!isset($files))
            {
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "파일이 없습니다";
                echo json_encode($res);
                break;
            }
            $cnt=0;
            foreach($files['name'] as $key=>$value)
            {
                $cnt=$cnt+1;
            }
            if($cnt>80)
            {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "파일의 개수는 80개까지";
                echo json_encode($res);
                break;
            }
            http_response_code(200);
            $mainPostId = createMainPost($data->userId,$_POST["isOpen"]);
            if(isset($_POST["chekIn"]) && is_int($_POST["chekIn"]) && $_POST["chekIn"]>0)
            {
                putCheckIn($_POST["chekIn"],$mainPostId);
            }
            if(isset($_POST["emotion"]) && is_int($_POST["emotion"]) && $_POST["emotion"]>0)
            {
                putEmotion($_POST["emotion"],$mainPostId);
            }
            if (isset($_POST["mainContent"]))
            {
                putContent($_POST["mainContent"],$mainPostId);
            }
            $sumSize=0;
            $maxSize=52428800;
            $cnt=0;
            if(isset($files)) {
                foreach($files['name'] as $key=>$value)
                {
                    $cnt=$cnt+1;
                }
                for ($i = 0; $i < $cnt; $i++) {
                    $ext_str = "pdf,jpg,gif,png,mp4,jpeg";
                    $ext_str_image = "pdf,jpg,gif,png";
                    $ext_str_video = "mp4";

                    $allowed_extensions = explode(',', $ext_str);

                    $ext = substr($files['name'][$i], strrpos($files['name'][$i], '.') + 1);
                    if (!in_array($ext, $allowed_extensions)) {

                        $res->isSuccess = false;
                        $res->code = 200;
                        $res->message = "올바르지 않은 확장자";
                        echo json_encode($res);
                        break;
                    }

                }
                for ($i = 0; $i < $cnt; $i++) {

                    $fileSize = $files['size'][$i];
                    $sumSize = $sumSize + $fileSize;
                }
                if ($sumSize >= $maxSize) {
                    $res->isSuccess = FALSE;
                    $res->code = 204;
                    $res->message = "파일은 500MB 까지 업로드 할 수 있습니다";
                    echo json_encode($res);
                    break;
                }


                for ($i = 0; $i < $cnt; $i++) {
                    $saveFilesId = saveFile($files, $data, $i); //파일 저장 아이디
                    if ($saveFilesId[0] == false) {
                        $res->isSuccess = $saveFilesId[0];
                        $res->code = $saveFilesId[1];
                        $res->message = $saveFilesId[2];
                        echo json_encode($res);
                        break;
                    }
                    $thisPostId = createPostWithFiles($data->userId, $mainPostId);  //현재 포스트 아이디
                    if ($thisPostId[0] == false) {
                        $res->isSuccess = $thisPostId[0];
                        $res->code = $thisPostId[1];
                        $res->message = $thisPostId[2];
                        echo json_encode($res);
                        break;
                    }
                    if(isset($_POST["chekIn"]) && is_int($_POST["chekIn"]) && $_POST["chekIn"]>0)
                    {
                        putCheckIn($_POST["chekIn"],$thisPostId);
                    }
                    if(isset($_POST["emotion"]) && is_int($_POST["emotion"]) && $_POST["emotion"]>0)
                    {
                        putEmotion($_POST["emotion"],$thisPostId);
                    }
                    if (isset($_POST["photoContent"][$i]))
                    {
                        putContent($_POST["photoContent"][$i],$thisPostId);
                    }
                    savePostFiles($thisPostId,$saveFilesId,$files,$i);
                }
                $res->isSuccess = true;
                $res->code = 100;
                $res->message = "게시글 생성 완료";
                echo json_encode($res);
                break;
            }
            $res=(object)array();
            $res->isSuccess = FALSE;
            $res->code = 205;
            $res->message = "서버 오류";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 0
         * API Name : 테스트 Path Variable API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "testFiles":
            http_response_code(200);
            $files=$_FILES['files'];
            echo $files;
            break;

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

