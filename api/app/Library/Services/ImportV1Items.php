<?php
namespace App\Library\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GClient;
use App\Client;
use App\Item;
use App\ItemCategory;
use App\ItemGroup;
use App\Supplier;
use App\SupplierSetting;
use App\UomSetting;
use App\CurrencyCode;
use App\UomLevels;
use App\UomCode;
use App\DimensionsUnitCode;
use App\WeightUnitCode;

class ImportV1Items extends BaseImportService
{
    protected $service = 'products';

     /*
       * @Author Ashish Awasthy
       * @desc
    */
    public function fetchAllProducts()
    {
        echo PHP_EOL, 'Fetching from API';
        $res = $this->gClient->get($this->apiEndPoint);
        return json_decode($res->getBody(), true);
    }

    /*
      * @Author Ashish Awasthy
      * @desc
    */
    public function fetchAllProductsByClientCode($clientCode)
    {
        echo PHP_EOL, 'Fetching from API';
        $res = $this->gClient->get(sprintf('%s&client_code=%s', $this->apiEndPoint, $clientCode));
        return json_decode($res->getBody(), true);
    }



    public function parseClientInsertData($itemsData)
    {
        echo PHP_EOL, 'Inserting Records in DB';
        $responseData = array_chunk($itemsData, 500);
        $itemsInsertDataArray = [];
        $supplierSettingsInsertData = [];
        foreach ($responseData as $responseDataItems) {
            foreach ($responseDataItems as $item) {
                //Product or Item Master Data
                $itemRecord = Item::where('item_code', $item['product_code'])
                ->where('uid_no', $item['uid_no'])->first();
                if ($itemRecord instanceof Item && isset($itemRecord->item_code)) {
                    echo PHP_EOL, 'Item "', $itemRecord->item_code, '" Already exist DB insertion skipped.';
                } else {
                    echo PHP_EOL, print_r($item, true);
                    $insertItem = [
                      'uid_no' => $item['uid_no'],
                      'item_code' => $item['product_code'],
                      'item_description' => sprintf('%s %s', $item['description_1'], $item['description_2']),
                      'client_id'        => Client::where('client_code', $item['client_code'])->first()->id,
                      'item_name'        => $item['short_descr'],
                      'item_category_id' => ItemCategory::where('category_code', 'default')->first()->id,
                      'item_group_id'    => ItemGroup::find(1)->id,
                      'upc_code'         => $item['upc_code'],
                      'currency_id'      => CurrencyCode::where('code', 'USD')->first()->id,
                      'list_price'       => $item['our_list_price'],
                      'base_cost'        => $item['costs_1'],
                      'is_active'        => $item['yes_no_fields_1'],
                      'is_serial'        => ($item['serial_no_flag'] == 2),
                      'is_tag'           => ($item['serial_no_flag'] == 3),
                      'is_lot'           => ($item['lot_no_flag'] == 1),
                      'is_scan'          => ($item['yes_no_fields_4'] == 1),
                      'quantity'         => $item['qty_on_hand'],
                      'min_stock_level'  => $item['order_qtys_6'],
                      'max_stock_level'  => $item['order_qtys_7'],
                      'bom_flag'         => $item['bom_kit_flag'],
                      'is_track_expiry'  => $item['fifo_lifo_flag'],
                      'suggested_stock_level' => $item['order_qtys_4'],
                      'created_at' => date('d-m-y h:i:s'),
                      'updated_at' => date('d-m-y h:i:s'),

                    ];

                    try {
                        $itemInsertId = DB::table('items')->insertGetId($insertItem);
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }

                    //Supplier Settings Data
                    //TODO::Need to fix this as there could be more suppliers for one client
                    $supplierSetting['supplier_id'] = Supplier::where('client_id', $insertItem['client_id'])
                    ->first()->id;
                    $supplierSetting['item_id']     = $itemInsertId;


                    //UOM Data
                    $uomData = [
                        'level' => 1,
                        'width' => $item['measures_4'],
                        'length' => $item['measures_3'],
                        'height' => $item['measures_5'],
                        'net_weight' => $item['measures_5'],
                        'gross_weight' => $item['measures_5'],
                        'teir_weight' => $item['measures_5'],
                        'uom_code_id' => UomCode::where('code', 'EA')->first()->id,
                        'dimension_unit_id' => DimensionsUnitCode::where('unit', 'MM')->first()->id,
                        'weight_unit_id' => WeightUnitCode::where('unit', 'KG')->first()->id,
                    ];

                    try {
                        $uomInsertId = DB::table('uoms')->insertGetId($uomData);
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }

                    //UOM Settings Data
                    $uomSettingData = [
                        'uom_id' => $uomInsertId,
                        'item_id' => $itemInsertId,
                        'shipping_uom_level_id' => UomLevels::where('level', 'Level - 1')->first()->id,
                    ];
                    $uomSettingData['receiving_uom_level_id']   = $uomSettingData['shipping_uom_level_id'];
                    $uomSettingData['inventory_uom_level_id']   = $uomSettingData['shipping_uom_level_id'];

                    unset(
                        $item['product_code'],
                        $item['description_1'],
                        $item['description_2'],
                        $item['short_descr'],
                        $item['our_list_price'],
                        $item['costs_1'],
                        $item['yes_no_fields_1'],
                        $item['serial_no_flag'],
                    );

                    $supplierSettingsInsertData[] = $supplierSetting;
                    $uomSettingInsertData[]       = $uomSettingData;
                }
            }
        }

        try {
            if (isset($supplierSettingsInsertData)) {
                SupplierSetting::insert($supplierSettingsInsertData);
            }

            if (isset($uomSettingInsertData)) {
                UomSetting::insert($uomSettingInsertData);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
