<?php
namespace ComBank\Exceptions;

use ComBank\Exceptions\BaseExceptions;

class PersonException extends BaseExceptions {
    protected $errorCode = 500;
    protected $errorLabel = 'PersonException';
}