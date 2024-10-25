<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:22 PM
 */

use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class WithdrawTransaction extends BaseTransaction implements BankTransactionInterface{
    public function __construct($amount){
        parent::validateAmount(amount: $amount);
        $this->amount = $amount;
    }
    function applyTransaction(BackAccountInterface $account) : float{
        parent::validateAmount(amount: $this->amount);
        $newBalance = $account->getBalance() - $this->amount; //Realizamos una resta del saldo.
        $overdraft = $account->getOverdraft()->isGrantOverdraftFunds($newBalance); //Verificamos si la resta del saldo es menor al límite.
        if ($newBalance < 0) { //En caso de que la resta sea menor a 0 ya no verificamos el NoVerdraft sino el silver directamente
            if ($overdraft) { //Overdraft me devuelve true o false en caso de que al sumarle el límite al saldo final sigue estando en negativo o al ras.
                return $newBalance;
            }else {
                throw new InvalidOverdraftFundsException("Insufficient balance to complete the withdrawal.");//Excepción: si está en negativo es porque no se puede restar.
            }
        }else {
            return $newBalance;//Si es mayor que 0 no hay nada que verificar devolvemos directamente el sobregiro.
        }
    }
    
    function getTransactionInfo() : string{
        return "WITHDRAW_TRANSACTION";
    }
    function getAmount() : float{
        return $this->amount;
    }
}
