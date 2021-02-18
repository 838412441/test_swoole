<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class Swoole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $url = "ws://175.24.185.52:9501";
    protected $server;
    protected $redis;
    protected $userList = [
        [
            'id' => 1,
            'title' => '小红',
            'avatar' => '/img/a3.jpg',
        ],
        [
            'id' => 2,
            'title' => '小兰',
            'avatar' => '/img/a7.jpg',
        ],
        [
            'id' => 3,
            'title' => '小青',
            'avatar' => '/img/a5.jpg',
        ],
        [
            'id' => 4,
            'title' => '王刚',
            'avatar' => '/img/a6.jpg',
        ],
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // redis
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
        // websocket
        $this->server = new \Swoole\WebSocket\Server("0.0.0.0", 9501);
        // 设置websocket链接方式
        $this->server->set([
            'dispatch_mode' => 5,
        ]);
        $this->server->on('open', function (\swoole_websocket_server $server, $request) {
            var_dump("start " . $request->fd);
        });
        $this->server->on('message', function (\Swoole\WebSocket\Server $server, $frame) {
            $data = $frame->data;
            $data = json_decode($data, true);
            if ($data['type'] == 'start') {
                // 将链接FD绑定对应用户
                $server->bind($frame->fd, $data['token']);
                // 存入redis
                $this->redis->rPush($data['token'], $frame->fd);
            } elseif ($data['type'] == 'close') {
                // 删除redis中的绑定关系
                $this->redis->lRem($data['token'], $frame->fd);
            } elseif ($data['type'] == 'message') {
                if (!isset($data['token']) || !isset($data['party']) || !isset($data['message'])) {
                    $json = ['code' => 999, 'message' => 'token、party、message不存在'];
                    $server->push($frame->fd, json_encode($json, JSON_UNESCAPED_UNICODE));
                }
                $user_id_list = array_column($this->userList, 'id');
                if (!in_array($data['token'], $user_id_list) || !in_array($data['party'], $user_id_list)) {
                    $json = ['code' => 999, 'message' => '发送者或接受者不存在'];
                    $server->push($frame->fd, json_encode($json, JSON_UNESCAPED_UNICODE));
                }
                // 读取所有当前用户的FD信息
                $fds = $this->redis->lRange($data['token'], 0, -1);
                // 读取所有接受者的FD信息
                $party = $this->redis->lRange($data['party'], 0, -1);
                // 接受消息
                $infos = [
                    'send_user_id' => $data['token'],
                    'send_user_info' => json_encode($this->userList[array_search($data['token'], $user_id_list)], JSON_UNESCAPED_UNICODE),
                    'party_user_id' => $data['party'],
                    'party_user_info' => json_encode($this->userList[array_search($data['party'], $user_id_list)], JSON_UNESCAPED_UNICODE),
                    'message' => $data['message'],
                    'time' => Carbon::now(),
                ];
                // 判断聊天室是否存在
                if ($this->redis->exists($data['token'] . "_" . $data['party'])) {
                    // 获取聊天室
                    $room = $data['token'] . "_" . $data['party'];
                } elseif ($this->redis->exists($data['party'] . "_" . $data['token'])) {
                    // 获取聊天室
                    $room = $data['party'] . "_" . $data['token'];
                } else {
                    // 创建一个聊天室
                    $room = $data['token'] . "_" . $data['party'];
                }
                // 在聊天室内储存信息
                $message = json_encode(['code' => 1000, 'type' => 'string', 'message' => '操作成功', 'data' => compact('infos')]);
                $this->redis->rPush($room, $message);
                // 将聊天信息返回前端
                foreach ($fds as $key => $value) {
                    $server->push($value, $message);
                }
                if ($party) {
                    foreach ($party as $key => $value) {
                        $server->push($value, $message);
                    }
                }
            } elseif ($data['type'] == 'history') {
                // 读取所有当前用户的FD信息
                $fds = $this->redis->lRange($data['token'], 0, -1);
                // 读取所有接受者的FD信息
                $party = $this->redis->lRange($data['party'], 0, -1);
                // 判断聊天室是否存在
                if ($this->redis->exists($data['token'] . "_" . $data['party'])) {
                    // 获取聊天室
                    $room = $data['token'] . "_" . $data['party'];
                } elseif ($this->redis->exists($data['party'] . "_" . $data['token'])) {
                    // 获取聊天室
                    $room = $data['party'] . "_" . $data['token'];
                }
                // 获取聊天室的所有信息
                $infos = $this->redis->lRange($room, 0, -1);
                $message = json_encode(['code' => 1000, 'message' => 'array', 'message' => '操作成功', 'data' => compact('infos')]);
                foreach ($fds as $index => $item) {
                    $server->push($item, $message);
                }
            }
            var_dump($data);
        });
        $this->server->on('close', function ($ser, $fd) {
            var_dump("close " . $fd);
        });
        $this->server->start();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dd($this->url);
    }

}
