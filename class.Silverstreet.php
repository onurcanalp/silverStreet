<?php
/**
 * Silverstreet SMS Sınıfı
 * User: Onur Canalp
 * Date: 24/11/14
 */

class Silverstreet
{
    /* Temel Ayarlar */
    private $username   = 'onurcanalp';
    private $password   = 'xxxxxx';
    private $sender     = 'OnurCanalp.com';

    /* Mesajın gövdesi */
    private $url        = '';
    private $targets    = ''; //comma seperated text
    private $message    = '';

    /* Debug */
    private $error      = '';
    private $status     = false;

    public function __construct() {
        $this->url= 'https://api.silverstreet.com/send.php?username='.$this->username.'&password='.$this->password.'&sender='.$this->sender.'&bodytype=1';
    }

    public function getErrors(){
        return $this->error;
    }

    public function getStatus(){
        return $this->status;
    }

    public function setNumbers($numbers = array()){
        //numaralar
        if(is_array($numbers)){
            foreach($numbers as $key => $number){
                if(strlen($number) != 9){
                    $this->error = 'Some numbers are too short!'; // Yine de bakılmak istenirse hatayı tutalım
                    return false;
                }

                if(!ctype_digit($number)){
                    $this->error = 'Some numbers has wrong characters!';
                    return false;
                }
            }
            $this->targets = implode(",", $numbers);
            return true;
        }
        else{
            //tek numara
            if(strlen($numbers) != 9){
                $this->error = 'Some numbers are too short!'; // Yine de bakılmak istenirse hatayı tutalım
                return false;
            }

            if(!ctype_digit($numbers)){
                $this->error = 'Some numbers has wrong characters!';
                return false;
            }

            $this->targets = $numbers;
            return true;
        }
    }

    public function setMessage($message = ''){
        if($message == ""){
            $this->error = "Message is empty!";
            return false;
        }

        if(strlen($message) > 160){
            $this->error = "Message length must be max 160 char!";
            return false;
        }

        $this->message = $message;
        $this->status = true;
        return true;
    }

    public function send(){
        //hata varmı
        if(!$this->status || $this->error != ''){
            return false;
        }

        $finalUrl = $this->url.'&destination='.$this->targets.'&body='.$this->message;
        $result = file_get_contents($finalUrl);

        if($result == '01'){
            return true;
        }
        else{
            $this->error = $result;
            return false;
        }
    }
}