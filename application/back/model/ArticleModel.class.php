<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/23
 * Time: 17:43
 */
class ArticleModel extends Model{
    protected function tableName() {
        return 'article';
    }
    public function getPageList($page, $size) {
        $offset = ($page - 1) * $size;
//        $count = $this->db->fetchColumn('select count(*) from ' . $this->table()); //获取记录总数
        $count = $this->db->from($this->table())->count(); //获取记录总数
        $rows = $this->db->from($this->table())->limit($size, $offset)->query(); //获取当页的记录
        return array('rows' => $rows, 'count' => $count);
    }
    public function validate()
    {
        return true;
    }
}