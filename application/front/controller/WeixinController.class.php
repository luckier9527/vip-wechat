<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/24
 * Time: 10:55
 */
require APP_PATH."wechat/autoload.php";
use Overtrue\Wechat\Server;
use Overtrue\Wechat\Message;
use Overtrue\Wechat\Menu;
use Overtrue\Wechat\MenuItem;

class WeixinController extends Controller
{
    private $_appId = 'wx9f2b9ee81f41c72d';
    private $_secret = 'b81f2f1559dc8e1e630df5db77f969af';
    private $_token = "itsource";
    function index()
    {
        $server = new Server($this->_appId, $this->_token);
        //处理关注事件，返回感谢信息
        $server->on('event', 'subscribe', function($event) {
            #error_log('收到关注事件，关注者openid: ' . $event['FromUserName']);
            return Message::make('text')->content('感谢您关注');
        });
        $server->on('event', 'click', function($event) {
            #error_log('收到关注事件，关注者openid: ' . $event['FromUserName']);
            if($event['EventKey'] == 'DONOTCLICK'){
                return Message::make('text')->content('叫你不要点，你偏要点');
            }
            if($event['EventKey'] == 'BIND'){
                return Message::make('news')->items(
                    array(
                        Message::make('news_item')->title('绑定手机')->description('点此绑定你的手机号码')->url('http://http://phpweixin.itsource.cn/itsource/weixin/user.php'),
                    )
                );
                #return  Message::make('text')->content('点此绑定你的手机号码http://139.129.26.163/itsource/weixin/user.php?openid='.$event['FromUserName']);
            }
        });
        //处理默认的回复信息
        $server->on('message', function($message) {
            if($message['Content'] == '帮助'){
                return Message::make('text')->content('显示帮助信息...');
            }
            return Message::make('text')->content('您好！如需帮助请回复 “帮助” ');
        });
        echo $server->serve();
    }
    //设置菜单
    function setMenus()
    {
        $menuService = new Menu($this->_appId, $this->_secret);

        $button = new MenuItem("个人信息");

        $menus = array(
            new MenuItem("最新活动", 'view', 'http://www.taobao.com/'),
            $button->buttons(array(
                new MenuItem('绑定手机', 'click', 'BIND'),
                new MenuItem('预约美发', 'view', 'http://www.baidu.com/'),
                new MenuItem('消费记录', 'view', 'http://www.qq.com/'),
                new MenuItem('这个不能点', 'click', 'DONOTCLICK'),
            )),
        );

        try {
            $menuService->set($menus);// 请求微信服务器
            echo '设置成功！';
        } catch (Exception $e) {
            echo '设置失败：' . $e->getMessage();
        }

    }
    //获取菜单
    function getMenus()
    {
        $menuService = new Menu($this->_appId, $this->_secrett);

        $menus = $menuService->get();

        print_r($menus);
    }
    //活动列表
    function articlelist()
    {

    }
    //查看活动内容
    function article()
    {

    }
    //绑定
    function bind()
    {
        include APP_PATH.'back/model/UserModel.class.php';
        $openid = $_GET['openid'];
        if(!$openid) exit("参数错误");
        $model = new UserModel();
        $user = $model->getByOpenid($openid);
        if($_POST){
            $phone = $_POST['phone'];
            $user = $model->getByPhone($phone);
            if($user){
                $model->updateData(array('opendi'=>$openid),'user_id='.$user['user_id']);
                $this->redirect(FunctionTool::url('front','weixin','bind'),'绑定成功');
            }else{
                $this->redirect(FunctionTool::url('front','weixin','bind'),'找不到该手机号码');
            }
        }
        require VIEW_PATH . 'add.html';
    }
    //查看消费记录
    function user()
    {
        if($_GET['openid']){
            //1.从数据库查询该openid是否存在
            $result = false;
            if($result == false){//2.如果不存在，则显示绑定手机号码信息
                $this->redirect(FunctionTool::url('front','weixin','bind'),'请先绑定手机');
            }else{
                //3.如果存在，则显示对应用户的消费记录信息
                include APP_PATH.'back/model/HistoryModel.class.php';
            }



        }
    }



}