<?php

namespace RightSignature\Exception;

class Unauthorized extends \RightSignature\Exception
{
    public function __construct()
    {
        parent::__construct('You are not authorized to access that resource');
    }
}
