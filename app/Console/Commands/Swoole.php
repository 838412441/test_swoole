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
//            var_dump($request);
//            // 接收用户token 绑定对应用户
//            $server->bind($request->fd, 11);
//            // reids
//            $this->redis->rPush(11, $request->fd);
//            echo "server: handshake success with fd{$request->fd}\n";
        });
        $this->server->on('message', function (\Swoole\WebSocket\Server $server, $frame) {
            // echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
            // redis
//            $fds = $this->redis->lRange(11, 0, -1);
//            var_dump($fds);
//            $server->push($frame->fd, "this is server");
            $data = $frame->data;
            $data = json_decode($data, true);
            var_dump($data);

        });
        $this->server->on('close', function ($ser, $fd) {
//            echo "client {$fd} closed\n";
//            // redis
//            $this->redis->lRem(11, $fd);
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
