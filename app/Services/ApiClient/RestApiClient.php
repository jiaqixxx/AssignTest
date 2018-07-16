<?php
/**
 * Created by PhpStorm.
 * User: rayndeng
 * Date: 13/7/18
 * Time: 1:14 PM
 */

namespace App\Services\ApiClient;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

abstract class RestApiClient
{

    /**
     * @var Client $client
     */
    private $client;
    protected $options;
    protected $timeout = 3600;
    protected $connectTimeout = 3600;
    protected $apiKey = '';
    protected $baseUri;
    protected $headers;

    public function __construct()
    {
        $this->options = [
            'base_uri' => $this->baseUri,
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connectTimeout,
            'allow_redirects' => true,
            'headers' => ['authorization' => $this->apiKey] + $this->headers,
        ];

        $this->client = new Client($this->options);
    }

    /*
    * Instances of descendant classes
    * array
    */
    protected static $instances = [];

    /*
     * For Singleton Pattern
     */
    public static function instance()
    {
        if (empty(self::$instances[static::class])) {
            $instance = new static();

            self::$instances[static::class] = $instance;
        } else {
            $instance = self::$instances[static::class];
        }

        return $instance;
    }


    public function test()
    {
        $test = "base1";
        var_dump($test);
    }

    public function get($uri, $queryParams = [])
    {
        $this->options['query'] = $queryParams;
        $data = $this->client->get($uri, $this->options);
        return json_decode($data->getBody()->getContents(), true);
    }

    public function put($uri, $body = [])
    {
        $this->options['form_params'] = $body;
        $data = $this->client->put($uri, $this->options);

        return json_decode($data->getBody()->getContents(), true);
    }

    public function postWithBody($uri, $body)
    {
        $this->options['body'] = $body;
        $data = $this->client->post($uri, $this->options);
        return json_decode($data->getBody()->getContents(), true);
    }

    public function post($uri, $body = [])
    {
        $this->options['form_params'] = $body;
        $data = $this->client->post($uri, $this->options);
        return json_decode($data->getBody()->getContents(), true);
    }

    public function patch($uri, $body = [])
    {
        $this->options['form_params'] = $body;
        $data = $this->client->patch($uri, $this->options);

        return json_decode($data->getBody()->getContents(), true);
    }


    public function delete($uri, $queryParams = [])
    {
        $this->options['query'] = $queryParams;
        $data = $this->client->get($uri, $this->options);
        return json_decode($data->getBody()->getContents(), true);
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }


    /**
     * @return int
     */
    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    /**
     * @param int $connectTimeout
     */
    public function setConnectTimeout(int $connectTimeout): void
    {
        $this->connectTimeout = $connectTimeout;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

}