<?php

namespace RightSignature\Util;

class ArrayHelpersTest extends \Tests\UnitTestCase
{
    public function testIsAssociative()
    {
        $this->assertTrue(ArrayHelpers::isAssociative(array('foo' => 'bar', 'baz')));
        $this->assertFalse(ArrayHelpers::isAssociative(array('foo', 'bar')));
    }

    public function testMapKeyValues()
    {
        $mapped = ArrayHelpers::mapKeyValues(
            array('foo' => 'bar', 'baz' => 'bla'),
            function ($k, $v) {
                return array(strtoupper($k), $v.'!');
            }
        );

        $this->assertEquals(
            array('FOO' => 'bar!', 'BAZ' => 'bla!'),
            $mapped
        );
    }

    public function testNormaliseKeys()
    {
        $normalised = ArrayHelpers::normaliseKeys(array(
            'foo-bar' => array(
                'baz-bla' => array(
                    'bla-bla', 'yackity-schmackity',
                ),
            ),
        ));

        $expected = array(
            'foo_bar' => array(
                'baz_bla' => array(
                    'bla-bla', 'yackity-schmackity',
                ),
            ),
        );

        $this->assertEquals($expected, $normalised);
    }

    public function testCollapseGroup()
    {
        $uncollapsed = array(
            'foos' => array(
                'foo' => array(
                    array('bar' => 1),
                    array('bar' => 2),
                ),
            ),
        );

        $collapsed = array(
            'foos' => array(
                array('bar' => 1),
                array('bar' => 2),
            ),
        );

        $this->assertEquals($collapsed, ArrayHelpers::collapseGroup($uncollapsed, 'foos'));
    }

    public function testCollapseSingletonGroup()
    {
        $uncollapsable = array(
            'foos' => array(
                'foo' => array(
                    'bar' => 1,
                ),
            ),
        );

        $collapsed = array(
            'foos' => array(
                array('bar' => 1),
            ),
        );

        $this->assertEquals($collapsed, ArrayHelpers::collapseGroup($uncollapsable, 'foos'));
    }
}
