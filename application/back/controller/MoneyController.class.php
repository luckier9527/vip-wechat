<?php
/**
 * 用户充值消费管理控制器

 */
class MoneyController extends AdminController {
    public $title="充值和消费";
    //用户信息显示页面
    public function index()
    {
        $user_id = isset($_GET['user_id']) ? $_GET['user_id']:0;
        $usermodel = new UserModel();
        $user = $usermodel->getByPk($user_id);
        if($user==false){
            $url = 'index.php?p=back&c=account&a=index';
            $this->redirect($url,'用户不存在');
        }
        $plans = PlanModel::getPlans(true);
        $members = MemberModel::getMembers(true);

        require VIEW_PATH . 'index.html';
    }

    /**
     * 给用户充值
     */
    public function increase()
    {
        if($_POST){
            $usermodel = new UserModel();
            $user = $usermodel->getByPk($_POST['user_id']);


            if($user==false){
                $url = 'index.php?p=back&c=account&a=index';
                $this->redirect($url,'参数错误');
            }
            $amount = floatval($_POST['amount']);

            $historymodel = new HistoryModel();
            $history = $_POST;
            //充500送100，充1000送300,充5000送2000
            if($amount >= 500){
                if($amount >= 5000){
                    $amount += 2000;
                }elseif($amount >= 1000){
                    $amount += 300;
                }else{
                    $amount += 100;
                }
            }
            $history['type'] = 'increase';
            $history['amount'] =  $amount;
            $history['time'] = time();
            $history['remainder'] = $user['money'] + $amount;
            $id = $historymodel->insertData($history);
            if($id){
                //充值5000成为vip
                if($amount >= 5000 && $user['is_vip'] == 0){
                    $usermodel->updateData(array('money'=>$history['remainder'],'is_vip'=>1),'user_id='.$user['user_id']);
                }else{
                    $usermodel->updateData(array('money'=>$history['remainder']),'user_id='.$user['user_id']);
                }

                $url = 'index.php?p=back&c=money&a=history&user_id='.$user['user_id'];
                $this->redirect($url,'充值成功');
            }else{
                $url = 'index.php?p=back&c=account&a=index';
                $this->redirect($url,'充值失败');
            }
        }
    }
    public function reduce()
    {
        if($_POST){
            $usermodel = new UserModel();
            $user = $usermodel->getByPk($_POST['user_id']);
            $planmodel = new PlanModel();
            $plan = $planmodel->getByPk($_POST['plan_id']);
            if($user==false || $plan == false){
                $url = 'index.php?p=back&c=account&a=index';
                $this->redirect($url,'参数错误');
            }
            $amount = -$plan['money'];
            //vip用户5折
            if($user['is_vip']) $amount *= 0.5;
            $historymodel = new HistoryModel();
            $history = $_POST;
            $history['type'] = 'reduce';
            $history['content'] = $plan['name'];
            $history['amount'] =  $amount;
            $history['time'] = time();
            $history['remainder'] = $user['money'] + $amount;
            if($history['remainder'] < 0){
                $url = 'index.php?p=back&c=account&a=index';
                $this->redirect($url,'消费失败:用户余额不足，请先充值');
            }
            $id = $historymodel->insertData($history);
            if($id){
                $usermodel->updateData(array('money'=>$history['remainder']),'user_id='.$user['user_id']);
                $url = 'index.php?p=back&c=money&a=history&user_id='.$user['user_id'];
                $this->redirect($url,'消费成功');
            }else{
                $url = 'index.php?p=back&c=account&a=index';
                $this->redirect($url,'消费失败');
            }
        }
    }
    /**
     * 充值消费记录
     */
    public function history()
    {
        $model = new HistoryModel();
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $size = isset($_GET['size']) ? $_GET['size'] : $GLOBALS['config']['back']['page_size'];
        $user_id = isset($_GET['user_id']) ? $_GET['user_id']:0;
        $usermodel = new UserModel();
        $user = $usermodel->getByPk($user_id);
        if($user==false){
            $url = FunctionTool::url('back','account','index');
            $this->redirect($url,'用户不存在');
        }
        $rst = $model->getPageList($page, $size,$user['user_id']);
        $rows = $rst['rows'];
        $count = $rst['count'];
        $page_tool = new PageTool();
        $page_html = $page_tool->page($count, $size, $page, FunctionTool::url('back','money','history',array('user_id'=>$user['user_id'])));

        require VIEW_PATH . 'history.html';
    }

}