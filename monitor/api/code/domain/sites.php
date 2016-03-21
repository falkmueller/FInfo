<?php

namespace code\domain;

class sites
{
    
    public function get($id = 0){
        $db = \code\core\database::getInstance();
        
        if($id){
            $db->where ("id", $id);
        }
        
        return $db->get ("sites");
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
