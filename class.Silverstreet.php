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
        $this->url= 'https://api.silverstreet.com/send.php?username='.$this->username.'&password='.$this->password.'&sender='.$this->sender.'&bodytype=4';
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
                if(strlen($number) < 9){
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
            if(strlen($numbers) < 9){
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

        $temp = $this->utf8ToUnicode($message);
        if(count($temp) > 70){
            $this->error = "Message length must be max 70 char!";
            return false;
        }

        $this->message = implode("", $temp);

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

    public function utf8ToUnicode($str) {

        $unicode = array();
        $values = array();
        $lookingFor = 1;

        for ($i = 0; $i < strlen($str); $i++) {

            $thisValue = ord($str[$i]);

            if ($thisValue < 128)
                $unicode[] = str_pad(dechex($thisValue), 4, "0", STR_PAD_LEFT);
            else {
                if (count($values) == 0) $lookingFor = ($thisValue < 224) ? 2 : 3;
                $values[] = $thisValue;
                if (count($values) == $lookingFor) {
                    $number = ($lookingFor == 3) ?
                        (($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64):
                        (($values[0] % 32) * 64) + ($values[1] % 64);
                    $number = strtoupper(dechex($number));
                    $unicode[] = str_pad($number, 4, "0", STR_PAD_LEFT);
                    $values = array();
                    $lookingFor = 1;
                }
            }
        }
        return $unicode;
    }
}