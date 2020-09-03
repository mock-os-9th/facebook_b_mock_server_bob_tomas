<?php
require './pdos/DatabasePdo.php';
require './pdos/IndexPdo.php';
require './pdos/UserPdo.php';
require './pdos/PostPdo.php';
require './pdos/profilePdo.php';
require './pdos/PostPdo.php';
require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');

//에러출력하게 하는 코드
error_reporting(E_ALL); ini_set("display_errors", 1);

//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* ******************   Test   ****************** */
    $r->addRoute('GET', '/', ['IndexController', 'index']);

    $r->addRoute('POST', '/user', ['UserController', 'createUser']); //회원가입
    $r->addRoute('POST', '/login', ['UserController', 'login']); //로그인
    $r->addRoute('DELETE', '/logout', ['UserController', 'logout']); //로그인 정보 삭제, 로그아웃(토큰 무력화)
    $r->addRoute('PUT', '/change-password', ['UserController', 'changePassword']); //비밀번호 변경
//    $r->addRoute('GET', '/find-password', ['UserController', 'findPassword']); //비밀번호 찾기
    $r->addRoute('DELETE', '/user', ['UserController', 'deleteUser']); //유저 탈퇴


    $r->addRoute('POST', '/post', ['PostController', 'createPost']); //게시글 생성
    $r->addRoute('PUT', '/post', ['PostController', 'updatePost']); //게시글 수정
    $r->addRoute('PUT', '/post-open', ['PostController', 'updatePostOpen']); //게시글 공개범위 생성
    $r->addRoute('DELETE', '/post/{postId}', ['PostController', 'deletePost']); //게시글 삭제


    $r->addRoute('GET', '/jwt', ['MainController', 'validateJwt']);
    $r->addRoute('GET', '/jwt-data', ['MainController', 'data']);


    $r->addRoute('GET', '/profile/{userIdx}', ['ProfileController', 'getProfile']);
    $r->addRoute('GET', '/openModifyPage', ['ProfileController', 'openModifyPage']);
    $r->addRoute('PATCH', '/introduceModify', ['ProfileController', 'introduceModify']);
    $r->addRoute('DELETE', '/introduceDelete', ['ProfileController', 'introduceDelete']);
    $r->addRoute('PATCH', '/hobbyModify', ['ProfileController', 'modifyHobby']);

    $r->addRoute('GET', '/allFriend/{userIdx}', ['ProfileController', 'getAllFriends']);

    $r->addRoute('POST', '/insertProfileImage', ['ProfileController', 'insertProfileImage']);
    $r->addRoute('POST', '/insertCoverImage', ['ProfileController', 'insertCoverImage']);

//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'MainController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MainController.php';
                break;
            case 'UserController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/UserController.php';
                break;
            case 'PostController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/PostController.php';
                break;
            case 'ProfileController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/ProfileController.php';
                break;
            /*case 'EventController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ProfileController.php';
                break;
            /*case 'ProductController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ProductController.php';
                break;
            case 'SearchController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/SearchController.php';
                break;
            case 'ReviewController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ReviewController.php';
                break;
            case 'ElementController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ElementController.php';
                break;
            case 'AskFAQController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/AskFAQController.php';
                break;*/
        }

        break;
}
