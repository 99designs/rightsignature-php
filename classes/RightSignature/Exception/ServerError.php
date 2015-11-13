<?php

namespace RightSignature\Exception;

class ServerError extends \RightSignature\Exception
{
    public function __construct()
    {
        parent::__construct('RightSignature has experienced a server error');
    }
}
