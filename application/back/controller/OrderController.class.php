<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/24
 * Time: 9:59
 */
class OrderController extends AdminController{
    public $title = '预约信息';
    /**
     * 预约信息列表
     */
    public function index()
    {
        $model = new OrderModel();
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $size = isset($_GET['size']) ? $_GET['size'] : $GLOBALS['config']['back']['page_size'];
        $rst = $model->getPageList($page, $size);
        $rows = $rst['rows'];
        $count = $rst['count'];
        $page_tool = new PageTool();
        $page_html = $page_tool->page($count, $size, $page, 'index.php?p=back&c=order&a=index');

        require VIEW_PATH . 'index.html';
    }
    /**
     * 处理预约信息
     */
    public function view()
    {
        $order_id = isset($_GET['order_id'])?$_GET['order_id']:0;
        $model = new OrderModel();
        $row = $model->getByPk($order_id);
        if($row==false){
            $url = '?c=order&a=index';
            $this->redirect($url,'预约不存在');
        }
        if($_POST){
            if($model->updateData($_POST,'order_id='.$row['order_id'])){
                $msg = '处理成功!';
                $url = 'index.php?p=back&c=order&a=index';
            }else{
                $msg = '处理失败：'.$model->error;
                $url = 'index.php?p=back&c=order&a=view&order_id='.$row['order_id'];
            }

            $this->redirect($url,$msg);

        }
        require VIEW_PATH . 'view.html';
    }
    /**
     * 删除预约信息
     */
    public function del()
    {
        $order_id = isset($_GET['order_id'])?$_GET['order_id']:0;
        $model = new OrderModel();
        $row = $model->getByPk($order_id);
        $url = '?c=order&a=index';
        if($row==false){
            $this->redirect($url,'预约信息已经被删除');
        }
        if($model->deleteByPk($row['order_id'])){
            $this->redirect($url,'删除预约成功');
        }else{
            $this->redirect($url,'删除预约失败');
        }
    }
}