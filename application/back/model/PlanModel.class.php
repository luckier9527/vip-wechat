<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/23
 * Time: 17:43
 */
class PlanModel extends Model{

    protected function tableName() {
        return 'plans';
    }

    public function getPageList($page, $size) {
        $offset = ($page - 1) * $size;
//        $count = $this->db->fetchColumn('select count(*) from ' . $this->table()); //获取记录总数
        $count = $this->db->from($this->table())->count(); //获取记录总数
        $rows = $this->db->from($this->table())->limit($size, $offset)->query(); //获取当页的记录
        return array('rows' => $rows, 'count' => $count);
    }

    public static function getPlans($toHash=false)
    {
        $model = new self;
        $plans = $model->getAll('status=1');
        if($toHash){
            $result = array();
            foreach($plans as $plan)
            {
                $result[$plan['plan_id']]=$plan['name'].'-'.$plan['money'];
            }
            return $result;
        }else{
            return $plans;
        }

    }

    public function validate($data)
    {
        if(empty($data)){
            $this->error = '数据不能为空';
            return false;
        }
        if(empty($data['name'])){
            $this->error = '套餐名称不能为空';
            return false;
        }

        return true;

    }
}