<?php

namespace code;

use code\entity\response;
use \code\core\session;

class api {
    
    private $response;
    private $allow_logout_actions = array(
        "user-login",
        "install-index"
    );


    public function __construct(){
        $this->response = new response();
        $this->setDatabaseConnection();
    }
    
    private function setDatabaseConnection(){
        $config = new \data\config();
        $database = new \code\core\database($config->db);
        $database->connect();
    }


    private function check_login($controller_name,$action_name){
        $session = session::getInstance();
        if ($session->get("user.login", false)){
            return true;
        }
        
        if(in_array($controller_name.'-'.$action_name, $this->allow_logout_actions)){
            return true;
        }
        
        return false;
    }

    public function run($path){
        $controller_name = "";
        $action_name = "";
        
        $path_split = explode("/", $path);
        
        if (!empty($path_split[0])){
            $controller_name = strtolower($path_split[0]);
        }
        
        if (!empty($path_split[1])){
            $action_name = strtolower($path_split[1]);
        } else {
            $action_name = "index";
        }
        
        if(!$controller_name){
            $this->set404("No Controller Name");
            return;
        }
        
        if(!$this->check_login($controller_name, $action_name)){
            $redirectAction = new \code\entity\functionCall();
            $redirectAction->functionName = "app.library.redirect";
            $redirectAction->parameters[] = "#login";
            $this->response->functionCalls[] = $redirectAction;
            return;
        }
        
        
        $classname = '\\code\\controller\\'.$controller_name.'Controller';
        
        if(!class_exists($classname)){
            $this->set404("class {$classname} not exists");
            return;
        }
        
        $myclass = new $classname();
        
        $action_name .= "Action";
        
        if(!method_exists($myclass,$action_name)){
            $this->set404("Action {$action_name} not exists");
            return;
        }
        
        $Actionvalue = $myclass->$action_name();
        
        if (!$Actionvalue instanceof response) {
            $this->set404("Action result must by of type response");
            return;
        };
        
        $this->response = $Actionvalue;     
    }
    
    private function set404($message){
        $this->response->success = false;
        $this->response->message = "404: {$message}";
    }


    public function display(){
        //ob_clean();
        header('content-type: application/json; charset=utf-8');
        echo json_encode($this->response);
    }
    
}