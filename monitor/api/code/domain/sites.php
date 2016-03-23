<?php

namespace code\domain;

class sites
{
    
    public function get($id = 0, $with_secred_data = true){
        $db = \code\core\database::getInstance();
        
        if($id){
            $db->where ("id", $id);
        }
        
        $cols = Array ("id", "name", "url");
        if ($with_secred_data){
            $cols[] = "client_url";
            $cols[] = "client_password";
        }
        return $db->get ("sites", null, $cols);
    }


    public function insert($data){
        $db = \code\core\database::getInstance();
        $db->insert ('sites', $data); 
    }
    
    public function update($id, $data){
        $db = \code\core\database::getInstance();
        $db->where ('id', $id);
        $db->update ('sites', $data); 
    }
    
    public function delete($id){
        $db = \code\core\database::getInstance();
        $db->where('id', $id);
        $db->delete('sites');
    }
    
}
