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

class RestApiClientFactory
{
    /**
     * @return KibanaRestApiClient
     */
    public static function kibana(){
        return KibanaRestApiClient::instance();
    }

}