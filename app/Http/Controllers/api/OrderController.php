<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\OrderModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function getOrders(Request $request)
    {
        $search = [
            'from' => $request->from ? $request->from : null,
            'to' => $request->to ? $request->to : null,
            'username' => $request->username ? $request->username : null,
            'status' => $request->status ? $request->status : null,
        ];
        $model = new OrderModel();
        $orders = $model->getOrders($search);
        return response()->json($orders, 200);
    }

    public function updateStatusOrder(Request $request)
    {
        try {
            $params = [
                'id' => $request->id,
                'status_id' => $request->status_id
            ];
            $model = new OrderModel();
            $model->updateStatusOrder($params);
            return response()->json(['success' => "Update status order success"], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => "Update status order failed, Please try again!"], 400);
        }
    }
    public function updateOrder(Request $request)
    {
    }

    public function detailOrder(Request $request)
    {
        $model = new OrderModel();
        $params = explode(',', rtrim($request->id, ','));
        $data = [];
        if (count($params) > 1) {
            for ($i = 0; $i < count($params); $i++) {
                $item = DB::table('order_products')->where('order_products.order_id', '=', $params[$i])->get();
                foreach ($item as $key) {
                    array_push($data, $key);   
                }
            }
            return response()->json($data, Response::HTTP_OK);
        } else {

            $params = [
                'id' => $request->id
            ];
            $data = $model->detailOrder($params);
            return response()->json($data, Response::HTTP_OK);
        }
    }
    public function getDetailOrderUpdate(Request $request)
    {
        try {
            $params = [
                'id' => $request->id
            ];
            $model = new OrderModel();
            $data = $model->getDetailOrderUpdate($params);
            return response()->json($data, Response::HTTP_OK);
        } catch (\Throwable $th) {
        }
    }
    public function updateOrderPacking(Request $request)
    {
        try {
            $model = new OrderModel();
            $model->updatePacketOrder($request->all());
            return response()->json(['success' => "Update status order success"], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => "Update status order failed, Please try again!"], 400);
        }
    }
}
