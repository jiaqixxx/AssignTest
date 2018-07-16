<?php
/**
 * Created by PhpStorm.
 * User: rayndeng
 * Date: 13/7/18
 * Time: 1:14 PM
 */

namespace App\Services\ApiClient;

use App\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class KibanaRestApiClient extends RestApiClient
{

    protected $timeout = 3600;
    protected $connectTimeout = 3600;
    protected $headers = [
        'content-type' => 'application/x-ndjson',
        'kbn-version' => '6.0.1'
    ];
    protected $apiKey = 'Basic c3VwcG9ydDoxbmtAU3VQUDBydA==';
    protected $baseUri = 'https://play.inkstation.com.au/elasticsearch/';


    /**
     * @param Carbon $fromCarbon
     * @param Carbon $toCaron
     * @return array
     */
    public function getRawData(Carbon $fromCarbon, Carbon $toCaron)
    {
        $from = $fromCarbon->timestamp * 1000;
        $to = $toCaron->timestamp * 1000;
        return $this->postWithBody('_msearch', '{"index":["inkstation-*"],"ignore_unavailable":true,"preference":1531465019044}
{"version":true,"size":500,"sort":[{"@timestamp":{"order":"desc","unmapped_type":"boolean"}}],"_source":{"excludes":[]},"aggs":{"2":{"date_histogram":{"field":"@timestamp","interval":"1m","time_zone":"Australia/Sydney","min_doc_count":1}}},"stored_fields":["*"],"script_fields":{},"docvalue_fields":["@timestamp"],"query":{"bool":{"must":[{"match_all":{}},{"match_phrase":{"source":{"query":"order-summary"}}},{"range":{"@timestamp":{"gte":' . $from . ',"lte":' . $to . ',"format":"epoch_millis"}}}],"filter":[],"should":[],"must_not":[]}},"highlight":{"pre_tags":["@kibana-highlighted-field@"],"post_tags":["@/kibana-highlighted-field@"],"fields":{"*":{}},"fragment_size":2147483647}}
');
    }

    /**
     * @param Carbon $fromCarbon
     * @param Carbon $toCaron
     * @return array
     */
    public function getTestData(Carbon $fromCarbon, Carbon $toCaron)
    {

        $data = $this->getRawData($fromCarbon, $toCaron);

        $testData = [];
        if (!empty($data) && isset($data['responses'])) {

            foreach ($data['responses'] as $response) {


                if (isset($response['hits']) && isset($response['hits']['hits'])) {

                    $hits = $response['hits']['hits'];

                    foreach ($hits as $hit) {

                        $testData[] = $hit['_source'];
                    }
                }

            }
        }
        return $testData;
    }
}