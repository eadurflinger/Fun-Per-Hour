<?php

header("content-type: text/xml");

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$server = $url["host"];
$dbname = substr($url["path"], 1);
$sqluser = $url["user"];
$sqlpass = $url["pass"];

$mysqli = new mysqli($server, $sqluser, $sqlpass, $dbname);
if($mysqli->connect_error) {
  die(json_encode(Array('error' => 'MySQL Connect Error ('.
    $mysqli->connect_errno.') '.
    $mysqli->connect_error)));
}

require "../../vendor/autoload.php";
use Twilio\Rest\Client;

$sid = "AC11e0c32f223e9f9424f5de8664c56d3b";
$token = "d8419de55b30f7d7acb918a07973f0b8";
$client = new Client($sid, $token);

$TIME_BETWEEN_UPDATES = 3600;

date_default_timezone_set('UTC');
$date = date("h:i:s");
$updatetime = date("h:i:s", time()+$TIME_BETWEEN_UPDATES);

if($res=mysqli->query("SELECT * FROM `registration` WHERE `updatetime` < '". $time."'")) {
    while($row = $res->fetch_assoc()) {
        $client->messages->create(
           $row['attendeepno'] , [
    'from' => '+17243085071',
    'body' => 'How much fun are you having from one to ten?'
]);
    }
}

?>
