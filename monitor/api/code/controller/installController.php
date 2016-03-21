<?php

namespace code\controller;

use code\entity\response;
use \code\core\session;

class installController {
    
    public function indexAction(){
        $database = \code\core\database::getInstance();
        $prefix = \code\core\database::$prefix;
        
        $query = "CREATE TABLE IF NOT EXISTS {$prefix}user ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(250) NOT NULL , `password` VARCHAR(250) NOT NULL , PRIMARY KEY (`id`) ) ENGINE = InnoDB;";
        $database->query($query);
        
        $database->where ("name", "admin");
        $user = $database->getOne ("user");
        if(!$user){
            $database->insert ('user', array("name" => "admin", "password" => md5("test123"))); 
        }
        
        $query = "CREATE TABLE {$prefix}sites ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(250) NOT NULL , `url` VARCHAR(255) NOT NULL , `client_url` VARCHAR(255) NOT NULL , `client_password` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`) ) ENGINE = InnoDB;";
        $database->query($query);
        
      $res = new response();
      return $res;
    }
    
}
