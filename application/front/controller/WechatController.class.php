<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/13
 * Time: 9:36
 */

require APP_PATH."wechat/autoload.php";
require APP_PATH.'back/model/UserModel.class.php';
#require "./application/wechat/autoload.php";//使用wechat sdk开发前，必须先引入这个autouload.php
use Overtrue\Wechat\Server;
use Overtrue\Wechat\Message;//使用Message类之前要先指定命名空间
use Overtrue\Wechat\MenuItem;
use Overtrue\Wechat\Menu;


class WechatController extends Controller{
    //1。该控制器不要设置用户登录验证检查
    private $_appId = 'wx9f2b9ee81f41c72d';
    private $_token = "itsource";
    private $_secret = 'b81f2f1559dc8e1e630df5db77f969af';

    function index()
    {
        $server = new Server($this->_appId, $this->_token);   
        $server->on('event', 'subscribe', function($event) {
            #error_log('收到关注事件，关注者openid: ' . $event['FromUserName']);
            return Message::make('text')->content('感谢您关注，可回复帮助获取帮助信息');
        });
        //处理用户提交的文本信息
        $server->on('message', 'text', function($message) {
           switch ($message['Content']) {
               case '帮助'://处理用户发送的“帮助” 文本信息
                   return Message::make('text')->content('可回复 绑定手机+手机号码 最新活动 消费记录 解除绑定');
                   break;
               case '最新活动':
                   $model = new ArticleModel();
                   $articles = $model->getAll();
                   /* $articles = array(
                    array('id'=>1,'title'=>'第一个活动','content'=>'xxxx','picurl'=>'xxxxx'),
                    array('id'=>2,'title'=>'第二个活动','content'=>'xxxxxx','picurl'=>'xxxxx')
                );*/
                   $data = array();
                   foreach ($articles as $article) {
                       $item = Message::make('news_item')->title($article['title'])
                           ->url('http://phpweixin.itsource.cn/itsource/vip/index.php?p=front&c=wechat&a=viewaricle&id=' . $article['article_id']);
                       $data[] = $item;
                   }
                   $news = Message::make('news')->items($data);
                   return $news;
                   break;
               case '消费记录':
                   return Message::make('text')->content('点此查看消费记录http://phpweixin.itsource.cn/itsource/vip/index.php?p=front&c=wechat&a=log&openid='.$message['FromUserName']);
                   break;
               case '解除绑定':
                   $model = new UserModel();
                   //获取该openid对应的用户
                   $user = $model->getByOpenid($message['FromUserName']);
                   if ($user) {//如果用户存在，就解除绑定
                       $model->updateData(array('openid' => ''), 'user_id=' . $user['user_id']);
                       return Message::make('text')->content('解绑成功');
                   } else {//如果用户不存在，则提示用户尚未绑定手机
                       return Message::make('text')->content('您尚未绑定手机');
                   }
                   break;
               default:
                   if (strpos($message['Content'], '绑定手机') !== false) {//如果内容包含绑定手机
                       $phone = str_replace('绑定手机', '', $message['Content']);//获取电话号码
                       $length = strlen($phone);
                       if (empty($phone) || $length != 11) {//判断电话号码是否合法
                           return Message::make('text')->content('手机号码格式不正确，请重新输入');
                       } else {
                           $model = new UserModel();
                           $user = $model->getByPhone($phone);//查询会员表，该电话号码是否存在
                           if ($user) {//电话号码存在
                               if ($user['openid']) {//已有openid，说明已经绑定
                                   return Message::make('text')->content('该手机号码已绑定，如需解绑请回复解除绑定');
                               } else {//电话号码存在且未绑定手机号码
                                   $model = new UserModel;
                                   while ($code = UserModel::makeCode()) {
                                       $usercode = $model->getByCode($code);//查询该验证码是否已存在
                                       if ($usercode == false) break;//验证码唯一，跳出
                                   }

                                   $model->updateData(array('code' => $code), 'user_id=' . $user['user_id']);//将验证码更新到数据表
                                   return Message::make('text')->content('请回复验证码+' . $code . '绑定手机');

                               }
                           } else {
                               return Message::make('text')->content('该手机号码不存在，请重新输入');
                           }

                       }
                   }elseif(strpos($message['Content'],'验证码') !==false){
                        $code = str_replace('验证码', '', $message['Content']);
                       if($code){
                           $model = new UserModel();
                           $user = $model->getByCode($code);
                           if($user){
                               $model->updateData(array('openid'=>$message['FromUserName']),'user_id='.$user['user_id']);
                               return Message::make('text')->content('绑定成功');
                           }
                       }
                   }
           }

            #return Message::make('text')->content('我们已经收到您发送的信息！');
        });
        $server->on('event','CLICK',function($event){
            switch ($event['EventKey']){
                case 'bind'://绑定手机
                    return Message::make('text')->content('点此绑定手机http://phpweixin.itsource.cn/itsource/vip/index.php?p=front&c=wechat&a=bind&openid='.$event['FromUserName']);
                    break;

                case 'log':
                    return Message::make('text')->content('点此查看消费记录http://phpweixin.itsource.cn/itsource/vip/index.php?p=front&c=wechat&a=log&openid='.$event['FromUserName']);
                    break;
                case 'news':
                    $model = new ArticleModel();
                    $articles = $model->getAll();

                    $data = array();
                    foreach ($articles as $article) {
                        $item = Message::make('news_item')->title($article['title'])
                            ->url('http://phpweixin.itsource.cn/itsource/vip/index.php?p=front&c=wechat&a=viewaricle&id=' . $article['article_id']);
                        $data[] = $item;
                    }
                    $news = Message::make('news')->items($data);
                    return $news;
                    break;
                    case 'order':
                        return Message::make('text')->content('点此进入预约页面http://phpweixin.itsource.cn/itsource/vip/index.php?p=front&c=wechat&a=order');

                        break;
            }

        });



// 您可以直接echo 或者返回给框架
        echo $server->serve();
        $str = ob_get_contents();
        file_put_contents('repose.xml',$str);
    }
    //设置菜单
    function setMenu()
    {

        $menuService = new Menu($this->_appId, $this->_secret);

        $button = new MenuItem("个人信息");

        $menus = array(
            new MenuItem("最新活动", 'click', 'news'),
            $button->buttons(array(
                new MenuItem('绑定手机', 'click', 'bind'),
                new MenuItem('预约', 'click', 'order'),
                new MenuItem('消费记录', 'click', 'log'),
            )),
        );

        try {
            $menuService->set($menus);// 请求微信服务器
            echo '设置成功！';
        } catch (\Exception $e) {
            echo '设置失败：' . $e->getMessage();
        }

    }
    //从微信服务器获取当前菜单，不会有缓存，可以用来验证菜单的设置情况
    function getMenu()
    {
        $menuService = new Menu($this->_appId, $this->_secret);
        $menus = $menuService->get();
        var_dump($menus);
    }

    function viewAricle()
    {
        $model = new ArticleModel();
        $aticle = $model->getByPk($_GET['id']);

        echo '<pre>';
        var_dump($aticle);
    }

    function bind()
    {

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
        require VIEW_PATH . 'bind.html';
    }
    function ubind()
    {
        $phone = $_GET['phone'];
        $openid = $_GET['openid'];
        $model = new UserModel();
        $user = $model->getByOpenid($openid);
        if($user){
            $model->updateData(array('openid'=>''),'user_id='.$user['user_id']);

        }
        echo '解绑成功';
    }

    function log()
    {
        include APP_PATH.'back/model/HistoryModel.class.php';
        $openid = $_GET['openid'];
        if(!$openid) exit("参数错误");
        $model = new UserModel();
        $user = $model->getByOpenid($openid);
        if($user){
            $model = new HistoryModel();
            $logs = $model->getAll('user_id='.$user['user_id']);
            echo '<pre>';
            var_dump($logs);
        }else{
            echo '请先绑定手机';
        }

    }
    function order()
    {
        echo '这里显示预约页面';
    }
    function test()
    {
        $model = new ArticleModel();
        $articles = $model->getAll();
        echo '<pre>';
        var_dump($articles);
    }


}