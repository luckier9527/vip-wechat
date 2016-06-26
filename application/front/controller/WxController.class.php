<?php
use Overtrue\Wechat\Server;//指定Server的命名空间(>=php5.3 namespace)
use Overtrue\Wechat\Message;
use Overtrue\Wechat\Menu;
use Overtrue\Wechat\MenuItem;
class WxController extends Controller
{
    private $appId = 'wx7ad20d105dc3a02d';
    private $token = 'itsource';
    private $secret = 'b9019027f1ac1d88118f460762f326d0';
    function __construct()
    {
        parent::__construct();
        require './application/wechat/autoload.php';
    }
    function index()
    {

        $server=new Server($this->appId, $this->token);//实例化Servier对象

        //监听文本信息
        $server->on('message', 'text', function($message) {
            switch($message['Content']){//根据用户发动的文本内容来分别处理
                case '帮助':
                    return Message::make('text')->content('帮助信息');

            }

        });
        //监听关注事件
        $server->on('event', 'subscribe', function($event) {
            return Message::make('text')->content('感谢您关注');
        });
        //监听菜单点击事件
        $server->on('event', 'click', function($event) {
            switch($event['EventKey']){
                case 'V1001_GOOD'://判断点击的是最新活动
                    //从数据库获取活动信息
                    $articles = array(
                        array('id'=>1,'title'=>'第一条活动','content'=>'xxxxxxxxxxxxxxxxxxxxx')
                    );
                    //封装到图文信息
                    $items = array();
                    foreach($articles as $article){
                        $item = Message::make('news_item')->title($article['title'])->url('http://phpweixin.itsource.cn/itsource/vip/index.php?p=front&c=wx&a=article&id='.$article['id']);
                        $items[] = $item;
                    }
                    //创建一个图文消息
                    $news = Message::make('news')->items($items);
                    //返回为微信服务器
                    return $news;
            }
            //$event['EventKey'] 获取菜单的第三个参数
//            return Message::make('text')->content($event['EventKey']);
        });

        // 将处理后的结果返回给微信服务器
        echo $server->serve();
    }
    //设置菜单
    function setmenu()
    {
        $menuService = new Menu($this->appId, $this->secret);
        $button1=new MenuItem("菜单");//创建一个空的一级菜单
        //给菜单添加3个二级菜单
        $button1 = $button1->buttons(
            array(
                new MenuItem('搜索', 'view', 'http://www.soso.com/'),
                new MenuItem('视频', 'view', 'http://v.qq.com/'),
                new MenuItem('赞一下我们', 'click', 'V1001_GOOD'),
            )
        );
        //创建一个一级菜单
        $button2 = new MenuItem("最新活动", 'click', 'activity');
        $menus=array($button1,$button2);

        try {
            $menuService->set($menus);// 请求微信服务器
            echo'设置成功！';
        } catch (\Exception$e) {
            echo'设置失败：'.$e->getMessage();
        }


    }
    //获取菜单
    function getmenu()
    {
        $menuService = new Menu($this->appId, $this->secret);
        $menus = $menuService->get();//读取菜单
        var_dump($menus);
    }
    //显示一条活动详细信息页面
    function article()
    {
        $id = $_GET['id'];
        echo $id;
        //从数据库获取该id对应的活动信息

        //显示content内容

    }
}