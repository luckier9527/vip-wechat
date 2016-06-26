<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/24
 * Time: 9:14
 */
class RankController extends Controller{
    public $title = '排行榜';
    /**
     *
     */
    public function index()
    {
        $model = new HistoryModel();
        $ranks = $model->rank();
        $usermodel = new UserModel();
        $rank_name = '充值排行';
        require VIEW_PATH . 'index.html';
    }
    public function index2()
    {
        $rank_name = '消费排行';
        $model = new HistoryModel();
        $ranks = $model->rank2();
        $usermodel = new UserModel();
        require VIEW_PATH . 'index.html';
    }
    public function index3()
    {
        $model = new HistoryModel();
        $ranks = $model->rank3();
        $usermodel = new MemberModel();
        require VIEW_PATH . 'index3.html';
    }

}