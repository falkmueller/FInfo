<?php

namespace code\controller;

use code\entity\response;

class statController {
    
    public function pingAction(){
        $response =  new response();
        
        $id = intval($_GET["id"]);
        $clientDomain = new \code\domain\client();
        $sitesDomain = new \code\domain\sites();
        $site_data = $sitesDomain->get($id);
        if(!$site_data || count($site_data) == 0){
            return $response;
        }
        $response->data = $clientDomain->pingPage($site_data[0]);
        
        return $response;
    }
    
    public function basicsAction(){
        $response =  new response();
        
        $id = intval($_GET["id"]);
        $clientDomain = new \code\domain\client();
        $sitesDomain = new \code\domain\sites();
        $site_data = $sitesDomain->get($id);
        if(!$site_data || count($site_data) == 0){
            return $response;
        }
        $response->data = $clientDomain->getBasics($site_data[0]);
        
        return $response;
    }
    
    public function fetchBasics(){
        $sitesDomain = new \code\domain\sites();
        $clientDomain = new \code\domain\client();
        $sites = $sitesDomain->get();
        
        foreach ($sites AS $site_data){
           $clientDomain->getBasics($site_data);
        }
        
        $response =  new response();
        return $response;
    }
    
}
