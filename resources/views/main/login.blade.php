<!doctype html>
<html lang="en">
<head>
    @include("main.header");
</head>
<body class="gray-bg">
<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div id="app">
        <div>

            <h1 class="logo-name">H+</h1>

        </div>
        <h3>选择一个用户身份登录</h3>

        <form class="m-t" role="form" action="">
            <div class="form-group">
                <select name="" ref="user" class="form-control">
                    <option v-for="value in userList" :value="value.id">@{{ value.title }}</option>
                </select>
            </div>
            <button @click="submit" type="button" class="btn btn-primary block full-width m-b">登 录</button>
        </form>
    </div>
</div>
</body>
@include("main.footer");
<script>
    var app = new Vue({
        el: '#app',
        data: {
            message: 'Hello Vue!',
            userList: [
                {
                    id: 1,
                    title: "小红"
                },
                {
                    id: 2,
                    title: "小兰"
                },
                {
                    id: 3,
                    title: "小青"
                },
                {
                    id: 4,
                    title: "王刚"
                },
            ],
        },
        methods: {
            submit() {
                let user = this.$refs.user.value;
                this.$http.get("/swoole/login-pd/" + user).then(function (res) {
                    if (res.data.code == 1000) {
                        localStorage.setItem("token", res.data.token)
                        window.location.href = '/swoole/chat';
                    } else {
                        alert(res.data.message);
                    }
                });
            }
        }
    })
</script>
</html>