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
                                    <div class="chat-message">
                                        <img class="message-avatar avatar-left" src="/img/a1.jpg" alt="">
                                        <div class="message message-left">
                                            <a class="message-author" href="javascript:;"> 颜文字君</a>
                                            <span class="message-date"> 2015-02-02 18:39:23 </span>
                                            <span class="message-content">
											H+ 是个好框架
                                            </span>
                                        </div>
                                    </div>
                                    <div class="chat-message">
                                        <img class="message-avatar avatar-right" src="/img/a1.jpg" alt="">
                                        <div class="message message-right">
                                            <a class="message-author" href="javascript:;"> 颜文字君</a>
                                            <span class="message-date"> 2015-02-02 18:39:23 </span>
                                            <span class="message-content">
											H+ 是个好框架
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
                                        <textarea class="form-control message-input" name="message"
                                                  placeholder="输入消息内容，按回车键发送"></textarea>
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
            }
        },
        created() {
            this.getUserList();
        }
    });
</script>
</html>