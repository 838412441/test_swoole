<?php

namespace App\Console\Commands;

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
                // 读取所有当前用户的FD信息
                $fds = $this->redis->lRange($data['token'], 0, -1);
                // 读取所有接受者的FD信息
                $party = $this->redis->lRange($data['party'], 0, -1);
            } elseif ($data['type'] == 'history') {
                // 读取当前用户所有FD信息
                $fds = $this->redis->lRange($data['token'], 0, -1);
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
