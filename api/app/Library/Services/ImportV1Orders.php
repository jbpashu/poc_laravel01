<?php
namespace App\Library\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Client;
use App\Warehouse;
use App\Customer;
use App\Item;
use Config;

class ImportV1Orders extends BaseImportService
{
    protected $service = 'orders';

    public function fetchAllAllocatedOrders()
    {
        // set execution time
        set_time_limit(0);
        ini_set('max_execution_time', 300000);
        ini_set('memory_limit', '-1');
        date_default_timezone_set('America/Denver');
        $this->apiCredentials   = Config::get('app.wms');
        $startDate = '20200617000000';
        $endDate = '20200626115959';

        $res = $this->gClient->get($this->apiEndPoint);
        $responseData =  json_decode($res->getBody());

        $orderInsertData = $orderItemInsertData = [];
        $responseData = array_chunk($responseData, 500);

        foreach ($responseData as $key => $responseDataItem) {
            foreach ($responseDataItem as $order) {
                try {
                    if ($order->status_code == 1 || $order->status_code == 2) {
                        $clientId = Client::where('client_code', $order->client_code)->first()->id;
                        $shipFromId = Warehouse::where('warehouse_code', $order->ship_from->id)->first()->id;
                        $shipToId = Customer::where([
                          'name' => $order->ship_to->name,
                          'client_id'=>$clientId
                        ])->first()->id;
                        $billToId = Customer::where([
                          'name' => $order->bill_to->name,
                          'client_id'=>$clientId
                        ])->first()->id;

                        try {
                            $shippingInsertDataId = DB::table('shippings')
                            ->insertGetId([
                              'carrier_code'=>$order->shipping->carrier_code,
                              'carrier_description'=>$order->shipping->carrier_description,
                              'service'=>$order->shipping->service,
                              'service_code'=>$order->shipping->service_code,
                              'payment_type'=>$order->shipping->payment_type,
                              'pickup_date'=> (
                                $this->isTimestamp($order->shipping->pickup_date)?
                                $order->shipping->pickup_date : null
                              ),
                              'pickup_time'=> (
                                $this->isTimestamp($order->shipping->pickup_time)?
                                $order->shipping->pickup_time : null
                              ),
                              'pickup_party'=>$order->shipping->pickup_party,
                              'delivery_date'=> (
                                $this->isTimestamp($order->shipping->delivery_date)?
                                $order->shipping->delivery_date : null
                              ),
                              'cost' => $order->shipping->cost,
                              'tracking_number'=>$order->shipping->tracking_number,
                              'total_package_weight'=>$order->shipping->total_package_weight,
                              'total_package_count'=>$order->shipping->total_package_count,
                              'is_saturday_delivery'=>$order->shipping->is_saturday_delivery,
                              'is_insured'=>$order->shipping->is_insured,
                              'is_machinable'=>$order->shipping->is_machinable,
                              'is_inside_delivery'=>$order->shipping->is_inside_delivery,
                              'is_liftgate_required'=>$order->shipping->is_liftgate_required,
                              'has_alcohol'=>$order->shipping->has_alcohol,
                              'hold_until'=>(
                                $this->isTimestamp($order->shipping->hold_until)?
                                $order->shipping->hold_until : null
                              ),
                              'warehouse_id'=>$order->shipping->warehouse_id,
                              'created_at'=>date('d-m-y h:i:s'),
                              'updated_at'=>date('d-m-y h:i:s')
                            ]);
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }
                        $orderInsertData= [
                          'order_id' => $order->order_id,
                          'backorder_id' => $order->backorder_id,
                          'client_id' => $clientId,
                          'ship_from_id' => $shipFromId,
                          'ship_to_id' => $shipToId,
                          'bill_to_id' => ($billToId)??$shipToId,
                          'shipping_notes' => $order->shipping_notes,
                          'picking_notes' => $order->picking_notes,
                          'order_date' => $order->order_date,
                          'order_reference_1' => $order->order_reference_1,
                          'order_reference_2' => $order->order_reference_2,
                          'status_code' => $order->status_code,
                          'status_text' => $order->status_text?? '',
                          'source_id' => $order->source_id,
                          'freight_bill_to' => ($order->freight_bill_to ?? '') ,
                          'notes' => $order->notes,
                          'pick_ticket_printed' => $order->pick_ticket_printed,
                          'hold_flag' => $order->hold_flag ?? 0,
                          'total_amount' => $order->shipping->total_amount ?? 0,
                          'total_shipping' => $order->shipping->total_shipping ?? 0,
                          'shipping_id' => $shippingInsertDataId,
                          'created_at' => date('d-m-y h:i:s'),
                          'updated_at' => date('d-m-y h:i:s'),
                        ];
                       
                        if (isset($order->request_date) && strlen($order->request_date) > 2) {
                            $orderInsertData['requested_date'] = $order->request_date;
                        }
                        
                        $orderInsertData['packaged_on'] = (
                          isset($order->packaged_on) &&
                          strlen($order->packaged_on) > 2
                        )? $order->packaged_on : null;
                      
                        $orderInsertData['delivery_date'] = (
                          isset($order->shipping->delivery_date) &&
                          strlen($order->shipping->delivery_date) > 2
                        )? $order->shipping->delivery_date : null;

                        try {
                            $orderInsertDataId = DB::table('orders')->insertGetId($orderInsertData);
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }
                        echo '\n';
                        echo "orderId:".$order->order_id;
                        // prepare order items
                        if ($orderInsertDataId > 0) {
                            foreach ($order->items as $orderItems) {
                                $itemId = Item::where([
                                  'item_code' => $orderItems->product_code,
                                  'client_id' => $clientId
                                ])->first()->id;
                                $validatedQuantity = $orderItems->details->qty_picked_validated ?? 0;
                                $packedQuantity = $orderItems->details->qty_packed?? 0;

                                $orderItemInsertData = [
                                    'order_id' => $orderInsertDataId,
                                    'line' => $orderItems->line,
                                    'item_id' => $itemId,
                                    'external_id' => $orderItems->product_id,
                                    'ordered_quantity' => $orderItems->quantity_ordered?? 0,
                                    'allocated_quantity' => $orderItems->quantity_allocated?? 0,
                                    'picked_quantity' => $validatedQuantity,
                                    'validated_quantity' => $validatedQuantity,
                                    'packed_quantity' => $packedQuantity,
                                    'UOM' =>  $orderItems->sell_qty_uom,
                                    'created_at' => date('d-m-y h:i:s'),
                                    'updated_at' => date('d-m-y h:i:s')
                                ];

                                $orderItemInsertDataId = DB::table('order_lines')->insertGetId($orderItemInsertData);
                                echo PHP_EOL, "order Item Id: ", $orderItems->product_id, $orderItems->product_code;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }

    protected function isTimestamp($string)
    {
        return (1 === preg_match('~^[1-9][0-9]*$~', $string));
    }
}
