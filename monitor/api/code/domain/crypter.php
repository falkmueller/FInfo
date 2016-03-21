<?php

namespace code\domain;

class crypter
{
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
