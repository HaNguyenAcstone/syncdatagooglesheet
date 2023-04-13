<?php

namespace App\Http\Controllers;

use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Revolution\Google\Sheets\Facades\Sheets as FacadesSheets;


class GoogleSpreadsheetController extends Controller
{
    private static $FILE_NAME = 'post.txt';
    private static $DISK_FOLDER = 'public';

    private static $ID_SHEET = '1Y40m28jPcaoYuunS9zIIr_zCfGGbBHa05i26hWUdpDs';
    private static $NAME_SHEET = 'Trang tính1';
    
    //
    public function addsheetdata()
    {
        // $results = DB::select('select p.id, p.post_date, p.post_status from post as p');
        $results = DB::table('post')->select('id', 'post_date', 'post_status')
        ->where('post_status', '!=', 'wc-pending')
        ->where('post_status', '!=', 'wc-trash')->get();

        $contents = Storage::disk(self::$DISK_FOLDER)->get(self::$FILE_NAME);

        if(($contents ? (int)$contents : 0) == $results->count()) {
            return response()->json([
                'status' => 400,
                'message' => 'Nothing changes'
            ], 400);
        }

        $data = array_slice($results->toArray(), ($contents ?  (int)$contents : 0), count($results->toArray()));
        $rows = [];

        // return response()->json([
        //     'status' => 400,
        //     'message' => $data
        // ], 400);

        foreach($data as $value) {
            $arrTemp = [];
            foreach((array)$value as $valueObj) {
                array_push($arrTemp, $valueObj ? $valueObj : 0);
            }

            array_push($rows, $arrTemp);
        }

        Storage::disk(self::$DISK_FOLDER)->put(self::$FILE_NAME, count($results->toArray()));

        FacadesSheets::spreadsheet(self::$ID_SHEET)->sheet(self::$NAME_SHEET)->append($rows);
        dd('Data Added in Sheet');
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

        $count = count($rows);

        $contents = Storage::disk('public')->get('post.txt');

        return $contents;
    }

    public function createPost(Request $request) {
        $now = new DateTimeImmutable();
        DB::table('post')->insert([
            'id' => $request->id,
            'post_author' => $request->post_author,
            'post_date' => date_format($now, 'Y-m-d H:i:s'),
            'post_date_gmt' => date_format($now, 'Y-m-d H:i:s'),
            'post_content' => '',
            'post_title' => '',
            'post_excerpt' => '',
            'post_status' => $request->post_status,
            'comment_status' => 'open',
            'ping_status' => 'closed',
            'post_password' => '',
            'post_name' => '',
            'to_ping' => '',
            'pinged' => '',
            'post_modified' => date_format($now, 'Y-m-d H:i:s'),
            'post_modified_gmt' => date_format($now, 'Y-m-d H:i:s'),
            'post_content_filtered' => '',
            'post_parent' => 0,
            'guid' => '',
            'menu_order' => 0,
            'post_type' => 'shop_order',
            'post_mime_type' => '',
            'comment_count' => 0,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Created Post Successfully.',
        ], 200);
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
