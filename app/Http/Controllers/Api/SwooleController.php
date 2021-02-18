<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

class SwooleController extends Controller
{
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

    // 页面
    public function chat()
    {
        return view('main.chat');
    }

    // 获取好友列表
    public function userList($token)
    {
        $userList = $this->userList;
        foreach ($userList as $key => $value) {
            if ($token == $value['id']) {
                unset($userList[$key]);
            }
        }
        sort($userList);
        return response()->json(['code' => 1000, 'message' => '操作成功', 'data' => compact('userList')]);
    }

    // 获取选中的用户信息
    public function userInfo($party)
    {
        $user_id_list = array_column($this->userList, 'id');
        $user = $this->userList[array_search($party, $user_id_list)];
        return response()->json(['code' => 1000, 'message' => '操作成功', 'data' => compact('user')]);
    }
}