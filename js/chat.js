var websocket = new WebSocket(web_socket_server);

$(window).unload(function(){
    websocket.close();
});

websocket.onopen = function (evt) {
    $("#chatMsg").prepend("<li style='color: green' class=\"list-group-item\">Connected to WebSocket server!</li>");
};

websocket.onclose = function (evt) {
    $("#chatMsg").prepend("<li style='color: red' class=\"list-group-item\">Disconnected! Code: " + evt.code + "</li>");
};

websocket.onmessage = function (evt) {
    var data = $.parseJSON(evt.data);
    console.log(evt.data);
    var is_admin = data.is_admin;
    if (!is_admin && $("#chat_div" + data.from_uid).length < 1) {
        createBox(data.to_name, data.from_uid);
    }
    if (is_backend) {
        $("#" + "chat_div" + data.from_uid).chatbox("option", "boxManager").addMsg(data.from_name, data.msg);
    } else {
        $("#chat_div").chatbox("option", "boxManager").addMsg(data.from_name, data.msg);
    }
};

websocket.onerror = function (evt, e) {
    $("#chatMsg").prepend("<li style='color: red' class=\"list-group-item\">Error occured:" + evt.data + "</li>");
};

function send(msg, to_uid) {
    var jsonMsg = {message: msg, token: getCookie('token'), to_uid: to_uid};
    msgStr = JSON.stringify(jsonMsg);
    websocket.send(msgStr);
}

function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=")
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1
            c_end = document.cookie.indexOf(";", c_start)
            if (c_end == -1) c_end = document.cookie.length
            return unescape(document.cookie.substring(c_start, c_end))
        }
    }
    return ""
}

var totalChat = 0;

function createBox(user, to_uid) {
    if (is_backend) {
        var id = 'chat_div' + to_uid;
    } else {
        var id = 'chat_div';
    }
    $("#chatMsg").after('<div id=' + id + '></div>');
    $("#" + id).chatbox({
        id: to_uid,
        title: "在线客服",
        user: user,
        offset: 320 * totalChat,
        messageSent: function (id, user, msg) {
            this.boxManager.addMsg(user, msg);
            if (is_backend) {
                send(msg, to_uid);
            } else {
                send(msg, to_uid);
            }
        },
    });
    if (!is_backend) {
        $("#" + id).chatbox("option", "boxManager").addMsg("欢迎你", user);
    }
    totalChat++;
}
