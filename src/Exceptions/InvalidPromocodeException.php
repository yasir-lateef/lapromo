<?php

namespace Ylateef\LaPromo\Exceptions;

use Exception;

class InvalidPromocodeException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Invalid promotion code was passed.';

    /**
     * @var int
     */
    protected $code = 404;
}
