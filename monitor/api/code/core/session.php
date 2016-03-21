<?php

namespace code\core;

class session {
    
    protected static $_instance;
    
    public function __construct()
    {
        $this->start();
        self::$_instance = $this;
    }
    
    public static function getInstance()
    {
        if(!self::$_instance){
            new self();
        }
        return self::$_instance;
    }

    public function start()
    {
        if (session_id() === '') {
            if (!session_start()) {
                return false;  
            }
            
            if(!$this->isFingerprint()){
                $this->destroy();
                return $this->start();
            }
            
            //return mt_rand(0, 4) === 0 ? $this->refresh() : true; // 1/5
        }
        return true;
    }

    public function refresh()
    {
        return session_regenerate_id(true);
    }

    public function isFingerprint()
    {
        $hash = md5(
            $_SERVER['HTTP_USER_AGENT'] .
            (ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0'))
        );
        if (isset($_SESSION['_fingerprint'])) {
            return $_SESSION['_fingerprint'] === $hash;
        }
        $_SESSION['_fingerprint'] = $hash;
        return true;
    }
    
    public function destroy(){
        return session_destroy();
    }


    public function get($name, $default = null)
    {
        $parsed = explode('.', $name);
        $result = $_SESSION;
        while ($parsed) {
            $next = array_shift($parsed);
            if (isset($result[$next])) {
                $result = $result[$next];
            } else {
                return $default;
            }
        }
        return $result;
    }
    public function put($name, $value)
    {
        $parsed = explode('.', $name);
        $session =& $_SESSION;
        while (count($parsed) > 1) {
            $next = array_shift($parsed);
            if ( ! isset($session[$next]) || ! is_array($session[$next])) {
                $session[$next] = [];
            }
            $session =& $session[$next];
        }
        $session[array_shift($parsed)] = $value;
    }
}
