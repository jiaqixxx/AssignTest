<?php
/**
 * Created by PhpStorm.
 * User: rayndeng
 * Date: 16/7/18
 * Time: 9:56 AM
 */

namespace App\Services;


use App\Order;
use App\Services\ApiClient\KibanaRestApiClient;
use App\Services\ApiClient\RestApiClientFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KibanaService
{

    public function syncTestData(Carbon $from, Carbon $to)
    {
        $data = RestApiClientFactory::kibana()->getTestData($from, $to);

        try {
            DB::beginTransaction();
            foreach ($data as $row) {
                $orderId = isset($row['order_id']) ? $row['order_id'] : null;
                if ($orderId) {
                    if (!Order::checkOrderIfExistsByRemoteOrderId($orderId)) {
                        $env = [
                            'device' => ['type' => 'mobile/desktop'],
                            'core' => isset($row['ua']) ? $row['ua'] : ''
                        ];
                        $orderId = isset($row['order_id']) ? $row['order_id'] : null;
                        $orderItems = isset($row['items']) ? $row['items'] : null;

                        $order = new Order();
                        $order->remote_order_id = $orderId;
                        $order->order_items = $orderItems;
                        $order->environment = $env;
                        $order->remote_order_created_date = isset($row['@timestamp']) ? $row['@timestamp'] : null;
                        $order->save();
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
        }
    }
}