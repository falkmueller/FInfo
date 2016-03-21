<?php

class crypter {
    
    private $_key;
    private $_slug;

    public function __contruct($key, $slug = "slug"){
        $this->_key = $key;
        $this->_slug = $slug;
    }

    public function encryptAES($_str){
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB); 
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); 
        return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->_key, $this->_slug.$_str, MCRYPT_MODE_ECB, $iv)); 
    }

    public function decryptAES($_str){
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB); 
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); 
        return substr(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->_key, hex2bin($_str), MCRYPT_MODE_ECB, $iv),strlen($this->_slug));
    }
}

class response {
    public $success = true;
    public $message = "";
    public $data = null;
}

class app {
    
    private $settings;
    private $response;


    public function __construct($settings){
        $this->settings = $settings;
        $this->response = new response();
    }
    
    public function check($hash){
        if(empty($this->settings["password"])){
            return true;
        }
        
        $cripter = new crypter($this->settings["password"]);
        
        $timestamp = $cripter->decryptAES($hash);
        
        $current_timestamp = time();
        if(intval($timestamp) > 0 && abs($current_timestamp - $timestamp) < 60 * 3 ){
            return true;
        }
        
        $this->response->success = false;
        $this->response->message = "Authendifizierung inkorrect";
        
        return false;
    }
    
    public function output(){
        header('content-type: application/json; charset=utf-8');
        header("access-control-allow-origin: *");
        
        if(!empty($_GET['callback'])){
            echo strtoident($_GET['callback']) . '('.json_encode($this->response).')';
        } else {
            echo json_encode($this->response); 
        }
    }


    public function run($request){
        $linfo = new \Linfo\Linfo($settings);
        $parser = $linfo->getParser();

        if(empty($_REQUEST["type"]) || $_REQUEST["type"] == "basics"){
            $data["cpu"] = $parser->getCPU();
            $data["ram"] = $parser->getRam();
            $data["hd"] = $parser->getHD();
            $data["mount"] = $parser->getMounts();
        }
        
        if(empty($_REQUEST["type"])){
            $data["upTime"] = $parser->getUpTime();
            $data["Temp"] = $parser->getTemps();
            $data["load"] = $parser->getLoad();
            $data["net"] = $parser->getNet();
            $data["process"] = $parser->getProcessStats();
            $data["distro"] = $parser->getDistro();
            $data["model"] = $parser->getModel();
            $data["ip"] = $parser->getAccessedIP();
            $data["phpversion"] = $parser->getPhpVersion();
            $data["op"] = $parser->getOS();
            $data["cpu_architecture"] = $parser->getCPUArchitecture();
            $data["hostname"] = $parser->getHostName();
        }
        
        if(!empty($_REQUEST["type"]) && $_REQUEST["type"] == "php_info"){
            $data["php_ini"] = ini_get_all();
            ob_start();
            phpinfo();
            $data["php_info"] = ob_get_contents();
            ob_end_clean();
        }
        
        $cripter = new crypter($this->settings["password"]);
        
        $this->response->success = true;
        $this->response->message = "";
        $this->response->data = $cripter->encryptAES(json_encode($data));

    }
    
    private function strtoident($orig,$replace=''){
        $orig=(string)$orig;                  // ensure input is a string
        for($i=0; $i<strlen($orig); $i++){
            $o=ord($orig{$i});
            if(!(  (($o>=48) && ($o<=57))     // numbers
                || (($o>=97) && ($o<=122))    // lowercase
                || (($o>=65) && ($o<=90))     // uppercase
                || ($orig{$i}=='_')))         // underscore
                   $orig{$i}=$replace;        // check failed, use replacement
        }
        return $orig;
    }
    
}
