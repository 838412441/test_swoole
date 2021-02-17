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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // redis
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
        // websocket
        $this->server = new \Swoole\WebSocket\Server("0.0.0.0", 9501);
        $this->server->set([
            'dispatch_mode' => 5,
        ]);
        $this->server->on('open', function (\swoole_websocket_server $server, $request) {
            $server->bind($request->fd, 11);
            $this->redis->rPush(11, $request->fd);
            echo "server: handshake success with fd{$request->fd}\n";
        });
        $this->server->on('message', function (\Swoole\WebSocket\Server $server, $frame) {
            // echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
            echo "frame";
            var_dump($frame);
            echo PHP_EOL;
            echo "server";
            var_dump($server->getClientInfo($frame->fd));
            $server->push($frame->fd, "this is server");
        });
        $this->server->on('close', function ($ser, $fd) {
            echo "client {$fd} closed\n";
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
