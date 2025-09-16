<?php

namespace Vdhoangson\ZmpOpenApi\Classes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Vdhoangson\ZmpOpenApi\Constants\ZmpConstant;

class BaseClient
{
    public $apiKey;
    public $isUseProxy = false;
    public $proxy;
    public $domain = ZmpConstant::DOMAIN_PROD;
    public $sdkVersion = '2.1.1';
    public $sdkName = 'PHP';
    public $headers;
    public $apiKeyHeaderName;
    public $identityHeaderName;
    public $identity;
    public $version;
    private $httpClient;

    public function __construct($version, $apiKeyHeaderName, $apiKey, $identityHeaderName, $identity, $proxy = null)
    {
        $this->version = $version;
        $this->apiKeyHeaderName = $apiKeyHeaderName;
        $this->apiKey = $apiKey;
        $this->identityHeaderName = $identityHeaderName;
        $this->identity = $identity;
        if ($proxy) {
            $this->proxy = $proxy;
            $this->isUseProxy = true;
        }
        $this->headers = [
            $apiKeyHeaderName => 'Bearer ' . $this->apiKey,
            $identityHeaderName => $this->identity,
            'X-Sdk-Version' => $this->sdkVersion,
            'X-Sdk-Name' => $this->sdkName,
        ];
        $this->httpClient = new Client([
            'proxy' => $this->isUseProxy ? $this->proxy : null,
        ]);
    }

    public function validateInit()
    {
        if (
            !$this->apiKey ||
            !$this->identity ||
            !$this->apiKeyHeaderName ||
            !$this->identityHeaderName
        ) {
            throw new \Exception('Invalid init value');
        }
        if ($this->isUseProxy && (!$this->proxy['host'] || !$this->proxy['port'])) {
            throw new \Exception('Invalid proxy value');
        }
    }

    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
        $this->isUseProxy = true;
        $this->httpClient = new Client([
            'proxy' => $this->proxy,
        ]);
    }

    public function cancelProxy()
    {
        $this->proxy = null;
        $this->isUseProxy = false;
        $this->httpClient = new Client();
    }

    public function doGet($endpoint, $params = [], $options = [])
    {
        try {
            $this->validateInit();
            $url = $this->domain . $endpoint;
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
            $response = $this->httpClient->get($url, [
                'headers' => $this->headers,
            ]);
            $data = json_decode($response->getBody(), true);
            return [
                'error' => $data['err'] ?? null,
                'message' => $data['msg'] ?? null,
            ] + ($data['data'] ?? []);
        } catch (RequestException $error) {
            $status = $error->getResponse() ? $error->getResponse()->getStatusCode() : $error->getCode();
            $message = isset($options['mapAxiosError']) ? $options['mapAxiosError']($error) : ($error->getResponse() ? json_decode($error->getResponse()->getBody(), true)['msg'] ?? $error->getMessage() : $error->getMessage());
            return [
                'error' => $status ?? -1,
                'message' => $message,
            ];
        } catch (\Exception $error) {
            return [
                'error' => -1,
                'message' => $error->getMessage(),
            ];
        }
    }

    public function doPost($endpoint, $params = [], $options = [])
    {
        try {
            $this->validateInit();
            $response = $this->httpClient->post($this->domain . $endpoint, [
                'headers' => $this->headers,
                'json' => $params,
            ]);
            $data = json_decode($response->getBody(), true);
            return [
                'error' => $data['err'] ?? null,
                'message' => $data['msg'] ?? null,
            ] + ($data['data'] ?? []);
        } catch (RequestException $error) {
            $status = $error->getResponse() ? $error->getResponse()->getStatusCode() : $error->getCode();
            $message = isset($options['mapAxiosError']) ? $options['mapAxiosError']($error) : ($error->getResponse() ? json_decode($error->getResponse()->getBody(), true)['msg'] ?? $error->getMessage() : $error->getMessage());
            return [
                'error' => $status ?? -1,
                'message' => $message,
            ];
        } catch (\Exception $error) {
            return [
                'error' => -1,
                'message' => $error->getMessage(),
            ];
        }
    }

    public function doPut($endpoint, $params = [], $options = [])
    {
        try {
            $this->validateInit();
            $response = $this->httpClient->put($this->domain . $endpoint, [
                'headers' => $this->headers,
                'json' => $params,
            ]);
            $data = json_decode($response->getBody(), true);
            return [
                'error' => $data['err'] ?? null,
                'message' => $data['msg'] ?? null,
            ] + ($data['data'] ?? []);
        } catch (RequestException $error) {
            $status = $error->getResponse() ? $error->getResponse()->getStatusCode() : $error->getCode();
            $message = isset($options['mapAxiosError']) ? $options['mapAxiosError']($error) : ($error->getResponse() ? json_decode($error->getResponse()->getBody(), true)['msg'] ?? $error->getMessage() : $error->getMessage());
            return [
                'error' => $status ?? -1,
                'message' => $message,
            ];
        } catch (\Exception $error) {
            return [
                'error' => -1,
                'message' => $error->getMessage(),
            ];
        }
    }

    public function getMiniApps($appSlice)
    {
        return $this->doGet($this->buildRequestURI(), $appSlice);
    }

    public function getVersionsMiniApp($appSlice)
    {
        return $this->doGet($this->buildRequestURI($appSlice['miniAppId'], ZmpConstant::VERSIONS), $appSlice, [
            'mapAxiosError' => function ($error) {
                if ($error->getResponse() && $error->getResponse()->getStatusCode() === 400) {
                    return 'Invalid Mini App ID';
                }
                return $error->getMessage();
            },
        ]);
    }

    public function createMiniApp($appInfo)
    {
        return $this->doPost($this->buildRequestURI(), $appInfo);
    }

    public function deployMiniApp($deployApp)
    {
        $multipart = [
            [
                'name' => 'file',
                'contents' => fopen($deployApp['file'], 'r'),
                'filename' => basename($deployApp['file']),
                'headers' => ['Content-Type' => 'application/zip'],
            ],
        ];
        $query = http_build_query([
            'name' => $deployApp['name'],
            'description' => $deployApp['description'],
        ]);
        $url = $this->domain . $this->buildRequestURI($deployApp['miniAppId'], ZmpConstant::UPLOAD) . '?' . $query;
        try {
            $response = $this->httpClient->post($url, [
                'headers' => $this->headers,
                'multipart' => $multipart,
            ]);
            $data = json_decode($response->getBody(), true);
            return [
                'error' => $data['err'] ?? null,
                'message' => $data['msg'] ?? null,
            ] + ($data['data'] ?? []);
        } catch (RequestException $error) {
            return [
                'error' => $error->getResponse() ? $error->getResponse()->getStatusCode() : -1,
                'message' => $error->getMessage(),
            ];
        }
    }

    public function requestPublishMiniApp($requestPublishApp)
    {
        return $this->doPost($this->buildRequestURI($requestPublishApp['miniAppId'], ZmpConstant::REQUEST_PUBLISH), $requestPublishApp);
    }

    public function publishMiniApp($publishApp)
    {
        return $this->doPost($this->buildRequestURI($publishApp['miniAppId'], ZmpConstant::PUBLISH), $publishApp);
    }

    public function getStats($statsRequest)
    {
        $response = $this->doGet($this->buildRequestURI($statsRequest['miniAppId'], ZmpConstant::STATS), $statsRequest);
        $error = $response['error'];
        $message = $response['message'];
        unset($response['error'], $response['message']);
        return [
            'error' => $error,
            'message' => $message,
            'data' => $response,
        ];
    }

    public function listPaymentChannels($request)
    {
        return $this->doGet($this->buildRequestURI($request['miniAppId'], ZmpConstant::PAYMENT_CHANNELS), []);
    }

    public function convertObjectToFormData($obj)
    {
        $multipart = [];
        foreach ($obj as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $i => $v) {
                    $multipart[] = [
                        'name' => $key . '[' . $i . ']',
                        'contents' => $v,
                    ];
                }
            } elseif (is_bool($value)) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value ? 'true' : 'false',
                ];
            } else {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value,
                ];
            }
        }
        return $multipart;
    }

    public function createPaymentChannel($request)
    {
        $multipart = $this->convertObjectToFormData($request);
        $url = $this->domain . $this->buildRequestURI($request['miniAppId'], ZmpConstant::PAYMENT_CHANNELS);
        try {
            $response = $this->httpClient->post($url, [
                'headers' => $this->headers,
                'multipart' => $multipart,
            ]);
            $data = json_decode($response->getBody(), true);
            return [
                'error' => $data['err'] ?? null,
                'message' => $data['msg'] ?? null,
            ] + ($data['data'] ?? []);
        } catch (RequestException $error) {
            return [
                'error' => $error->getResponse() ? $error->getResponse()->getStatusCode() : -1,
                'message' => $error->getMessage(),
            ];
        }
    }

    public function updatePaymentChannel($request)
    {
        $multipart = $this->convertObjectToFormData($request);
        $url = $this->domain . $this->buildRequestURI($request['miniAppId'], ZmpConstant::PAYMENT_CHANNELS, $request['channelId']);
        try {
            $response = $this->httpClient->put($url, [
                'headers' => $this->headers,
                'multipart' => $multipart,
            ]);
            $data = json_decode($response->getBody(), true);
            return [
                'error' => $data['err'] ?? null,
                'message' => $data['msg'] ?? null,
            ] + ($data['data'] ?? []);
        } catch (RequestException $error) {
            return [
                'error' => $error->getResponse() ? $error->getResponse()->getStatusCode() : -1,
                'message' => $error->getMessage(),
            ];
        }
    }

    public function getPaymentSetting($data)
    {
        return $this->doGet($this->buildRequestURI($data['miniAppId'], ZmpConstant::PAYMENT_SETTING), []);
    }

    public function updatePaymentSetting($data)
    {
        return $this->doPut($this->buildRequestURI($data['miniAppId'], ZmpConstant::PAYMENT_SETTING), $data);
    }

    public function listQrCodeShortLinks($data)
    {
        return $this->doGet($this->buildRequestURI($data['miniAppId'], ZmpConstant::QRCODE_SHORT_LINKS), $data);
    }

    public function createQrCodeShortUrl($data)
    {
        return $this->doPost($this->buildRequestURI($data['miniAppId'], ZmpConstant::QRCODE_SHORT_LINKS), $data);
    }

    public function requestPermission($data)
    {
        return $this->doPost($this->buildRequestURI($data['miniappId'], ZmpConstant::PERMISSIONS), $data);
    }

    /**
     * Tạo URL cho request
     *
     * @param int $appId
     * @param mixed $path
     * @param array $pathVariables
     * @return string
     */
    public function buildRequestURI(?int $appId = null, $path = null, array ...$pathVariables)
    {
        $rs = $this->version . ZmpConstant::APPS;
        if ($appId) {
            $rs .= '/' . $appId;
        }
        if ($path) {
            $rs .= $path;
        }
        foreach ($pathVariables as $pathVariable) {
            $rs .= '/' . $pathVariable;
        }
        return $rs;
    }

    public function listApiDomain($data)
    {
        return $this->doGet($this->buildRequestURI($data['miniAppId'], ZmpConstant::API_DOMAINS), $data);
    }

    public function createApiDomain($data)
    {
        return $this->doPost($this->buildRequestURI($data['miniAppId'], ZmpConstant::API_DOMAINS), $data);
    }

    public function updateApiDomain($data)
    {
        return $this->doPut($this->buildRequestURI($data['miniAppId'], ZmpConstant::API_DOMAINS), $data);
    }

    public function listCategories()
    {
        return $this->doGet($this->buildRequestURI(null, ZmpConstant::APP_CATEGORIES), []);
    }
}
