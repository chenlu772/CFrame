<?php
/**
 * Created by PhpStorm.
 * User: chen
 * Date: 2016/8/10
 * Time: 15:08
 */

class Model{

    protected $db;
    protected $table;
    protected $fields = array();

    public function __construct($table){

        $dbconfig['host'] = $GLOBALS['config']['host'];
        $dbconfig['user'] = $GLOBALS['config']['user'];
        $dbconfig['password'] = $GLOBALS['config']['password'];
        $dbconfig['dbname'] = $GLOBALS['config']['dbname'];
        $dbconfig['port'] = $GLOBALS['config']['port'];
        $dbconfig['charset'] = $GLOBALS['config']['charset'];

        $this->db = new Mysql($dbconfig);
        $this->table = $GLOBALS['config']['prefix'] . $table;
        $this->getFields();
    }

    /*
     * Get the list of table fields
     * */
    private function getFields(){

        $sql = "DESC ". $this->table;
        $result = $this->db->getAll($sql);

        foreach ($result as $v) {

            $this->fields[] = $v['Field'];

            if ($v['Key'] == 'PRI') {

                // If there is PK, save it in $pk
                $pk = $v['Field'];

            }

        }

        // If there is PK, add it into fields list
        if (isset($pk)) {

            $this->fields['pk'] = $pk;

        }
    }

    /*
     * @param $list array associative array
     * @return mixed If succeed return inserted record id, else return false
     * */
    public function insert($list){
        $field_list = '';
        $value_list = '';
        foreach($list as $k => $v){
            if(in_array($k, $this->fields)){
                $field_list .= "`".$k."`" . ',';
                $value_list .= "'".$v."'" . ',';
            }
        }

        $field_list = rtrim($field_list, ',');
        $value_list = rtrim($value_list, ',');

        $sql = "INSERT INTO `{$this->table}` ({$field_list}) VALUES ($value_list)";
        if($this->db->query($sql)){
            return $this->db->getInsertId();
        }else{
            return false;
        }

    }

    /*
     * @param $list array associative array needs to be updated
     * @return mixed If succeed return the count of affected rows, else return false
     * */
    public function update($list){
        $uplist = '';
        $where = 0;
        foreach($list as $k => $v){
            if(in_array($k, $this->fields)){
                if($k == $this->fields['pk']){
                    $where = "`$k`=$v";
                }else{
                    $uplist .= "`$k`='$v'".",";
                }
            }
        }

        $uplist = rtrim($uplist, ',');
        $sql = "UPDATE `{$this->table}` SET {$uplist} WHERE {$where}";

        if($this->db->query($sql)){
            if($rows = mysql_affected_rows()){
                return $rows;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    /*
     * @param $pk mixed could be an int or an array
     * @return mixed if succeed, return the count of deleted records, if fail, return false
     * */
    public function delete($pk){
        $where = 0;
        if(is_array($pk)){
            $where = "`{$this->fields['pk']}` in (".implode(',', $pk).")";
        }else{
            $where = "`{$this->fields['pk']}`=$pk";
        }

        $sql = "DELETE FROM `{$this->table}` WHERE $where ";
        if($this->db->query($sql)){
            if($rows = mysql_affected_rows()){
                // has count of affected rows
                return $rows;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /*
     * @param $pk int primary key
     * @return array an array of single record
     * */
    public function selectByPk($pk){
        $sql = "select * from `{$this->table}` WHERE `{$this->fields['pk']}` = $pk ";
        return $this->db->getRow($sql);
    }

    public function total(){
        $sql = "select count(*) from {$this->table}";
        return $this->db->getOne($sql);
    }

    /*
     * @param $offset int offset value
     * @param $limit int number of records of each fetch
     * @param $where string where condition, default is empty
     * */
    public function pageRows($offset, $limit, $where = ''){
        if(empty($where)){
            $sql = "select * from {$this->table} limit $offset, $limit";
        }else{
            $sql = "select * from {$this->table} where $where limit $offset, $limit";
        }

        return $this->db->getAll($sql);
    }
}