<?php include "../header.php"; ?>
<?php
if (!isset($_SESSION['user']['admin_uid'])) {
    header("location:./login.php");
}
?>
<ul id="chatMsg" class="list-group"></ul>
<script>
    var is_backend = 1;
</script>
<script type="text/javascript" src="js/chat.js"></script>
<?php include "../footer.php"; ?>
