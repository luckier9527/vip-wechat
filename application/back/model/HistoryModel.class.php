<?php

class HistoryModel extends Model {

    protected function tableName() {
        return 'histories';
    }

    /**
     * 验证当前模型数据
     *
     */
    public function validate($data)
    {

        return true;

    }


    public function getPageList($page, $size,$user_id) {
        $offset = ($page - 1) * $size;
//        $count = $this->db->fetchColumn('select count(*) from ' . $this->table()); //获取记录总数
        $count = $this->db->from($this->table())->where('user_id='.$user_id)->count(); //获取记录总数
        $rows = $this->db->from($this->table())->where('user_id='.$user_id)->order('time DESC')->limit($size, $offset)->query(); //获取当页的记录
        return array('rows' => $rows, 'count' => $count);
    }

    public function rank()
    {
        $rows = $this->db->query('SELECT sum(amount) as total,user_id FROM '.$this->table().' WHERE type="increase" GROUP BY user_id ORDER BY total DESC LIMIT 3');
        return $rows;
    }
    public function rank2()
    {
        $rows = $this->db->query('SELECT sum(amount) as total,user_id FROM '.$this->table().' WHERE type="reduce" GROUP BY user_id ORDER BY total ASC  LIMIT 3');
        return $rows;
    }
    public function rank3()
    {
        $rows = $this->db->query('SELECT count(member_id) as total,member_id FROM '.$this->table().' WHERE type="reduce" GROUP BY member_id ORDER BY total DESC LIMIT 3');
        return $rows;
    }

}
