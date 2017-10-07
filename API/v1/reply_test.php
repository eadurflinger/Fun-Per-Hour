<?php
header("content-type: text/xml");

?>

<Response>
    <Message>
        This is a response.
        <?php
        echo "Message Received:".$_POST['Body'];
        echo "From: ".$_POST['From'];

        echo json_encode(getallheaders());
        ?>
    </Message>
</Response>
