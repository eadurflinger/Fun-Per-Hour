<?php
require_once 'api.php';
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));


$server = $url["host"];
$dbname = substr($url["path"], 1);
$sqluser = $url["user"];
$sqlpass = $url["pass"];

// $dbname = 'timecardapp_v3';
// $sqluser = 'web_user';
// $sqlpass = 'BFWKsYv2PnywhDms' ;

$mysqli = new mysqli($server, $sqluser, $sqlpass, $dbname);
if($mysqli->connect_error) {
  die(json_encode(Array('error' => 'MySQL Connect Error ('.
    $mysqli->connect_errno.') '.
    $mysqli->connect_error)));
}

try {
    $API = new mAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN'],$mysqli);
    print $API->runAPI();
} catch (Exception $e) {
    //header('HTTP/1.1' +500)
    print json_encode(Array('error' => $e->getMessage()));
}

?>
