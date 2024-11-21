<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 11:30 AM
 */

use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Support\Traits\ApiTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class DepositTransaction extends BaseTransaction implements BankTransactionInterface{
    use ApiTrait;
    public function __construct($amount){
        parent::validateAmount($amount);
        $this->amount = $amount;
    }
    function applyTransaction(BackAccountInterface $account) : float{
        parent::validateAmount(amount: $this->amount);
        if($this->detectFraud($this)){
            throw new FailedTransactionException('The transaction has been blocked.');
        }
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
