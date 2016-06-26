<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/23
 * Time: 17:43
 */
class MemberModel extends Model{
    protected function tableName() {
        return 'members';
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
    public function getPageList($page, $size) {
        $offset = ($page - 1) * $size;
//        $count = $this->db->fetchColumn('select count(*) from ' . $this->table()); //获取记录总数
        $count = $this->db->from($this->table())->count(); //获取记录总数
        $rows = $this->db->from($this->table())->limit($size, $offset)->query(); //获取当页的记录
        return array('rows' => $rows, 'count' => $count);
    }

    /**
     * 验证用户提交的密码是否匹配.
     * 匹配返回true 否则，返回false
     */
    public function check($username, $password) {
        // $sql = 'select admin_id,admin_name,admin_pass,salt ';
        $rst = $this->db->select('*')->from($this->table())->where("username='$username'")->find();
        if ($this->mcrypt($password) == $rst['password']) {
            return $rst;
        } else {
            return false;
        }
    }

    /**
     * 通过cookie数据进行验证。
     * 由于cookie的密码是加盐加密后的，所以无需再次调用加盐算法。
     */
    public function checkByCookie($user_id, $password) {
        // $sql = 'select admin_id,admin_name,admin_pass,salt ';
        $rst = $this->db->select('password')->from($this->table())->where("user_id='$user_id'")->find();
        return $password == $rst['password'];
    }

    //获取加密结果
    public function mcrypt($str) {
        return md5($str);
    }

    public function getUserById($user_id,$key=null)
    {
        $user = $this->getByPk($user_id);
        if($key) return  $user[$key];
        return $user;
    }
    public static function getMembers($toHash=false)
    {
        $model = new self;
        $members = $model->getAll('is_admin=0');
        if($toHash){
            $result = array();
            foreach($members as $member)
            {
                $result[$member['member_id']]=$member['realname'];
            }
            return $result;
        }else{
            return $members;
        }

    }
}