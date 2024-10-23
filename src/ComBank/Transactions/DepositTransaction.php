<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 11:30 AM
 */

use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class DepositTransaction extends BaseTransaction implements BankTransactionInterface{
    
    public function __construct($amount){
        parent::validateAmount($amount);
        $this->amount = $amount;
    }
    function applyTransaction(BackAccountInterface $account) : float{
        parent::validateAmount(amount: $this->amount);
        $newBalance = $account->getBalance() + $this->amount; //Sumamos el saldo.
        return $newBalance; //Retornamos el valor sumado.
    }
    
    function getTransactionInfo() : string{
        return "DEPOSIT_TRANSACTION";
    }
    function getAmount() : float{
        return $this->amount;
    }
}
