<?php
header("content-type: text/xml");

?>

<Response>
    <Message>
        This is a response.
        <?php echo json_encode($_POST); ?>
    </Message>
</Response>
