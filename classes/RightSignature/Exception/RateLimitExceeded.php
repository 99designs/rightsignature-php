<?php

namespace RightSignature\Exception;

class RateLimitExceeded
	extends \RightSignature\Exception
{
	public function __construct()
	{
		parent::__construct('Rate limit exceeded');
	}
}
	