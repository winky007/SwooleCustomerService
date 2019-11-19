<?php include "header.php"; ?>
<?php
if (!isset($_SESSION['user']['uid'])) {
    header("location:login.php");
}
?>
<h5>注意:</h5>
<ul style="color: black;">
    <li>1 请用另一个浏览器打开以下URL接收信息:<br/> <?php echo BASE_URL; ?>admin <br></li>
    <li>2 再在这个页面的右下角输入信息 <br></li>
    <li>3 在步骤1的页面查看信息；输入信息，此页面会看到</li>
</ul>
<ul id="chatMsg" class="list-group">
</ul>
<script type="text/javascript">
    var is_backend = 0;
    <?php if (isset($_SESSION['user']['uid'])) { ?>
    $(function () {
        createBox('<?php echo $_SESSION['user']['name'];?>', '<?php echo $_SESSION['user']['uid'];?>');
    });
    <?php } ?>
</script>
<script type="text/javascript" src="js/chat.js"></script>
<?php include "footer.php"; ?>