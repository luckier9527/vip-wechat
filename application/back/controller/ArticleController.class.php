<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/24
 * Time: 9:14
 */
class ArticleController extends AdminController{
    public $title = '活动管理';
    /**
     *
     */
    public function index()
    {
        $model = new ArticleModel();
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $size = isset($_GET['size']) ? $_GET['size'] : $GLOBALS['config']['back']['page_size'];
        $rst = $model->getPageList($page, $size);
        $rows = $rst['rows'];
        $count = $rst['count'];
        $page_tool = new PageTool();
        $page_html = $page_tool->page($count, $size, $page, 'index.php?p=back&c=article&a=index');

        require VIEW_PATH . 'index.html';

    }

    /**
     * 新增套餐
     */
    public function add()
    {
        $model = new ArticleModel();
        if($_POST){
            if($model->validate($_POST) && $model->insertData($_POST))
            {
                $msg = '添加成功';
            }else{
                $msg = $model->error;
            }
            $url = 'index.php?p=back&c=article&a=index';
            $this->redirect($url,$msg);
        }

        require VIEW_PATH . 'add.html';
    }
    /**
     * 修改套餐
     */
    public function edit()
    {
        $id = isset($_GET['article_id'])?$_GET['article_id']:0;
        $model = new ArticleModel();
        $row = $model->getByPk($id);
        if($row==false){
            $url = '?c=article&a=index';
            $this->redirect($url,'活动不存在');
        }
        if($_POST){
            if($model->validate($_POST) && $model->updateData($_POST,'article_id='.$row['article_id'])){
                $msg = '修改成功!';
                $url = 'index.php?p=back&c=article&a=index';
            }else{
                $msg = '修改失败：'.$model->error;
                $url = 'index.php?p=back&c=article&a=edit&article_id='.$row['article_id'];
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
        $id = isset($_GET['article_id'])?$_GET['article_id']:0;
        $model = new ArticleModel();
        $row = $model->getByPk($id);
        $url = '?c=article&a=index';
        if($row==false){
            $this->redirect($url,'活动已经被删除');
        }
        if($model->deleteByPk($row['article_id'])){
            $this->redirect($url,'删除活动成功');
        }else{
            $this->redirect($url,'删除活动失败');
        }
    }
}