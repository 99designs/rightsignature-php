<?php

namespace Tests;

use \RightSignature\Util\XmlHelpers as XmlHelpers;

/**
 * Extends UnitTestCase with additional helpers.
 */
abstract class UnitTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Structurally compares two XML strings.
     */
    public function assertEqualXml($a, $b)
    {
        $this->assertEquals(
            XmlHelpers::toArray($a),
            XmlHelpers::toArray($b)
        );
    }
}