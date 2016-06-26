<?php
/**
 * 用户账户管理控制器


 */
class MemberController extends AdminController {
    public $title="员工管理";
    /**
     * 添加用户账户
     */
    public function add()
    {
        $model = new MemberModel();
        if($_POST){
            if($model->validate($_POST)){
                //处理密码加密
                $post = $_POST;
                $post['password'] = $model->mcrypt($post['password']);
                $model->insertData($post);
                $msg = '添加成功!';
            }else{
                $msg = '添加失败：'.$model->error;
            }
                $url = 'index.php?p=back&c=member&a=add';
                $this->redirect($url,$msg);

        }
        $groupmodel = new GroupModel();
        $groups = FunctionTool::toHashmap($groupmodel->getAll(),'group_id','name');

        require VIEW_PATH . 'add.html';

    }
    /**
     * 账户列表
     */
    public function index()
    {
        $model = new MemberModel();
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $size = isset($_GET['size']) ? $_GET['size'] : $GLOBALS['config']['back']['page_size'];
        $rst = $model->getPageList($page, $size);
        $rows = $rst['rows'];
        $count = $rst['count'];
        $page_tool = new PageTool();
        $page_html = $page_tool->page($count, $size, $page, 'index.php?p=back&c=member&a=index');

        require VIEW_PATH . 'index.html';
    }
    /**
     * 修改用户账户
     */
    public function edit()
    {
        $user_id = isset($_GET['member_id'])?$_GET['member_id']:0;
        $model = new MemberModel();
        $row = $model->getByPk($user_id);
        if($row==false){
            $url = '?c=account&a=index';
            $this->redirect($url,'用户不存在');
        }
        if($_POST){
            if($model->validate($_POST)){
                $model->updateData($_POST,'member_id='.$row['member_id']);
                $msg = '修改成功!';
                $url = 'index.php?p=back&c=member&a=index';
            }else{
                $msg = '修改失败：'.$model->error;
                $url = 'index.php?p=back&c=member&a=edit&user_id='.$row['user_id'];
            }

            $this->redirect($url,$msg);

        }
        $groupmodel = new GroupModel();
        $groups = FunctionTool::toHashmap($groupmodel->getAll(),'group_id','name');

        require VIEW_PATH . 'edit.html';
    }
    /**
     * 删除用户账户
     */
    public function del()
    {
        $user_id = isset($_GET['member_id'])?$_GET['member_id']:0;
        $model = new MemberModel();
        $row = $model->getByPk($user_id);
        $url = '?c=member&a=index';
        if($row==false){
            $this->redirect($url,'用户已经被删除');
        }
        if($model->deleteByPk($row['member_id'])){
            $this->redirect($url,'删除用户成功');
        }else{
            $this->redirect($url,'删除用户失败');
        }
    }




}