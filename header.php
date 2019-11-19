<?php include "global.php"; ?>
<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta charset="UTF-8">
    <title>在线客服</title>
    <base href="<?php echo BASE_URL; ?>"/>
    <link href="css/chat.css" rel="stylesheet">
    <script type="text/javascript">
        var web_socket_server = '<?php echo WEB_SOCKET_SERVER;?>';
    </script>
    <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
    <link rel="stylesheet" href="js/jquery-ui-1.8.2/jquery-ui-1.8.2.custom.css" type="text/css" media="screen"/>
    <script type="text/javascript" src="js/jquery-ui-1.8.2/jquery-ui-1.8.2.custom.min.js"></script>
    <link type="text/css" href="js/jquery.ui.chatbox/jquery.ui.chatbox.css" rel="stylesheet"/>
    <script type="text/javascript" src="js/jquery.ui.chatbox/chatboxManager.js"></script>
    <script type="text/javascript" src="js/jquery.ui.chatbox/jquery.ui.chatbox.js"></script>
</head>
<body>