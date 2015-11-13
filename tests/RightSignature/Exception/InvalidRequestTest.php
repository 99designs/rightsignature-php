<?php

namespace RightSignature\Exception;

class InvalidRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testFromXml()
    {
        $xml = '<error><message>Sample message</message></error>';
        $ex = InvalidRequest::fromXml($xml);
        $this->assertEquals('Sample message', $ex->getMessage());
    }
}
