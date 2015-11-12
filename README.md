rightsignature-php
==================

A PHP wrapper around the RightSignature API.

This is not a complete implementation. The following API calls are
implemented:

 * Document Details
 * Prepackage Template
 * Prefill Template
 * Send Template
 * Signer Links

The following API calls are not yet implemented:

 * List Documents
 * Batch Document Details
 * Resend Reminder Emails
 * Trash Document
 * Extend Expiration
 * Update Document Tags
 * Send Document
 * List Templates
 * Template Details
 * Build New Template
 * User Details
 * Add User
 * Usage Report

Pull requests welcome.

Dependencies
------------

`RightSignature\HttpClient` currently has a dependency on [Guzzler][1].
The test suite additionally requires [PHPUnit][2] and [Mockery][3].

Usage
-----

    $client = RightSignature\HttpClient::forToken($myApiToken);
    $rs = new RightSignature($client);

    $document = $rs->documentDetails($someDocumentGuid);

Entities match the structure of the API responses:

    // Access fields using ->
    echo $document->state;

    // Hyphen-separated identifiers become underscore_separated
    echo $document->original_filename;

    // Repeating elements are accessed like array members
    echo $document->recipients[0]->name;

See [RightSignature API documentation][4] for details.

 [1]: https://github.com/guzzle/guzzle
 [2]: https://github.com/sebastianbergmann/phpunit
 [3]: https://github.com/padraic/mockery
 [4]: https://rightsignature.com/apidocs/api_calls
