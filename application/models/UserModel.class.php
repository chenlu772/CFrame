<?php
/**
 * Created by PhpStorm.
 * User: chen
 * Date: 2016/8/10
 * Time: 16:23
 */

class UserModel extends Model{

    public function getUsers(){

        $sql = "select * from $this->table";
        $users = $this->db->getAll($sql);
        return $users;
    }

}