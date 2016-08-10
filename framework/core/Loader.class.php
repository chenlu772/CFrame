<?php
/**
 * Created by PhpStorm.
 * User: chen
 * Date: 2016/8/10
 * Time: 13:34
 */

class Loader{
    // Load library classes
    public function library($lib){
        include LIB_PATH . "$lib.class.php";
    }

    // loader helper functions. Naming conversion is XXX_helper.php
    public function helper($helper){
        include HELPER_PATH . "{$helper}_helper.php";
    }

}