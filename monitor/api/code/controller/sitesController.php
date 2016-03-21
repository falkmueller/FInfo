<?php

namespace code\controller;

use code\entity\response;

class sitesController {
    
    public function getAction(){
        $response =  new response();
        
        $sitesDomain = new \code\domain\sites();
        if(!empty($_GET["id"]) && intval($_GET["id"]) > 0){
            $response->data = $sitesDomain->get(intval($_GET["id"]));
        } else {
            $response->data = $sitesDomain->get();
        }
        
        return $response;
    }
    
    public function saveAction(){
        $response =  new response();
        
        if(empty($_POST["name"]) || empty($_POST["url"]) || empty($_POST["client_url"]) || empty($_POST["client_password"])){
            $response->success = false;
            $response->message = "Bitte füllen Sie alle Felder aus";
            return $response;
        }
        
        $data = array(
            "name" => $_POST["name"],
            "url" => $_POST["url"],
            "client_url" => $_POST["client_url"],
            "client_password" => $_POST["client_password"],
        );
        
        $sitesDomain = new \code\domain\sites();
        
        if(!empty($_POST["id"]) && intval($_POST["id"]) > 0){
            $sitesDomain->update(intval($_POST["id"]), $data);
        } else {
            $sitesDomain->insert($data);
        }
        
        return $response;
    }
    
    public function deleteAction(){
        $response =  new response();
        
        $sitesDomain = new \code\domain\sites();
        
        if(!empty($_GET["id"]) && intval($_GET["id"]) > 0){
            $sitesDomain->delete(intval($_GET["id"]));
        } else {
            $response->success = false;
            $response->message = "Keine Id übergeben";
        }
        
        return $response;
    }
}
