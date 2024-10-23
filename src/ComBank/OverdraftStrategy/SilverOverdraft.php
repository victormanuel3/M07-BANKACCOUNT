<?php namespace ComBank\OverdraftStrategy;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:39 PM
 */

/**
 * @description: Grant 100.00 overdraft funds.
 * */
class SilverOverdraft implements  OverdraftInterface{
    public function isGrantOverdraftFunds($float) : bool{
        return $float + $this->getOverdraftFundsAmount() >= 0; //Verificamos si la cantidad restada al saldo me devuelve un saldo de -100.
    }

    public function getOverdraftFundsAmount() : float{
        return 100.0;
    }
    
}
