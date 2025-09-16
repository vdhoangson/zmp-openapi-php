<?php

namespace Vdhoangson\ZmpOpenApi\Classes;

use Vdhoangson\ZmpOpenApi\Classes\BaseClient;

class PartnerClient extends BaseClient
{
    /**
     * PartnerClient constructor.
     *
     * @param string $partnerApiKey
     * @param int $partnerId
     * @param array|null $proxy
     */
    public function __construct(string $partnerApiKey, int $partnerId, ?array $proxy = null)
    {
        parent::__construct('/partners', 'X-Partner-Api-Key', $partnerApiKey, 'X-Partner-Id', $partnerId, $proxy);
    }
}
