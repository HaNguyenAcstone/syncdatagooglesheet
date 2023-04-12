<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sheets;

class GoogleSpreadsheetController extends Controller
{
    public $dataPost;
    //
    public function addsheetdata()
    {
        // $results = DB::select('select p.id, p.post_date, p.post_status from post as p');
        $results = DB::table('post')->select('id', 'post_date', 'post_status')
        ->where('post_status', '!=', 'wc-pending')
        ->where('post_status', '!=', 'wc-trash')->get();

        return $this->dataPost;
        if(($this->dataPost ? $this->dataPost->count() : 0) == $results->count()) {
            return response()->json([
                'status' => 400,
                'message' => 'Nothing changes'
            ], 400);
        }

        $data = array_slice($results->toArray(), ($this->dataPost ? $this->dataPost->count() : 0), $results->count());
        $rows = [];

        foreach($data as $value) {
            $arrTemp = [];
            foreach((array)$value as $valueObj) {
                array_push($arrTemp, $valueObj);
            }

            array_push($rows, $arrTemp);
        }
        
        Sheets::spreadsheet('1Y40m28jPcaoYuunS9zIIr_zCfGGbBHa05i26hWUdpDs')->sheet('Trang tính1')->append($rows);
        dd('Data Added in Sheet');
        $this->dataPost = $results;
    }

    public function getPost() { 
        $results = DB::select('select p.id, p.post_date, p.post_status from post as p');

        $rows = [];
        foreach($results as $value) {
            # code...
            $arrTemp = [];
            foreach((array)$value as $valueObj) {
                array_push($arrTemp, $valueObj);
            }

            array_push($rows, $arrTemp);
        }

        return $rows;
    }

    public function getCustomer() {
        // $queryCustomer = DB::select(DB::raw('
        // select p.id, pm_sfn.meta_value as customer, oim_lt.meta_value as total_price,
        // GROUP_CONCAT(concat(oi.order_item_name, - (Qty: , oim_qty.meta_value, )) SEPARATOR, ) AS list_order
        // from post as p left join postmeta as pm_sfn on p.id = pm_sfn.post_id
        // left join order_items as oi on oi.order_id = p.id 
        // left join order_itemmeta as oim_qty on oi.order_item_id = oim_qty.order_item_id
        // left join order_itemmeta as oim_lt on oi.order_item_id = oim_lt.order_item_id
        // where pm_sfn.meta_key = _shipping_first_name AND (oim_qty.meta_key = _qty OR oim_lt.meta_key = _line_total)
        // and p.post_status != wc-cancelled and p.post_status != wc-trash AND oi.order_item_type = line_item
        // group by pm_sfn.meta_id;
        // '));
        // $results = DB::table('post as p')
        // ->select('p.id',
        //     DB::raw("GROUP_CONCAT(CONCAT(oi.order_item_name, '- (Qty: ', oim_qty.meta_value, ')') SEPARATOR ', ') AS list_order"))
        // ->leftJoin('postmeta as pm_sfn', 'p.id', '=', 'pm_sfn.post_id')
        // ->leftJoin('order_items as oi', 'oi.order_id', '=', 'p.id')
        // ->leftJoin('order_itemmeta as oim_qty', 'oi.order_item_id', '=', 'oim_qty.order_item_id')
        // ->leftJoin('order_itemmeta as oim_lt', 'oi.order_item_id', '=', 'oim_lt.order_item_id')
        // ->where('pm_sfn.meta_key', '_shipping_first_name')
        // ->where(function ($query) {
        //     $query->where('oim_qty.meta_key', '_qty')
        //         ->orWhere('oim_lt.meta_key', '_line_total');
        // })
        // ->where('p.post_status', '!=', 'wc-cancelled')
        // ->where('p.post_status', '!=', 'wc-trash')
        // ->where('oi.order_item_type', '=', 'line_item')
        // ->groupBy('p.id')
        // ->get();

        // $query = DB::select("select `p`.`id`, `pm_sfn`.`meta_value` as `customer`, `oim_lt`.`meta_value` as `total_price`, GROUP_CONCAT(concat(oi.order_item_name, '- (Qty: ', oim_qty.meta_value, ')') SEPARATOR ', ') AS `list_order` from `post` as `p` left join `postmeta` as `pm_sfn` on `p`.`id` = `pm_sfn`.`post_id` left join `order_items` as `oi` on `oi`.`order_id` = `p`.`id` left join `order_itemmeta` as `oim_qty` on `oi`.`order_item_id` = `oim_qty`.`order_item_id` left join `order_itemmeta` as `oim_lt` on `oi`.`order_item_id` = `oim_lt`.`order_item_id` where `pm_sfn`.`meta_key` = '_shipping_first_name' and (`oim_qty`.`meta_key` = '_qty' or `oim_lt`.`meta_key` = '_line_total') and `p`.`post_status` != 'wc-cancelled' and `p`.`post_status` != 'wc-trash' and `oi`.`order_item_type` = 'line_item' group by `p`.`id`;");

        // Execute the query and fetch results
        // $results = $query;
      //   return $results;
        // return response()->json([
        //     'status' => 200,
        //     'data' => $results
        // ], 200);
    }
}
