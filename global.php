<?php
define('WEB_SOCKET_SERVER', 'ws://x.x.x.x:9502');
define("BASE_URL", "http://x.x.x.x/swoole-customer-service/");
define("DB_HOST", "x.x.x.x");
define("DB_PORT", 3306);
define("DB_NAME", "xxx");
define("DB_USER", "xxx");
define("DB_PWD", "xxx");
define("REDIS_HOST", 'x.x.x.x');
define("REDIS_PORT", 6379);

define('WEB_SOCKET_SERVER_IP', 'x.x.x.x');
define('WEB_SOCKET_SERVER_PORT', 9502);

include "class/MysqlPdo.php";
include "class/OpensslCipher.php";
include "class/Utils.php";

function h($str)
{
    return htmlentities($str, ENT_COMPAT | ENT_HTML401, 'UTF-8');
}

$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);
$pdo = MysqlPdo::getInstance()->getDbConnection();
