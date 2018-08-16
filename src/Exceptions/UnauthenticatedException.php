<?php

namespace Ylateef\LaPromo\Exceptions;

use Exception;

class UnauthenticatedException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'User is not authenticated, and can not use promotion code.';

    /**
     * @var int
     */
    protected $code = 401;
}
