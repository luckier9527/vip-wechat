<?php
/**
 * 用户账户管理控制器


 */
class AccountController extends AdminController{
    public $title="会员账户";
    /**
     * 添加用户账户
     */
    public function add()
    {
        $model = new UserModel();
        if($_POST){
            if($model->validate($_POST)){
                $model->insertData($_POST);
                $msg = '添加成功!';
            }else{
                $msg = '添加失败：'.$model->error;
            }
                $url = 'index.php?p=back&c=account&a=add';
                $this->redirect($url,$msg);

        }

        require VIEW_PATH . 'add.html';

    }
    /**
     * 账户列表
     */
    public function index()
    {
        $model = new UserModel();
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $size = isset($_GET['size']) ? $_GET['size'] : $GLOBALS['config']['back']['page_size'];
        $prama = array();
        if(isset($_GET['Filter'])) $prama = $_GET['Filter'];
        $rst = $model->getPageList($page, $size,$prama);
        $rows = $rst['rows'];
        $count = $rst['count'];
        $page_tool = new PageTool();
        $page_html = $page_tool->page($count, $size, $page, 'index.php?p=back&c=account&a=index');

        require VIEW_PATH . 'index.html';
    }
    /**
     * 修改用户账户
     */
    public function edit()
    {
        $user_id = isset($_GET['user_id'])?$_GET['user_id']:0;
        $model = new UserModel();
        $row = $model->getByPk($user_id);
        if($row==false){
            $url = '?c=account&a=index';
            $this->redirect($url,'用户不存在');
        }
        if($_POST){
            if($model->validate($_POST)){
                $model->updateData($_POST,'user_id='.$row['user_id']);
                $msg = '修改成功!';
                $url = 'index.php?p=back&c=account&a=index';
            }else{
                $msg = '修改失败：'.$model->error;
                $url = 'index.php?p=back&c=account&a=edit&user_id='.$row['user_id'];
            }

            $this->redirect($url,$msg);

        }
        require VIEW_PATH . 'edit.html';
    }
    /**
     * 删除用户账户
     */
    public function del()
    {
        $user_id = isset($_GET['user_id'])?$_GET['user_id']:0;
        $model = new UserModel();
        $row = $model->getByPk($user_id);
        $url = '?c=account&a=index';
        if($row==false){
            $this->redirect($url,'用户已经被删除');
        }
        if($model->deleteByPk($row['user_id'])){
            $this->redirect($url,'删除用户成功');
        }else{
            $this->redirect($url,'删除用户失败');
        }
    }

    //修改自己密码
    function chpasswd()
    {
        //1.显示修改密码的表单
        //3个输入框 旧密码oldpasswd ，新密码newpasswd ，确认新密码newpasswd2

        //2.获取用户提交的表单信息$_POST

        //3.检查新密码和确认新密码是否一致

        //4.验证旧密码
        //MEMBER check($username, $password) 就跟用户登录验证一样，到数据库查询
        //$username =isset($_COOKIE['admin_id'])?$_COOKIE['admin_id']:null;
        //$password = $_POST['oldpasswd']

        //5。验证通过更新用户密码
        //$model = new MemberModel;
        //$pwd = $model->mcrypt($_POST['mewpasswd']);
        //$model->updateData（array('passwd'=>$pwd),'username="'.$username.'"'）


    }





}