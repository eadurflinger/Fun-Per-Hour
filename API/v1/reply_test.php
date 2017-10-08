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

$TIME_BETWEEN_UPDATES = 3600;

date_default_timezone_set('UTC');
$date = date("h:i:s");
$updatetime = date("h:i:s", time()+$TIME_BETWEEN_UPDATES);

$input = $_POST['Body'];
$from = $_POST['From'];
$out = "";


if(((int)$input)>0 && ((int)$input)<=10) {
    $sql = "INSERT INTO `attendee` (attendeepno,funlevel,submitTime) VALUES (". $from ."". $input ."". $time .")";
    if(!$mysqli->query($sql)) {
        $out = "MySQL Error:":$mysqli->error;
    } else {
        $sql = "UPDATE `registration` SET updatetime=".$updatetime.", submittime=".$time." WHERE attendeepno==".$from; //FIXME: change names of columns
    }
} else if()


?>

<Response>
    <Message>
        This is a response.
        <?php echo $out; ?>
    </Message>
</Response>
