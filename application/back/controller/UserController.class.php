<?php

class UserController extends Controller
{

    public function login()
    {
        SessionDbTool::getInstance();
        if ($_POST) {
            $username = $_POST['username'];
            $password = $_POST['password'];


            //将数据交给模型验证是否匹配
            $model = new MemberModel();
            $rst = $model->check($username, $password);

            if ($rst) {
                // setcookie('is_login','yes');
                // session_start();
                SessionDbTool::getInstance();
                $_SESSION['is_login'] = 'yes';
                $_SESSION['is_admin'] = $rst['is_admin'];
                #$_SESSION['username'] = $rst['username'];
                //判断是否保存登陆信息
                //如果保存就存储到cookie中
                $remember = true;#isset($_POST['remember']) ? true : false;
                if ($remember) {
                    //存cookie
                    //用户id  用户密码
                    setcookie('admin_id', $rst['username'], time() + 3600);
                    setcookie('admin_pass', $rst['password'], time() + 3600);
                    setcookie('is_admin', $rst['is_admin'], time() + 3600);
                }

                $msg = '登陆成功';
                $times = 3;
                $url = 'index.php?p=back&c=User&a=index';
                $this->redirect($url, $msg, $times);
            } else {
                $msg = '登陆失败';
                $times = 3;
                $url = 'index.php?p=back&c=User&a=login';
                $this->redirect($url, $msg, $times);
            }
        }


        require VIEW_PATH . 'login.html';
    }

    public function logout()
    {
        unset($_COOKIE);
        SessionDbTool::getInstance();
        SessionDbTool::sess_destroy(session_id());

        $this->redirect(FunctionTool::url('back', 'user', 'login'), '', 0);
    }

    public function index()
    {
        require VIEW_PATH . 'index.html';
    }

}