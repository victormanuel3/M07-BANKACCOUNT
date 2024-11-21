<?php
namespace ComBank\Persons;

use ComBank\Exceptions\PersonException;
use ComBank\Support\Traits\ApiTrait;

Class Person {
    use ApiTrait;    
    private $name;
    private $idCard;
    private $email;
    private $zip_code;

    public function __construct($name, $idCard, $email, $zip_code){
        $this->name = $name;
        $this->idCard = $idCard;
        $this->email = $email;
        $this->zip_code = $zip_code;
    }

    public function getEmailValidation(){
        $email = $this->email;
        pl("Validating email: ".$email);
        if ($this->validateEmail($email)){
            pl("Email is valid.");
        }else {
            throw new PersonException("Error: Invalid email addresses: $email");
        }
    }

    public function getName(){
        return $this->name;
    }

    public function getIdCard(){
        return $this->idCard;
    }
    public function getEmail(){
        return $this->email;
    }
    public function setEmail($email){
        $this->email = $email;
    }
    public function getZip_code(){
        return $this->zip_code;
    }
}