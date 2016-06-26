<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/24
 * Time: 9:14
 */
class PlanController extends AdminController{
    public $title = '套餐管理';
    /**
     * 套餐列表
     */
    public function index()
    {
        $model = new PlanModel();
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $size = isset($_GET['size']) ? $_GET['size'] : $GLOBALS['config']['back']['page_size'];
        $rst = $model->getPageList($page, $size);
        $rows = $rst['rows'];
        $count = $rst['count'];
        $page_tool = new PageTool();
        $page_html = $page_tool->page($count, $size, $page, 'index.php?p=back&c=plan&a=index');

        require VIEW_PATH . 'index.html';

    }

    /**
     * 新增套餐
     */
    public function add()
    {
        $model = new PlanModel();
        if($_POST){
            if($model->validate($_POST) && $model->insertData($_POST))
            {
                $msg = '添加成功';
            }else{
                $msg = $model->error;
            }
            $url = 'index.php?p=back&c=plan&a=add';
            $this->redirect($url,$msg);
        }

        require VIEW_PATH . 'add.html';
    }
    /**
     * 修改套餐
     */
    public function edit()
    {
        $plan_id = isset($_GET['plan_id'])?$_GET['plan_id']:0;
        $model = new PlanModel();
        $row = $model->getByPk($plan_id);
        if($row==false){
            $url = '?c=plan&a=index';
            $this->redirect($url,'套餐不存在');
        }
        if($_POST){
            if($model->validate($_POST) && $model->updateData($_POST,'plan_id='.$row['plan_id'])){
                $msg = '修改成功!';
                $url = 'index.php?p=back&c=plan&a=index';
            }else{
                $msg = '修改失败：'.$model->error;
                $url = 'index.php?p=back&c=plan&a=edit&plan_id='.$row['plan_id'];
            }

            $this->redirect($url,$msg);

        }
        require VIEW_PATH . 'edit.html';
    }
    /**
     * 删除套餐
     */
    public function del()
    {
        $plan_id = isset($_GET['plan_id'])?$_GET['plan_id']:0;
        $model = new PlanModel();
        $row = $model->getByPk($plan_id);
        $url = '?c=plan&a=index';
        if($row==false){
            $this->redirect($url,'套餐已经被删除');
        }
        if($model->deleteByPk($row['plan_id'])){
            $this->redirect($url,'删除套餐成功');
        }else{
            $this->redirect($url,'删除套餐失败');
        }
    }
}