<?php
require "./application/wechat/autoload.php";//引入autouload.php

use Overtrue\Wechat\Server;//指定Server类的姓名
use Overtrue\Wechat\Message;

class ZhangsanController {
    private $_appId = 'wx4cdf13aedac0a9b1';
    private $_secret = 'a82c403a2cdbccf0d6f51b081828421c';
    private $_token = "itsource";
    public function index()
    {
       $server = new Server($this->_appId, $this->_token);
       //1 监听用户发送的“人数”这个文本信息
       $server->on('message','text', function($message){
           if($message['Content'] == '人数'){
               //2 统计系统中会员的数量
               //$count = 100;
               $db = MysqlDb::getInstance();
               $sql = 'SELSECT count(*) as count FROM `htk_users`';
               $results = $db->query($sql);
               $count = $results[0]['count'];
               //3 将会员数量返回给微信服务器
               return Message::make('text')->content('当前系统会员数量为'.$count);
           }
       });
       
       echo $server->serve();
    }
    
    //put your code here
}
