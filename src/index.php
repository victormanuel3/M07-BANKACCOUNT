<?php

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:24 PM
 */

use ComBank\Bank\BankAccount;
use ComBank\Bank\InternationalBankAccount;
use ComBank\Bank\NationalBankAccount;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\PersonException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Persons\Person;

require_once 'bootstrap.php';


//---[Bank account 1]---/
// create a new account1 with balance 400
pl('--------- [Start testing bank account #1, No overdraft] --------');
try {
    // show balance account
    $bankAccount1 = new BankAccount(400);
    pl('My Balance: '.$bankAccount1->getBalance());
    // close account
    $bankAccount1->closeAccount();

    // reopen account
    $bankAccount1->reopenAccount();
    // deposit +150 
    pl('Doing transaction deposit (+150) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(transaction: new DepositTransaction(150));
    pl('My new balance after deposit (+150) : ' . $bankAccount1->getBalance());
    // withdrawal -25
    pl('Doing transaction withdrawal (-25) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(25));
    pl('My new balance after withdrawal (-25) : ' . $bankAccount1->getBalance());
    
    // withdrawal -600
    pl('Doing transaction withdrawal (-600) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(600));
    
} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount1->getBalance());




//---[Bank account 2]---/
pl('--------- [Start testing bank account #2, Silver overdraft (100.0 funds)] --------');
try {
    // show balance account
    $bankAccount2 = new BankAccount(balance: 200);
    $bankAccount2->applyOverdraft(new SilverOverdraft);
    pl('My Balance: '.$bankAccount2->getBalance());

    // deposit +100
    pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(transaction: new DepositTransaction(100));

    pl('My new balance after deposit (+100) : ' . $bankAccount2->getBalance());

    // withdrawal -300
    pl('Doing transaction withdrawal (-300) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(transaction: new WithdrawTransaction(300));

    pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());

    // withdrawal -50
    pl('Doing transaction withdrawal (-50) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(transaction: new WithdrawTransaction(50));

    pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());

    // withdrawal -120
    pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(transaction: new WithdrawTransaction(120));
    
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());

try {
    pl('Doing transaction withdrawal (-20) with current balance : ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(transaction: new WithdrawTransaction(20));
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}

pl('My new balance after withdrawal (-20) with funds : ' . $bankAccount2->getBalance());
$bankAccount2->closeAccount();
try {
    $bankAccount2->closeAccount();
} catch (BankAccountException $e) {
    pl($e->getMessage());
}

//---[Bank account 3]---/

pl('--------- [Start testing national account (No conversion)] --------');

try{
    $person1 = new Person("Víctor", "1234567891", "john.doe@gmail.com", 90210);
    try {
        $person1->getEmailValidation();
    }catch(PersonException $e){
        pl($e->getMessage());
    }
    $bankAccount3 = new NationalBankAccount(500,"EUR",$person1);
    pl("My balance: ".number_format($bankAccount3->getBalance(),1)." € (".$bankAccount3->getCurrency().")");

    try {
        $person1->getEmailValidation();
    }catch(PersonException $e){
        pl($e->getMessage());
    }

    pl('Doing transaction deposit (+7000) with current balance ' . $bankAccount3->getBalance());
    try{
        $bankAccount3->transaction(transaction: new DepositTransaction(7000));
    }catch(FailedTransactionException $e){
        pl($e->getMessage());
    }
    pl('My new balance after deposit (+7000) : ' . $bankAccount3->getBalance());


    pl('Doing transaction withdrawal (-5000) with current balance : ' . $bankAccount3->getBalance());
    try{
        $bankAccount3->transaction(transaction: new WithdrawTransaction(5000));
    }catch(FailedTransactionException $e){
        pl($e->getMessage());
    }
    pl('My new balance after withdrawal (-5000) with funds : ' . $bankAccount3->getBalance());

    pl("The maintenance fee for your postcode is: ".$bankAccount4->calculateMaintenanceRate());
} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
}
//----------------------------------------


//---[Bank account 4]---/
pl('--------- [Start testing International account (Dollar conversion)] --------');
try {
    $person2 = new Person("Joel", "123456789", "john.doe@invalid-email", 90210);
    try {
        $person2->getEmailValidation();
    }catch(PersonException $e){
        pl($e->getMessage());
    }
    $bankAccount4 = new InternationalBankAccount(300, "EUR", $person2);
    pl("My balance: ".number_format($bankAccount4->getBalance(),decimals: 1)." € (".$bankAccount3->getCurrency().")");

    $convertBalance = $bankAccount4->convertBalance($bankAccount4->getBalance());
    pl("Converted balance: ".number_format($convertBalance, 2)." $ (".$bankAccount4->getConvertedCurrency().")");

    pl('Doing transaction deposit (+25000) with current balance ' . $bankAccount4->getBalance());
    try{
        $bankAccount4->transaction(transaction: new DepositTransaction(25000));
    }catch(FailedTransactionException $e){
        pl($e->getMessage());
    }
    pl('My new balance after deposit (+25000) : ' . $bankAccount4->getBalance());

    pl("The maintenance fee for your postcode is: ".$bankAccount4->calculateMaintenanceRate());
} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
}


//----------------------------------------
