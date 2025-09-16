<?php

namespace Vdhoangson\ZmpOpenApi\Classes;

use Vdhoangson\ZmpOpenApi\Classes\BaseClient;

class OpenAPIV2Client extends BaseClient
{
    public function __construct($partnerApiKey, $partnerId, $proxy = null)
    {
        parent::__construct('/v2', 'X-Partner-Api-Key', $partnerApiKey, 'X-Partner-Id', $partnerId, $proxy);
    }
}
