<?php
header("content-type: text/xml");

?>

<Response>
    <Message>
        This is a response.
        <?php
        echo "Message Received:".$_POST['Body'];
        echo "From: ".$_POST['From'];

        $myfile = fopen("log.txt", "w");
        fwrite($myfile, json_encode(getallheaders()));
        fclose($myfile);
        ?>
    </Message>
</Response>
