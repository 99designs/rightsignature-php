<?php

namespace RightSignature\Util;

/**
 * Helpers for marshalling data from XML to array-trees, and vice-versa.
 */
class XmlHelpers
{
    /**
     * Convert an XML string to an array-tree.
     *
     * @param string $xml
     *
     * @return array
     */
    public static function toArray($xml)
    {
        return self::_elementToArray(self::_parse($xml));
    }

    /**
     * Convert an array-tree to an XML string. $array is expected to be in the
     * same form as a SimpleXMLElement cast to an array.
     *
     * @see XmlHelpers::toElement
     *
     * @param array $array
     *
     * @return string
     */
    public static function toXml($array)
    {
        return self::toElement($array)->asXML();
    }

    /**
     * Recursively transform an array-tree into a SimpleXMLElement tree.
     * Arrays are expected to be in the same form as a SimpleXMLElement cast
     * to an array, e.g.
     *
     * array(
     *   'root-element' => array(
     *     '@attributes' => array('attr-name' => 'attr-value'),
     *     'repeating-element' => array(
     *       array('foo' => 'A'),
     *       array('foo' =>
     *         '@attributes' => array('x' => 'y'),
     *         '@value' => 'B'
     *       )
     *     )
     *   )
     * )
     *
     * yields:
     *
     * <root-element attr-name="attr-value">
     *   <repeating-element><foo>A</foo></repeating-element>
     *   <repeating-element><foo x="y">B</foo></repeating-element>
     * </root-element>
     * 
     * @param array $array
     *
     * @return object
     */
    public static function toElement($array)
    {
        $rootElementNames = array_keys($array);
        assert(count($rootElementNames) == 1);
        $rootElementName = $rootElementNames[0];

        $doc = new \DomDocument();

        $root = self::_toElement(
            $doc,
            $rootElementName,
            $array[$rootElementName]
        );

        $doc->appendChild($root);

        return simplexml_import_dom($root);
    }

    // ----------------------------------------
    // Private functions

    /**
     * Convert an XML string into a SimpleXMLElement.
     *
     * @param string $xml
     *
     * @return object
     */
    private static function _parse($xml)
    {
        return simplexml_load_string($xml);
    }

    public static function _elementToArray($element)
    {
        $self = __CLASS__;

        return array_map(
            function ($v) use ($self) {
                if (is_array($v) || $v instanceof \SimpleXMLElement) {
                    return empty($v) ? null : $self::_elementToArray($v);
                } else {
                    return $v;
                }
            },
            (array) $element
        );
    }

    /**
     * @param DomDocument $document
     * @param string      $elementName
     * @param mixed       $content     array or string
     */
    private static function _toElement($document, $elementName, $content)
    {
        $element = $document->createElement($elementName);

        if (is_array($content)) {
            if (isset($content['@attributes'])) {
                foreach ($content['@attributes'] as $k => $v) {
                    $element->setAttribute($k, $v);
                }
                unset($content['@attributes']);
            }

            if (isset($content['@value'])) {
                $element->appendChild($document->createTextNode(self::_stringify($content['@value'])));
                unset($content['@value']);
            } else {
                foreach ($content as $k => $v) {
                    if (is_array($v) && !ArrayHelpers::isAssociative($v)) {
                        // Repeating element
                        foreach ($v as $childContent) {
                            $element->appendChild(self::_toElement($document, $k, $childContent));
                        }
                    } else {
                        $element->appendChild(self::_toElement($document, $k, $v));
                    }
                }
            }
        } else {
            $element->appendChild($document->createTextNode(self::_stringify($content)));
        }

        return $element;
    }

    /**
     * Sane stringification of primitives.
     *
     * @param mixed $val
     *
     * @return string
     */
    private static function _stringify($val)
    {
        return is_bool($val) ? ($val ? 'true' : 'false') : $val;
    }
}
