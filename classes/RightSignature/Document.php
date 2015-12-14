<?php

namespace RightSignature;

use RightSignature\Util\ArrayHelpers as ArrayHelpers;
use RightSignature\Util\XmlHelpers as XmlHelpers;

/**
 * A RightSignature document instance.
 */
class Document extends \RightSignature\Util\ArrayDecorator
{
    const STATE_PENDING = 'pending';
    const STATE_SIGNED = 'signed';

    // ----------------------------------------
    // Signer Links

    public static function signerLinks($client, $documentGuid, $returnUrl = null)
    {
        $url = sprintf('/api/documents/%s/signer_links.xml%s',
            $documentGuid,
            $returnUrl ? sprintf('?redirect_location=%s', urlencode($returnUrl)) : ''
        );
        $xml = $client->get($url);

        $array = ArrayHelpers::normaliseKeys(XmlHelpers::toArray($xml));
        $array = ArrayHelpers::collapseGroup($array, 'signer_links');

        return new SignerLinks($array['signer_links']);
    }

    // ----------------------------------------
    // Document Details

    public static function documentDetails($client, $documentGuid)
    {
        $xml = $client->get(sprintf('/api/documents/%s.xml', $documentGuid));

        $array = ArrayHelpers::normaliseKeys(XmlHelpers::toArray($xml));
        $array = ArrayHelpers::collapseGroup($array, 'audit_trail');
        $array = ArrayHelpers::collapseGroup($array, 'form_fields');
        $array = ArrayHelpers::collapseGroup($array, 'recipients');
        $array = ArrayHelpers::collapseGroup($array, 'pages');

        return new self($array);
    }

    /**
     * This method is for sending a once-off document that has not been setup as a Template.
     *
     * @param string $path    Absolute path of the document
     * @param array  $payload Payload of the request
     *
     * @return array XML response parsed to array
     *
     * @throws Exception Missing required key
     */
    public static function send($client, $path, $payload)
    {
        // validation of required arguments
        foreach (['action', 'type', 'recipients'] as $argument) {
            ArrayHelpers::ensureIsSet($payload, $argument);
        }

        $info = pathinfo($path);
        $payload['document_data'] = [
            'type' => $payload['type'],
            'value' => $path,
        ];

        // change the underlying document (url, base64)
        if ('base64' == $payload['type']) {
            $payload['document_data']['filename'] = $info['basename'];

            // get the resource content
            $resource = fopen($path, 'r');
            $content = fread($resource, filesize($path));
            fclose($resource);

            $payload['document_data']['value'] = base64_encode($content);
        }

        // make the request
        $payload = XmlHelpers::toXml(['document' => $payload]);
        $response = $client->post('/api/documents.xml', $payload);

        return XmlHelpers::toArray($response);
    }
}
