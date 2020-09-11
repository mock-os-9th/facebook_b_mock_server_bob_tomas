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
            $isOpen=$_POST["isOpen"];
            $bannedUser=(int)array();
            $showedUser=(int)array();
            if(isset($_POST["bannedUser"]))
            {
                $bannedUser=$_POST["bannedUser"];
            }
            if(isset($_POST["showedUser"]))
            {
                $showedUser=$_POST["showedUser"];
            }
            updateIsOpen($isOpen,$mainPostId,$bannedUser,$showedUser);

            if(isset($_POST["checkIn"]))
            {
                putCheckIn($_POST["checkIn"],$mainPostId);
            }
            if(isset($_POST["emotion"]))
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
                    if (isset($_POST["photoContent"][$i]))
                    {
                        putContent($_POST["photoContent"][$i],$thisPostId);
                    }
                    savePostFiles($thisPostId,$saveFilesId,$files,$i);
                }
            }
            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "게시글 생성 완료";
            echo json_encode($res);
            break;

        case "updatePostOpen":
            http_response_code(200);
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
            $mainPostId=$vars["mainPostId"];

            if(!isPostWriter($mainPostId,$data->userId))  //
            {
                $res->isSuccess = FALSE;
                $res->code = 210;
                $res->message = "수정 권한이 없습니다";
                echo json_encode($res);
                break;
            }
            $isOpen=$req->isOpen;
            $bannedUser=(int)array();
            $showedUser=(int)array();
            if(isset($req->bannedUser))
            {
                $bannedUser=$req->bannedUser;
            }
            if(isset($req->showedUser))
            {
                $showedUser=$req->showedUser;
            }
            updateIsOpen($isOpen,$mainPostId,$bannedUser,$showedUser);

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "공개범위 변경 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : 테스트 Body & Insert API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "updatePost":

            http_response_code(200);
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
            $mainPostId=$vars["mainPostId"];
            if(!isPostWriter($mainPostId,$data->userId))  //
            {
                $res->isSuccess = FALSE;
                $res->code = 210;
                $res->message = "수정 권한이 없습니다";
                echo json_encode($res);
                break;
            }
            if(isset($_POST["changeFilePostId"]))
            {
                $cnt=0;
                $changeFilePostId=$_POST["changeFilePostId"];
                $changeFileComment=$_POST["changeFileComment"];
                foreach($_POST["changeFilePostId"] as $key=>$value)
                {
                    $cnt=$cnt+1;
                }
                for($i=0;$i<$cnt;$i++)
                {
                    updateContent($changeFileComment[$i],$changeFilePostId[$i]);
                }
            }

            if(isset($_POST["deleteFileId"]))
            {
                $deleteFileId=$_POST["deleteFileId"];
                $cnt=0;
                foreach($_POST["deleteFileId"] as $key=>$value)
                {
                    $cnt=$cnt+1;
                }
                for($i=0;$i<$cnt;$i++)
                {
                    deleteFilePost($deleteFileId[$i]);
                }
            }
            if(isset($_POST["isOpen"]))
            {
                $isOpen=$_POST["isOpen"];
                $bannedUser=(int)array();
                $showedUser=(int)array();
                if(isset($_POST["bannedUser"]))
                {
                    $bannedUser=$_POST["bannedUser"];
                }
                if(isset($_POST["showedUser"]))
                {
                    $showedUser=$_POST["showedUser"];
                }
                updateIsOpen($isOpen,$mainPostId,$bannedUser,$showedUser);
            }
            if(isset($_POST["checkIn"]))
            {
                updateCheckIn($_POST["checkIn"],$mainPostId);
            }
            if(isset($_POST["emotion"]))
            {
                updateEmotion($_POST["emotion"],$mainPostId);
            }
            if (isset($_POST["mainContent"]))
            {
                updateContent($_POST["mainContent"],$mainPostId);
            }

            $files = $_FILES['uploaded_file'];
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
                    if (isset($_POST["photoContent"][$i]))
                    {
                        putContent($_POST["photoContent"][$i],$thisPostId);
                    }
                    savePostFiles($thisPostId,$saveFilesId,$files,$i);
                }
            }
            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "게시글 수정 성공";
            echo json_encode($res);
            break;


        case "deletePost":
            http_response_code(200);
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
            $mainPostId=$vars["mainPostId"];

            if(!isPostWriter($mainPostId,$data->userId))  //
            {
                $res->isSuccess = FALSE;
                $res->code = 210;
                $res->message = "삭제 권한이 없습니다";
                echo json_encode($res);
                break;
            }
            deletePost($mainPostId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "게시글 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getPost":
            http_response_code(200);
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
            $mainPostId=$vars["mainPostId"];
            if(isDeletedPost($mainPostId))
            {
                $res->isSuccess = FALSE;
                $res->code = 299;
                $res->message = "삭제된 게시글 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $isOpen=getIsOpen($mainPostId);
            $writer=getWriter($mainPostId);
            if(isDeletedUser($writer))
            {
                $res->isSuccess = FALSE;
                $res->code = 299;
                $res->message = "삭제된 게시글 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if($isOpen==2)
            {
                $isOpenChecked=CheckIsOpen2($writer,$data->userId);
                if($isOpenChecked[0]==0 and $isOpenChecked[1]==0)
                {
                    $res->isSuccess = FALSE;
                    $res->code = 210;
                    $res->message = "조회 권한이 없습니다";
                    echo json_encode($res);
                    break;
                }
            }
            if($isOpen==3)
            {

                if(CheckIsOpen3($mainPostId,$data->userId))
                {
                    $res->isSuccess = FALSE;
                    $res->code = 210;
                    $res->message = "조회 권한이 없습니다";
                    echo json_encode($res);
                    break;
                }
            }
            if($isOpen==4)
            {

                if(!CheckIsOpen4($mainPostId,$data->userId))
                {
                    $res->isSuccess = FALSE;
                    $res->code = 210;
                    $res->message = "조회 권한이 없습니다";
                    echo json_encode($res);
                    break;
                }
            }
            if($isOpen==5)
            {
                if(!isPostWriter($mainPostId,$data->userId))  //
                {
                    $res->isSuccess = FALSE;
                    $res->code = 210;
                    $res->message = "조회 권한이 없습니다";
                    echo json_encode($res);
                    break;
                }
            }

            $res->mainPostId=$mainPostId; //메인포스트 인덱스
//            if($req->sharedPostId!=-1)
//            {
//                $sharedPostId=$req->sharedPostId;
//                $res->share=getSharedPost($sharedPostId); //공유된 게시글 인덱스, 작성자 인덱스, 프로필 사진 링크, 이름, 게시날짜, 내용, 파일 있으면 파일들 postId, 코멘트
//            }
            $res->headInfo=getWriterInfo((int)$mainPostId); //프로필 이름, 프로필 사진 링크
            $res->headInfo=getPostInfo($mainPostId); //게시 날짜, 공개 범위
            if(hasMainContent($mainPostId))
            {
                $res->mainContent=getMainContent($mainPostId); //메인 내용 조회
            }
            if(hasFiles($mainPostId))
            {
                $res->files=getFiles($mainPostId,$offset); //파일 경로, 내용
            }
            $res->userLikeThis=getUserLikeThis($mainPostId,$data->userId); //좋아요 여부
            $res->shareNum=getShareNum($mainPostId); //공유 개수
            $res->likeNum=getLikeNum($mainPostId); //좋아요 개수
            $res->replyNum=getReplyNum($mainPostId); //댓글 수
            $res->isSuccess = true;
            $res->code = 100;
            $res->message = "게시물 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            addErrorLogs($errorLogs, $res, $req);
            break;

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}

