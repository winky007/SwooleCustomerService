<?php
include "global.php";
$server = new swoole_websocket_server(WEB_SOCKET_SERVER_IP, WEB_SOCKET_SERVER_PORT);
$server->set([
    'task_worker_num' => 6,
    'log_level' => SWOOLE_LOG_ERROR,
    'trace_flags' => SWOOLE_TRACE_CLOSE,
]);

$server->on('open', function ($server, $req) {
    $redis = Utils::redis();
    $cookie = $req->cookie;
    $fd = $req->fd;
    $oldToken = $cookie['token'];
    $token = OpensslCipher::decode($oldToken);
    $token = json_decode($token, true);
    if ($token['is_admin']) {
        $redis->set("admin:getUidByFd:{$fd}", $token['uid'], 86400);
        $redis->set("admin:getFdByUid:{$token['uid']}", $fd, 86400);
        $redis->set("admin:getNameByUid:{$token['uid']}", $token['name'], 86400);
        $redis->sAdd('admin:uidList', $token['uid']);
        $redis->setTimeout('admin:uidList', 86400);
    } else {
        $redis->set("user:getUidByFd:{$fd}", $token['uid'], 86400);
        $redis->set("user:getFdByUid:{$token['uid']}", $fd, 86400);
        $redis->set('user:getNameByUid:' . $token['uid'], $token['name'], 86400);
        $redis->sAdd('user:uidList', $token['uid']);
        $redis->setTimeout('user:uidList', 86400);
    }
    $redis->set("tokenByFd:" . $fd, $oldToken, 86400);
    Utils::debug("connection open: {$req->fd}");
});

$server->on('Request', function ($request, $response) {
    echo "request\n";
});

$server->on('message', function ($server, $frame) {
    $server->task($frame);
});

$server->on('task', function ($server, $taskId, $fromId, $frame) {
    $data = $frame->data;
    $fd = $frame->fd;
    echo "received message from {$fd}: " . json_encode($frame) . "\n";
    $dataArr = json_decode($data, true);
    $msg = $dataArr['message'];
    $toUid = $dataArr['to_uid'];
    $token = OpensslCipher::decode($dataArr['token']);
    var_export($token);
    Utils::debug($token, 'task token');
    $token = json_decode($token, true);
    $uid = $token['uid'];
    $name = $token['name'];
    $isAdmin = $token['is_admin'];
    $redis = Utils::redis();
    Utils::debug($isAdmin, '$isAdmin');
    if (!$isAdmin) {
        $adminList = $redis->sMembers('admin:uidList');
        Utils::debug($adminList, '$adminList');
        if (empty($adminList)) {
            $d = [
                'type' => 'message',
                'msg' => '客服都在忙线中',
                'from_uid' => $token['uid'],
                'to_uid' => $token['uid'],
                'is_admin' => 1,
                'from_name' => 'Admin',
                'to_name' => '客官',
            ];
            Utils::debug('no admin list');
            Utils::debug($d, '$d');
            $server->push($fd, json_encode($d));
            return;
        } else {
            //assign user
            $toUid = $redis->sRandMember('admin:uidList');
            $toFd = $redis->get("admin:getFdByUid:{$toUid}");
            Utils::debug('assign user user');
            Utils::debug($toUid, '$toUid');
            Utils::debug($toFd, '$toFd');
        }
        $toName = $redis->get("admin:getNameByUid:{$toUid}");
        Utils::debug($toName, 'toName');
    } else {
        $toFd = $redis->get("user:getFdByUid:{$toUid}");
        $toName = $redis->get("user:getNameByUid:{$toUid}");
        Utils::debug($toFd, '$toFd');
    }
    $sendMsg = nl2br(Utils::h($msg));
    $fromName = $name;
    $d = [
        'type' => 'message',
        'msg' => $sendMsg,
        'from_uid' => $uid,
        'to_uid' => $toUid,
        'is_admin' => $isAdmin,
        'from_name' => $fromName,
        'to_name' => $toName,
    ];
    Utils::debug($d, 'push to {$toFd}');
    $server->push($toFd, json_encode($d));
    return $data;
});

$server->on('finish', function ($server, $taskId, $data) {
    Utils::debug("{$taskId} task. data:{$data}", 'finish');
});

$server->on('close', function ($server, $fd) {
    $redis = Utils::redis();
    $token = $redis->get("tokenByFd:" . $fd);
    if ($token !== false) {
        $token = OpensslCipher::decode($token);
        $token = json_decode($token, true);
        Utils::debug($token, 'close');
        $adminKeys = [];
        if ($token['is_admin']) {
            $adminKeys = [
                "admin:getUidByFd:{$fd}",
                "admin:getFdByUid:{$token['uid']}",
                "admin:getNameByUid:{$token['uid']}"
                ];
        }
        $userKeys = [
            "user:getUidByFd:{$fd}",
            "user:getFdByUid:{$token['uid']}",
            'user:getNameByUid:' . $token['uid'],
            "tokenByFd:" . $fd,
        ];
        $keys = array_merge($adminKeys, $userKeys);
        foreach ($keys as $key) {
            $redis->del($key);
        }
        $redis->sRem('user:uidList', $token['uid']);
        if ($token['is_admin']) {
            $redis->sRem('admin:uidList', $token['uid']);
        }
    }
    Utils::debug("connection close: {$fd}");
});

$server->start();
