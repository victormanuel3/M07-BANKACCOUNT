<?php
namespace ComBank\Bank;

class InternationalBankAccount extends BankAccount {
    function getConvertedBalance():float{
       return parent::convertBalance($this->balance);
    }
    function getConvertedCurrency():string{   
        return 'USD';
    }
}
