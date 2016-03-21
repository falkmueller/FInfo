<?php

namespace code\domain;

class client
{
    public function pingPage($url){
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
                $query = ($query->length == 0 ? $xpath->query('//link[@rel="shortcut icon"]') : $query);
                $query = ($query->length == 0 ? $xpath->query('//link[@rel="icon"]') : $query);

                if($query->length > 0){
                    $returnvalue["icon"] = $query->item(0)->getAttribute("href");
                    $returnvalue["icon"] = trim($url, "/").'/'.trim($returnvalue["icon"], "/");
                }
                 
                return $returnvalue;
            }
        } catch (\Exception $exc) {

        }

        return null;
    }
}
