<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/24
 * Time: 9:14
 */
class GroupController extends AdminController{
    public $title = '部门管理';
    /**
     * 套餐列表
     */
    public function index()
    {
        $model = new GroupModel();
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $size = isset($_GET['size']) ? $_GET['size'] : $GLOBALS['config']['back']['page_size'];
        $rst = $model->getPageList($page, $size);
        $rows = $rst['rows'];
        $count = $rst['count'];
        $page_tool = new PageTool();
        $page_html = $page_tool->page($count, $size, $page, 'index.php?p=back&c=group&a=index');

        require VIEW_PATH . 'index.html';

    }

    /**
     * 新增
     */
    public function add()
    {
        $model = new GroupModel();
        if($_POST){
            if($model->validate($_POST) && $model->insertData($_POST))
            {
                $msg = '添加成功';
            }else{
                $msg = $model->error;
            }
            $url = 'index.php?p=back&c=group&a=add';
            $this->redirect($url,$msg);
        }

        require VIEW_PATH . 'add.html';
    }

    /**
     * 修改套餐
     */
    public function edit()
    {
        //1.获取需要修改的模型
        $id = isset($_GET['group_id'])?$_GET['group_id']:0;
        $model = new GroupModel();
        $row = $model->getByPk($id);

        if($row==false){
            $url = '?c=group&a=index';
            $this->redirect($url,'套餐不存在');
        }
        if($_POST){
            //4.处理模型修改后的表单数据
            if($model->validate($_POST) && $model->updateData($_POST,'group_id='.$row['group_id'])){
                $msg = '修改成功!';
                $url = 'index.php?p=back&c=group&a=index';
            }else{
                $msg = '修改失败：'.$model->error;
                $url = 'index.php?p=back&c=group&a=edit&group_id='.$row['group_id'];
            }

            $this->redirect($url,$msg);

        }
        //2.展示表单
        require VIEW_PATH . 'edit.html';
        //3.模型数据放入表单


    }

    /**
     * 删除套餐
     */
    public function del()
    {
        $plan_id = isset($_GET['group_id'])?$_GET['group_id']:0;
        $model = new GroupModel();
        $row = $model->getByPk($plan_id);
        $url = '?c=group&a=index';
        if($row==false){
            $this->redirect($url,'套餐已经被删除');
        }
        if($model->deleteByPk($row['group_id'])){
            $this->redirect($url,'删除套餐成功');
        }else{
            $this->redirect($url,'删除套餐失败');
        }
    }
}