<?php

namespace code\domain;

class client
{
    public function pingPage($site_data){
        $url = $site_data["url"];
        
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 10); 
            curl_setopt($ch, CURLOPT_TIMEOUT , 10); 
            $content = curl_exec($ch);

            if (!empty($content))
            {
                $returnvalue = array("available" => true );

                $dom = new \DOMDocument();
                @$dom->loadHTML($content);
                $xpath= new \DOMXPath($dom);

                $query = $xpath->query('//title');
                if($query->length > 0){
                    $returnvalue["title"] = $query->item(0)->textContent;
                }


                $query = $xpath->query('//link[@rel="apple-touch-icon-precomposed"]');
                $query = ($query->length == 0 ? $xpath->query('//meta[@itemprop="image"]') : $query);
                $query = ($query->length == 0 ? $xpath->query('//link[@rel="shortcut icon"]') : $query);
                $query = ($query->length == 0 ? $xpath->query('//link[@rel="icon"]') : $query);

                if($query->length > 0){
                    $returnvalue["icon"] = $query->item(0)->getAttribute("href");
                    if(!$returnvalue["icon"]){
                        $returnvalue["icon"] = $query->item(0)->getAttribute("content");
                    }
                    $returnvalue["icon"] = trim($url, "/").'/'.trim($returnvalue["icon"], "/");
                }
                 
                return $returnvalue;
            }
        } catch (\Exception $exc) {

        }

        return null;
    }
    
    public function getBasics($site_data){
        
        return $this->callClient($site_data["client_url"], $site_data["client_password"], "basics");
        
    }
    
    private function callClient($client_url, $client_password, $type){
        $crypterDomain = new \code\domain\crypter($client_password);
        
        $timestamp = time();
        $hash = $crypterDomain->encryptAES($timestamp);
        
        $url = $client_url.'?hash='.$hash.'&type='.$type;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 10); 
        curl_setopt($ch, CURLOPT_TIMEOUT , 10); 
        $content = curl_exec($ch);
       
        if(!$content){
            return null;
        }
        
        $content = json_decode($content, true);
        
        if(empty($content["success"]) || !$content["success"] || empty($content["data"])){
            return null;
        }
        
        $content = $content["data"];
        $content = $crypterDomain->decryptAES($content);
        
        return json_decode($content, true);

    }
    
}
