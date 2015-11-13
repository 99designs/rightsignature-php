<?php

namespace RightSignature\Exception;

class UserError extends \RightSignature\Exception
{
    /**
     * Return new instance with error message extracted from XML response.
     *
     * @param string $xml
     *
     * @return InvalidRequest
     */
    public static function fromXml($xml)
    {
        $data = \RightSignature\Util\XmlHelpers::toArray($xml);

        return new self($data['message']);
    }
}
