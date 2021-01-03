<?php


class PasswordCreator
{
    const LEVEL_EASY = 'easy';
    const LEVEL_MEDIUM = 'medium';
    const LEVEL_HARD = 'hard';
    private $type = "hard";
    private $limit = 7;
    private $hasSpecialCharacters = false;
    private $easyWords = "cow, dog, fish, fly, moon, fox ,bird, car, milk, apple, orange, house, cat, hat, mat, bat";

    public function __construct($type, $limit)
    {
        $this->type = $type;
        $this->limit = $limit;

    }

    public function setSpecialCharacters($val){
        $this->hasSpecialCharacters = $val;
    }

    public function generate(){
        if($this->type == self::LEVEL_EASY){
            return $this->createEasyPassword();
        }else if($this->type == self::LEVEL_MEDIUM){
            return $this->createMediumPassword();
        }else if($this->type == self::LEVEL_HARD){
            return $this->createHardPassword();
        }
        return "An error occurred";
    }

    private function createEasyPassword(){
        $pass = "";
        $word = $this->getRandomEasyWord();
        $number = $this->getRandomNumber();
        $count = strlen($word)-1;
        if($count < $this->limit){
            $diff = $this->limit - $count;
            $range = $this->createMaxAndMin($diff);
            $number = $this->getRandomNumber($range['min'], $range['max']);
        }
        if($this->hasSpecialCharacters){
            $word = $this->addSpecialCharacters($word);
        }
        $pass =  $word . $number;
        return $pass;
    }

    private function addSpecialCharacters($word){
        $max = 1;
        $check = 0;
        $tags = ['a','i','o','s'];
        $replace = ['@','!','&','$'];
        $output = $word;
        for ($i=0; $i<=count($tags)-1; $i++){
            if($check >= $max){
                break;
            }
            if(strpos($word, $tags[$i]) !== false){
                $output = str_replace($tags[$i], $replace[$i], $word);
                $check++;
            }
        }
        return $output;
    }


    private function createMediumPassword(){
        return $this->randomString($this->limit);
    }

    private function createHardPassword(){
        return $this->randomString($this->limit);
    }

    private function randomString($len=30){
        $string = '';
        if($this->type == self::LEVEL_HARD){
            if($this->hasSpecialCharacters){
                $characters = 'abcdefghijklmnopqrstuvwxyz0123456789!@#_*$()ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            }else{
                $characters = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            }
        }else{
            if($this->hasSpecialCharacters){
                $characters = 'abcdefghijklmnopqrstuvwxyz0123456789!@#_*$()';
            }else{
                $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            }
        }
        $max = strlen($characters) - 1;
        for ($i = 0; $i < $len; $i++) {
            $string .= $characters[mt_rand(0, $max)];
        }
        return $string;
    }



    private function createMaxAndMin($limit){
        $max = "9";
        $min = "1";
        for($i=0; $i<= $limit -2; $i++){
            $max = $max . '9';
            $min =  $min . '0';
        }
        return [
            'max' => $max,
            'min' => $min
        ];
    }

    private function getRandomEasyWord(){
        $ewArray = explode(',', $this->easyWords);
        $key = array_rand($ewArray, 1);
        return trim($ewArray[$key]);
    }

    private function getRandomNumber($min='10000', $max='99999'){
        return rand($min, $max);
    }

}