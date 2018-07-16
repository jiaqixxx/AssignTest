<?php
/**
 * Created by PhpStorm.
 * User: rayndeng
 * Date: 16/7/18
 * Time: 9:56 AM
 */

namespace App\Services;


use App\AddressBook;
use App\Customer;
use App\Order;
use App\Services\ApiClient\KibanaRestApiClient;
use App\Services\ApiClient\RestApiClientFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KibanaService
{

    /**
     * @param Carbon $from
     * @param Carbon $to
     */
    public function syncTestData(Carbon $from, Carbon $to)
    {
        $data = RestApiClientFactory::kibana()->getTestData($from, $to);

        $totalCustomers = Customer::count();
        $totalAddress = AddressBook::count();

        try {
            DB::beginTransaction();
            foreach ($data as $row) {
                $orderId = isset($row['order_id']) ? $row['order_id'] : null;
                if ($orderId) {

                    $order = Order::where('remote_order_id', $orderId)->first();

                    if (!$order) {
                        $order = new Order();
                    }

                    $env = [
                        'core' => isset($row['ua']) ? $row['ua'] : ''
                    ];
                    $orderId = isset($row['order_id']) ? $row['order_id'] : null;
                    $orderItems = isset($row['items']) ? $row['items'] : null;

                    $paymentMethod = isset($row['payment_method']) ? $row['payment_method'] : null;

                    if ($totalCustomers >= 1 && $totalAddress >= 1) {
                        $randCustomer = rand(1, $totalCustomers);
                        $randAddress = rand(1, $totalAddress);

                        $randomCustomer = Customer::where('id', $randCustomer)->first();
                        while ($randomCustomer == null) {
                            $randomCustomer = Customer::where('id', rand(1, $totalCustomers))->first();
                        }

                        $randomAddressBook = AddressBook::where('id', $randAddress)->first();
                        while ($randomAddressBook == null) {
                            $randomAddressBook = AddressBook::where('id', rand(1, $totalAddress))->first();
                        }


                        $customerDetails = [
                            'first_name' => $randomCustomer->customers_firstname,
                            'last_name' => $randomCustomer->customers_lastname,
                            'company' => $randomAddressBook->company,
                            'suburb' => $randomAddressBook->suburb,
                            'address' => $randomAddressBook->street,
                            'postcode' => $randomAddressBook->postcode,
                            'state' => $randomAddressBook->state,
                            'city' => $randomAddressBook->city,
                            'phone' => $randomCustomer->phone,
                        ];
                        $order->customer_details = $customerDetails;

                    }
                    $order->remote_order_id = $orderId;
                    $order->order_items = $orderItems;
                    $order->environment = $env;
                    $order->payment_method = $paymentMethod;
                    $order->product_look_up = [
                        '1' => 'Search Keyword',
                        '2' => 'Browse Category',
                        '3' => 'Reorder'
                    ];
                    $order->remote_order_created_date = isset($row['@timestamp']) ? $row['@timestamp'] : null;
                    $order->save();

                }
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
        }
    }
}