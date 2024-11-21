<?php
namespace ComBank\Support\Traits;

use ComBank\Exceptions\PersonException;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use PhpParser\Node\Stmt\Foreach_;

trait ApiTrait {
    function validateEmail(String $email): bool{
        $ch = curl_init();
        $api = "https://api.usercheck.com/email/$email?key=Y0eP8XwFZ9sikxZo9QokBKOfHPx3ltkN";
       
        curl_setopt($ch, CURLOPT_URL, $api);
            
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            //CURLOPT_HTTPHEADER => 'fxf_ONzIVnUTtxpeFL6XbKrm', //Api key
            CURLOPT_SSL_VERIFYPEER => false,
        ));
    
        $result = curl_exec($ch);
        $json = json_decode($result);
            
        curl_close($ch);
    
        if (isset($json->mx) && isset($json->disposable)){
            if ($json->mx && !$json->disposable){
                return true;
            }else {
                return false;
            }
        }else{
            return false;
        }

    }

    function convertBalance(Float $amountInEuros): float
    {
        $ch = curl_init();
        $api = "https://api.fxfeed.io/v1/convert?api_key=fxf_ONzIVnUTtxpeFL6XbKrm&from=EUR&to=USD&amount=$amountInEuros";
   
        curl_setopt($ch, CURLOPT_URL, $api);
        
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            //CURLOPT_HTTPHEADER => 'fxf_ONzIVnUTtxpeFL6XbKrm', //Api key
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        
        $result = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($result);

        return $json->result;

    }

    
    function detectFraud(BankTransactionInterface $bankTransactionInterface): bool
    {
        $ch = curl_init();
        $api = "https://67378e384eb22e24fca587e8.mockapi.io/Fraud";
   
        curl_setopt($ch, CURLOPT_URL, $api);
        
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            //CURLOPT_HTTPHEADER => 'fxf_ONzIVnUTtxpeFL6XbKrm', //Api key
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $result = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($result,true);
        $amount = $bankTransactionInterface->getAmount();

        $fraud = false;
        foreach($json as $key => $value){
            if($json[$key]["movementType"]==$bankTransactionInterface->getTransactionInfo()){
                if ($amount >= $json[$key]["amountMin"] && $amount <= $json[$key]["amountMax"]){
                    if($json[$key]["action"]==BankTransactionInterface::TRANSACTION_BLOCKED){
                        return $fraud = true;
                    }else {
                        return $fraud = false;
                    }
                }
            }
        }
        return $fraud;
    }

    function maintenance_rate(int $zip_code){
        $ch = curl_init();
        $api = "https://api.api-ninjas.com/v1/salestax?zip_code=$zip_code";

        curl_setopt($ch, CURLOPT_URL, $api);

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "X-Api-Key: h2obBiMKONHqkvIRmXzOQQ==VUxB5zRoxfrjiQ24"],
            CURLOPT_SSL_VERIFYPEER => false,
        ));

        $result = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($result,true);
        if (isset($json[0]['total_rate'])){
            return $json[0]['total_rate'];
        }else {
            throw new PersonException("There is no tax rate for the postcode $zip_code.");
        }
    }
}