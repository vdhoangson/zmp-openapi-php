<?php

namespace Vdhoangson\ZmpOpenApi\Classes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Vdhoangson\ZmpOpenApi\Constants\ZmpConstant;

class BaseClient
{
    public string $apiKey;
    public $isUseProxy = false;
    public $proxy;
    public $domain = ZmpConstant::DOMAIN_PROD;
    public $sdkVersion = '1.0.0';
    public $sdkName = 'PHP';
    public $headers;
    public $apiKeyHeaderName;
    public $identityHeaderName;
    public $identity;
    public string $version;

    private Client $httpClient;

    /**
     * Constructor for BaseClient.
     *
     * Initializes the client with API credentials and optional proxy settings.
     *
     * @param string $version API version
     * @param string $apiKeyHeaderName Header name for API key
     * @param string $apiKey API key
     * @param string $identityHeaderName Header name for identity
     * @param string $identity Identity value
     * @param mixed $proxy Proxy configuration (optional)
     */
    public function __construct(string $version, string $apiKeyHeaderName, string $apiKey, string $identityHeaderName, string $identity, $proxy = null)
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

    /**
     * Validates the initialization parameters.
     *
     * Checks if required fields are set and proxy is valid if used.
     *
     * @throws \Exception If validation fails
     */
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

    /**
     * Sets the proxy configuration for HTTP requests.
     *
     * @param mixed $proxy Proxy settings
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
        $this->isUseProxy = true;
        $this->httpClient = new Client([
            'proxy' => $this->proxy,
        ]);
    }

    /**
     * Cancels the proxy configuration.
     *
     * Resets proxy settings and recreates HTTP client without proxy.
     */
    public function cancelProxy()
    {
        $this->proxy = null;
        $this->isUseProxy = false;
        $this->httpClient = new Client();
    }

    /**
     * Performs a GET request to the API.
     *
     * @param string $endpoint API endpoint
     * @param array $params Query parameters
     * @param array $options Additional options
     * @return array Response data
     */
    public function doGet($endpoint, array $params = [], $options = [])
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

    /**
     * Performs a POST request to the API.
     *
     * @param string $endpoint API endpoint
     * @param mixed $params Request body parameters
     * @param array $options Additional options
     * @return array Response data
     */
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

    /**
     * Performs a PUT request to the API.
     *
     * @param string $endpoint API endpoint
     * @param mixed $params Request body parameters
     * @param array $options Additional options
     * @return array Response data
     */
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

    /**
     * Retrieves a list of mini apps.
     *
     * @param array $params Query parameters
     * @return array Response data
     */
    public function getMiniApps(array $params = [])
    {
        return $this->doGet($this->buildRequestURI(), $params);
    }

    /**
     * Retrieves versions of a mini app.
     *
     * @param mixed $appSlice App slice data
     * @return array Response data
     */
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

    /**
     * Creates a new mini app.
     *
     * @param mixed $appInfo App information
     * @return array Response data
     */
    public function createMiniApp($appInfo)
    {
        return $this->doPost($this->buildRequestURI(), $appInfo);
    }

    /**
     * Deploys a mini app.
     *
     * @param mixed $deployApp Deployment data including file
     * @return array Response data
     */
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

    /**
     * Requests publication of a mini app.
     *
     * @param mixed $requestPublishApp Publication request data
     * @return array Response data
     */
    public function requestPublishMiniApp($requestPublishApp)
    {
        return $this->doPost($this->buildRequestURI($requestPublishApp['miniAppId'], ZmpConstant::REQUEST_PUBLISH), $requestPublishApp);
    }

    /**
     * Publishes a mini app.
     *
     * @param mixed $publishApp Publication data
     * @return array Response data
     */
    public function publishMiniApp($publishApp)
    {
        return $this->doPost($this->buildRequestURI($publishApp['miniAppId'], ZmpConstant::PUBLISH), $publishApp);
    }

    /**
     * Retrieves statistics for a mini app.
     *
     * @param mixed $statsRequest Stats request data
     * @return array Response data
     */
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

    /**
     * Lists payment channels for a mini app.
     *
     * @param mixed $request Request data
     * @return array Response data
     */
    public function listPaymentChannels($request)
    {
        return $this->doGet($this->buildRequestURI($request['miniAppId'], ZmpConstant::PAYMENT_CHANNELS), []);
    }

    /**
     * Converts an object to multipart form data.
     *
     * @param mixed $obj Object to convert
     * @return array Multipart data
     */
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

    /**
     * Creates a payment channel for a mini app.
     *
     * @param mixed $request Request data
     * @return array Response data
     */
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

    /**
     * Updates a payment channel for a mini app.
     *
     * @param mixed $request Request data
     * @return array Response data
     */
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

    /**
     * Retrieves payment settings for a mini app.
     *
     * @param mixed $data Request data
     * @return array Response data
     */
    public function getPaymentSetting($data)
    {
        return $this->doGet($this->buildRequestURI($data['miniAppId'], ZmpConstant::PAYMENT_SETTING), []);
    }

    /**
     * Updates payment settings for a mini app.
     *
     * @param mixed $data Request data
     * @return array Response data
     */
    public function updatePaymentSetting($data)
    {
        return $this->doPut($this->buildRequestURI($data['miniAppId'], ZmpConstant::PAYMENT_SETTING), $data);
    }

    /**
     * Lists QR code short links for a mini app.
     *
     * @param mixed $data Request data
     * @return array Response data
     */
    public function listQrCodeShortLinks($data)
    {
        return $this->doGet($this->buildRequestURI($data['miniAppId'], ZmpConstant::QRCODE_SHORT_LINKS), $data);
    }

    /**
     * Creates a QR code short URL for a mini app.
     *
     * @param mixed $data Request data
     * @return array Response data
     */
    public function createQrCodeShortUrl($data)
    {
        return $this->doPost($this->buildRequestURI($data['miniAppId'], ZmpConstant::QRCODE_SHORT_LINKS), $data);
    }

    /**
     * Requests permission for a mini app.
     *
     * @param mixed $data Request data
     * @return array Response data
     */
    public function requestPermission($data)
    {
        return $this->doPost($this->buildRequestURI($data['miniappId'], ZmpConstant::PERMISSIONS), $data);
    }

    /**
     * Builds the request URI for API calls.
     *
     * @param int|null $appId Mini app ID (optional)
     * @param mixed $path Additional path
     * @param array ...$pathVariables Path variables
     * @return string Request URI
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

    /**
     * Lists API domains for a mini app.
     *
     * @param mixed $data Request data
     * @return array Response data
     */
    public function listApiDomain($data)
    {
        return $this->doGet($this->buildRequestURI($data['miniAppId'], ZmpConstant::API_DOMAINS), $data);
    }

    /**
     * Creates an API domain for a mini app.
     *
     * @param mixed $data Request data
     * @return array Response data
     */
    public function createApiDomain($data)
    {
        return $this->doPost($this->buildRequestURI($data['miniAppId'], ZmpConstant::API_DOMAINS), $data);
    }

    /**
     * Updates an API domain for a mini app.
     *
     * @param mixed $data Request data
     * @return array Response data
     */
    public function updateApiDomain($data)
    {
        return $this->doPut($this->buildRequestURI($data['miniAppId'], ZmpConstant::API_DOMAINS), $data);
    }

    /**
     * Lists app categories.
     *
     * @return array Response data
     */
    public function listCategories()
    {
        return $this->doGet($this->buildRequestURI(null, ZmpConstant::APP_CATEGORIES), []);
    }
}
