<?php namespace ComBank\Bank;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:25 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Support\Traits\ApiTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Persons\Person;

class BankAccount extends BankAccountException implements BackAccountInterface {
    use AmountValidationTrait;
    use ApiTrait;

    protected ?Person $person;
    protected $balance;
    protected $status = true;
    protected $overdraft;
    protected $currency;

    public function __construct($balance, string $currency = 'EUR', Person $person = null){
        $this->balance = $balance;
        $this->status = BackAccountInterface ::STATUS_OPEN;
        $this->overdraft = new NoOverdraft();
        $this->currency = $currency;
        $this->person = $person;
    }
    public function calculateMaintenanceRate():float{
        $zip_code = $this->person->getZip_code();
        $totalRate = $this->maintenance_rate($zip_code);
        return round($totalRate * $this->balance, 2);
    }

    public function transaction(BankTransactionInterface $transaction) : void{
        if ($this->status == 'OPEN'){
            try {
                $this->balance = $transaction->applyTransaction($this);
            }catch(InvalidOverdraftFundsException $e){
                throw new FailedTransactionException($e->getMessage(), $e->getCode());
            }
        }else {
            throw new BankAccountException("Error: The account is already closed.");
        }
    }

    public function openAccount() : bool{
        if ($this->status == 'OPEN') {
            return true;
        }else {
            return false;
        }
    }

    public function reopenAccount() : void{
        if ($this->openAccount()){
            throw new BankAccountException(message: "Error: The account is already opened.");
        }else {
            $this->status = BackAccountInterface ::STATUS_OPEN;
            echo '<br><br>My account is reopened<br>';
        }
    }
    
    public function closeAccount() : void{
        if($this->openAccount()){
            $this->status = BackAccountInterface ::STATUS_CLOSED;
            echo '<br>My account is closed';
        }else {
            throw new BankAccountException(message: "<br>Error: The account is already closed.");
        }
    }

    public function applyOverdraft(OverdraftInterface $overdraft) : void{
        $this->overdraft = $overdraft;
    }


    //GETTERS AND SETTERS ----------------------------------------
    public function getBalance() : float{
        return $this->balance;
    }

    function getCurrency(){
        return $this->currency;
    }

    public function setBalance($float) : void{
        $this->balance = $float;
    }

    public function getOverdraft(): OverdraftInterface{
        return $this->overdraft;  
    }

    public function getPerson(){
        return $this->person;
    }

    public function setPerson($person){
        $this->person = $person;
    }
}


