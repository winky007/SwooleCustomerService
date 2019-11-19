<?php include "../header.php"; ?>
<?php
if ($_GET['logout']) {
    unset($_SESSION['user']);
    header("location:login.php");
    exit;
}
if (isset($_SESSION['user']['admin_uid'])) {
    header("location:index.php");
    exit;
}
if ($_POST) {
    $sql = "SELECT * FROM admin WHERE `name` = :name LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $_POST['name']);
    if ($stmt->execute() === false) {
        throw new Exception($stmt->errorInfo());
    }
    $rst = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rst) {
        $_SESSION['user']['admin_uid'] = $rst['id'];
        $_SESSION['user']['admin_name'] = $rst['name'];
        $token = ['uid' => $rst['id'], 'name' => $rst['name'], 'is_admin' => 1];
        $token = OpensslCipher::encode(json_encode($token));
        setcookie('token', $token, time() + 86400, '/');
        header("location:./index.php");
    } else {
        echo "<h2 style='color: red'>错误: 没有这个用户</h2>";
    }
}
?>
    <h2>登录</h2>
    <form action="" method="post">
        <input type="text" name="name" placeholder="请输入登录名">
        <button class="button" type="button" onclick="this.form.submit();">Send</button>
    </form>
<?php include "../footer.php"; ?>