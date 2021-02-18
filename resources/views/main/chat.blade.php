<!doctype html>
<html lang="en">
<head>
    @include("main.header");
    <style>
        .avatar-left {
            float: left;
        }

        .message-left {
            text-align: left;
            margin-left: 50px;
            margin-right: 0px;
        }

        .avatar-right {
            float: right;
        }

        .message-right {
            text-align: right;
            margin-left: 0px;
            margin-right: 50px;
        }
    </style>
</head>
<body class="gray-bg">
<div id="app">
    <div class="wrapper wrapper-content  animated fadeInRight">

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox chat-view">
                    <div class="ibox-title">
                        <small class="pull-right text-muted">最新消息：{{date('Y-m-d')}}</small>
                        聊天窗口
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-9 ">
                                <div>
                                    <div class="chat-message" v-for="value in chatInfo">
                                        <img class="message-avatar"
                                             :class="[value.send_user_id == token ? 'avatar-right' : 'avatar-left']"
                                             :src="value.send_user_id == token ? value.send_user_info.avatar : value.party_user_info.avatar"
                                             alt="">
                                        <div class="message"
                                             :class="[value.send_user_id == token ? 'message-right' : 'message-left']">
                                            <a class="message-author" href="javascript:;">
                                                @{{ value.send_user_id == token ? value.send_user_info.title :
                                                value.party_user_info.title }}
                                            </a>
                                            <span class="message-date"> @{{ value.time }} </span>
                                            <span class="message-content">
											@{{ value.message }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="chat-users">
                                    <div class="users-list">
                                        <div v-if="userList.length == 0">好友列表中没有好友</div>
                                        <div v-else class="chat-user" v-for="value in userList"
                                             @click="getChatInfo(value.id)">
                                            <img class="chat-avatar" :src="value.avatar" alt="">
                                            <div class="chat-user-name">
                                                <a href="javascript:;">@{{ value.title }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="chat-message-form">
                                    <div class="form-group">
                                        <input type="textarea" class="form-control message-input" @keyup.enter="insert"
                                               :value="message">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
@include("main.footer")
<script>
    var app = new Vue({
        el: "#app",
        data: {
            userList: [],
            chatInfo: [],
            message: '',
            token: localStorage.getItem("token"),
        },
        methods: {
            getChatInfo(id) {
                console.log(id)
            },
            getUserList() {
                this.$http.get("/swoole/user-list/" + this.token).then(function (res) {
                    this.userList = res.data.data.userList;
                });
            },
            insert(e) {
                // 获取
                this.message = e.target.value;
                // 发送
                let actions = {
                    'type': 'message',
                    'token': this.token,
                    'party': 2,
                    'message': this.message,
                }
                this.websocketsend(JSON.stringify(actions));

                console.log(this.message)
                // 清空
                this.message = '';
            },
            initWebSocket() { //初始化weosocket
                const wsuri = "ws://175.24.185.52:9501";
                this.websock = new WebSocket(wsuri);
                this.websock.onmessage = this.websocketonmessage;
                this.websock.onopen = this.websocketonopen;
                this.websock.onerror = this.websocketonerror;
                this.websock.onclose = this.websocketclose;
            },
            websocketonopen() { //连接建立之后执行send方法发送数据
                // 和用户绑定
                let actions = {
                    "type": "start",
                    "token": this.token,
                };
                // 数据发送
                this.websocketsend(JSON.stringify(actions));
                // 获取历史记录
                let history = {
                    "type": "history",
                    "token": this.token,
                    "party": 2,
                };
                this.websocketsend(JSON.stringify(history));
                console.log("链接成功");
            },
            websocketonerror() {//连接建立失败重连
                console.log("链接失败...重连中...");
                this.initWebSocket();
            },
            websocketonmessage(e) { //数据接收
                let res = JSON.parse(e.data);
                if (res.code == 1000) {
                    if (res.type == 'string') {
                        let infos = res.data.infos;
                        infos.send_user_info = JSON.parse(infos.send_user_info);
                        infos.party_user_info = JSON.parse(infos.party_user_info);
                        this.chatInfo.push(infos);
                    } else if (res.type == 'array') {
                        infos.send_user_info = JSON.parse(infos.send_user_info);
                        infos.party_user_info = JSON.parse(infos.party_user_info);
                        this.chatInfo = infos;
                    }
                    console.log(res, 'success');
                } else {
                    console.log(res, 'error');
                }
            },
            websocketsend(Data) {//数据发送
                this.websock.send(Data);
            },
            websocketclose(e) {  //关闭
                // 和用户绑定
                let actions = {
                    "type": "close",
                    "token": this.token,
                };
                // 数据发送
                this.websocketsend(JSON.stringify(actions));
                console.log(e, '断开连接');
            },
        },
        created() {
            // 获取会员列表
            this.getUserList();
            // 判断是否登录
            if (!this.token) {
                window.location.href = '/swoole/login';
            }
            // websocket链接
            this.initWebSocket();
        },
        destroyed() {
            this.websock.close() //离开路由之后断开websocket连接
        },
    });
</script>
</html>