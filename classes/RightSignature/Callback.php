<?php

namespace RightSignature;

use RightSignature\Util\ArrayHelpers as ArrayHelpers;
use RightSignature\Util\XmlHelpers as XmlHelpers;

/**
 * A POST callback from RightSignature.
 */
class Callback
    extends \RightSignature\Util\ArrayDecorator
{
    const TYPE_TEMPLATE = 'Template';
    const TYPE_DOCUMENT = 'Document';

    const STATUS_CREATED = 'created';
    const STATUS_SIGNED = 'signed';

    public static function parse($xml)
    {
        return new self(ArrayHelpers::normaliseKeys(XmlHelpers::toArray($xml)));
    }

    public function isTemplate()
    {
        return $this->callback_type == self::TYPE_TEMPLATE;
    }

    public function isDocument()
    {
        return $this->callback_type == self::TYPE_DOCUMENT;
    }

    public function isSigned()
    {
        return $this->status == self::STATUS_SIGNED;
    }

    public function isCreated()
    {
        return $this->status == self::STATUS_CREATED;
    }
}
