<?php

require "../../vendor/autoload.php";
use Twilio\Rest\Client;

$sid = "AC11e0c32f223e9f9424f5de8664c56d3b";
$token = "d8419de55b30f7d7acb918a07973f0b8";
$client = new Client($sid, $token);

$client->messages->create([
    'to' => '+13305037056',
    'from' => '+17243085071',
    'body' => '<<MESSAGE>>'
]);


?>
