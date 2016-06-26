<?php

class UserModel extends Model {

    protected function tableName() {
        return 'users';
    }

    /**
     * 验证当前模型数据
     *
     */
    public function validate($data)
    {
        if(empty($data)){
            $this->error = '数据不能为空';
            return false;
        }
        if(empty($data['username'])){
            $this->error = '用户名不能为空';
            return false;
        }
        /*if(empty($data['password'])){
            $this->error = '密码不能为空';
            return false;
        }*/
        //TODO 验证用户名唯一性
        return true;

    }

    /**
     * 根据用户名从数据库获取用户账户信息
     * @param $username
     */
    public function getByOpenid($openid){
        $user = $this->db->from($this->table())->where('openid="'.$openid.'"')->find();
        return $user;
    }
    public function getByPhone($phone){
        $user = $this->db->from($this->table())->where('telephone="'.$phone.'"')->find();
        return $user;
    }
    public function getByCode($code){
        $user = $this->db->from($this->table())->where('code="'.$code.'"')->find();
        return $user;
    }
    public function getUserById($user_id,$key=null)
    {
        $user = $this->getByPk($user_id);
        if($key) return  $user[$key];
        return $user;
    }

    public function getPageList($page, $size,$prama=array()) {
        $where = '1';
        if(!empty($prama)){
            foreach($prama as $key=>$value)
                if(strlen($value)) $where .= ' AND `'.$key.'` LIKE "%'.$value.'%"';
        }
        $offset = ($page - 1) * $size;
//        $count = $this->db->fetchColumn('select count(*) from ' . $this->table()); //获取记录总数
        $count = $this->db->from($this->table())->count(); //获取记录总数
        $rows = $this->db->from($this->table())->limit($size, $offset)->where($where)->query(); //获取当页的记录
        return array('rows' => $rows, 'count' => $count);
    }

    static function makeCode($length=6)
    {
        $num = '';
        for($i=1;$i<=$length;$i++){
            $num .= rand(0, 9);
        }
        return $num;
    }


}
