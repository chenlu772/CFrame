<?php
/**
 * Created by PhpStorm.
 * User: chen
 * Date: 2016/8/10
 * Time: 13:25
 */

// Base Controller
class Controller{

    // Base Controller has a property called $loader, it is an instance of Loader class
    protected $loader;

    public function __construct(){
        $this->loader = new Loader();
    }

    public function redirect($url, $message, $wait = 0){
        if(0 == $wait){
            header("Location:$url");
        }else{
            include CURR_VIEW_PATH . "message.html";
        }

        exit;
    }

}