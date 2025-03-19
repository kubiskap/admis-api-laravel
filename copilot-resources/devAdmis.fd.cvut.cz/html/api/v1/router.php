<?php
require_once __DIR__ . "/../../conf/config.inc";
require_once SYSTEMINCLUDES . "functionApi.php";
error_reporting(E_ALL ^ E_NOTICE);
$accessDenied = true;
if (isset($_GET['token']) && isset($_SERVER['REMOTE_ADDR']) && \api\authorizeAccess($_GET['token'], $_SERVER['REMOTE_ADDR']) && $_SERVER['HTTPS']) {
    $context = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $accessDenied = false;
    if (substr($context, 0, 15) == '/api/v1/project') {
        $output = [];
        if (preg_match("/\\/api\\/v1\\/project\\/\\d/", $context)) {
            preg_match("/\\/api\\/v1\\/project\\/(\\d+)/", $context, $idProject);
            $output = api\getProject($idProject[1]);
        } elseif (preg_match("/\\/api\\/v1\\/project\\/?/", $context)) {
            $limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] <= 500) ? $_GET['limit'] : 500;
            $offset = (isset($_GET['offset']) && is_numeric($_GET['offset'])) ? $_GET['offset'] : 0;

            if (isset($_GET['fromUpdated']) && is_numeric($_GET['fromUpdated'])) {
                $output = api\getAllProjects($_GET['fromUpdated'], $limit, $offset);
            } else {
                $output = api\getAllProjects(null, $limit, $offset);
            }
        }


        if (!empty($output)) {
            header('HTTP/1.1 200 OK');
            header('Content-Type: application/json');

            print_r(\api\getJsonOutput($output));
        } else {
            header('HTTP/1.1 200 OK');
            echo "No data found";
        }
    } else {
        header('HTTP/1.1 422 Unprocessable Entity');
        echo 'Not supported method';
    }

} else {
    header('HTTP/1.0 403 Forbidden');

    echo 'You are forbidden!';
}
$token = isset($_GET['token']) ? $_GET['token'] : null;
$userId = \api\authorizeAccess($token, $_SERVER['REMOTE_ADDR']);
if (!$accessDenied) {
    unset($_GET['token']);
}
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . "?" . http_build_query($_GET);
\api\logEvent($userId, $url, $_SERVER['REMOTE_ADDR'], $accessDenied);


