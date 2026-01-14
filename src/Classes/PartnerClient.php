<?php

namespace Vdhoangson\ZmpOpenApi\Classes;

use Vdhoangson\ZmpOpenApi\Classes\BaseClient;
use Vdhoangson\ZmpOpenApi\Constants\ZmpConstant;

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

    /**
     * Get Business Profile Status
     * Returns the current verification status of the business profile
     * @param object $request Request containing miniAppId
     * @return array Business profile status
     */
    public function getBusinessProfile($request)
    {
        return $this->doGet($this->buildRequestURI($request->miniAppId, ZmpConstant::BUSINESS_PROFILE), []);
    }

    /**
     * Update Business Profile
     * Updates the business profile information
     * @param object $request Request containing miniAppId and kybData
     * @return array Update result
     */
    public function updateBusinessProfile($request)
    {
        return $this->doPut($this->buildRequestURI($request->miniAppId, ZmpConstant::BUSINESS_PROFILE), ['kybData' => $request->kybData]);
    }

    /**
     * Get Document Request Status
     * Returns document submission status and review history
     * Includes owner, business, and additional documents based on business type
     * @param object $request Request containing miniAppId
     * @return array Document request status and history
     */
    public function getDocumentRequest($request)
    {
        return $this->doGet($this->buildRequestURI($request->miniAppId, ZmpConstant::DOCUMENT_REQUEST), []);
    }

    /**
     * Get App Info
     * Returns detailed app metadata including business profile and OA info
     * @param object $request Request containing miniAppId
     * @return array Extended app information
     */
    public function getAppInfo($request)
    {
        return $this->doGet($this->buildRequestURI($request->miniAppId, ZmpConstant::APP_INFO), []);
    }

    /**
     * Update App Info
     * Updates app sub-category IDs
     * @param object $request Request containing miniAppId and subCateIds
     * @return array Update result
     */
    public function updateAppInfo($request)
    {
        return $this->doPut($this->buildRequestURI($request->miniAppId, ZmpConstant::APP_INFO), ['subCateIds' => implode(',', $request->subCateIds)]);
    }
}
