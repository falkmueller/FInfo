<?php

namespace code\controller;

use code\entity\response;
use \code\core\session;

class userController {
    
    public function loginAction(){
        $name = $_POST["name"];
        $password = $_POST["password"];
        $session = session::getInstance();
        
        $res = new response();
        
        $db = \code\core\database::getInstance();
        $db->where ("name", $name);
        $db->where ("password", md5($password));
        $user = $db->getOne("user");
        
        if ($user){
            $session->put("user.login", true);
            $session->put("user.id", $user["id"]);
            return $res;
        }
        
        $res->success = false;
        $res->message = "Das Passwort ist falsch";
        
        return $res;
    }
    
    public function logoutAction(){
        $session = session::getInstance();
        $session->destroy();
        
        $res = new response();
        return $res;
    }
    
    public function editAction(){
        $name = $_POST["name"];
        $password = $_POST["password"];
        $res = new response();
         
        $session = session::getInstance();
        $db = \code\core\database::getInstance();
        if ($password){
           $db->where ('id', $session->get("user.id", 0));
           $db->update ('user', array("password" =>  md5($password)));
        }
        
        if($name){
           $db->where ("name", $name);
           $user = $db->getOne("user"); 
           
           if ($user && $user["id"] != $session->get("user.id", 0)){
               $res->success = false;
               $res->message = "Ein anderer benutzer hat bereits diesen Namen";
               return $res;
           }
           
           $db->where ('id', $session->get("user.id", 0));
           $db->update ('user', array("name" =>  $name));
        }
        
        $res->message = "Änderungen gespeichert";
        return $res;
    }
    
}
