<?php
class AdminController extends Controller{
	public function __construct(){
		parent::__construct();
		$this->checkLogin();
		
	}

	//此方法用于后台的验证，验证用户是否登陆。
	private function checkLogin(){
		//过滤，如果是Admin控制器
		//A无需登陆
		//所有的叫做b的方法都可以外部访问
		$ignore = array(
			'controller'=>array(),
			'action'=>array(),
		);
		if(!in_array(CONTROLLER,$ignore['controller']) && !in_array(ACTION, $ignore['action'])){
			//此变量表示当前访问是否是合法的。
			$flag = false;
			//如果有cookie信息，就获取，用于没有session标识，验证用户合法性。
			$admin_id =isset($_COOKIE['admin_id'])?$_COOKIE['admin_id']:null;
			$admin_pass =isset($_COOKIE['admin_pass'])?$_COOKIE['admin_pass']:null;
			//如果session中有is_login标识，并且其值为yes才表示合法
			// session_start();
			SessionDbTool::getInstance();
			if(isset($_SESSION['is_login']) && $_SESSION['is_login']=='yes'){
				#$flag = true;
				$flag = $_SESSION['is_admin'];
				/*if(!$_SESSION['is_admin']){
					$this->redirect('index.php?p=back&c=User&a=login','请使用管理员账户登录',3);
				}*/
			}elseif($admin_id && $admin_pass){
				$model = new MemberModel();
				$rst = $model->checkByCookie($admin_id,$admin_pass);
				// var_dump($rst);
				//验证通过，保存登陆标识到session中
				if($rst){
					//$flag = true;
					$_SESSION['is_login'] = 'yes';
					$_SESSION['is_admin'] = $rst['is_admin'];
					$flag = $_SESSION['is_admin'];
				}
			}
			if(!$flag){
				$this->redirect('index.php?p=back&c=User&a=login','请先登录',3);

			}
		}
	}
}