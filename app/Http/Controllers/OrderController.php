<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Order::with(array('orderitem'=>function($query){
            $query->select();
        }))->get();

        if(!$data) {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error",
            ], 404);
        }

        Log::info('Showing all order');

        return response()->json([
            "message" => "Success get all data",
            "status" => "success",
            "data" => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:customers,id'
        ]);

        $order = new Order();
        $order->user_id = $request->input('user_id');
        $order->status = "created";
        $order->save();

        $order_detail = $request->input('order_detail');

        for ($i=0; $i < count($order_detail); $i++) {
            $order_item = new OrderItem();
            $order_item->order_id = $order->id;
            $order_item->product_id = $request->input('order_detail.'.$i.'.product_id');
            $order_item->quantity = $request->input('order_detail.'.$i.'.quantity');
            $order->orderitem()->save($order_item);
        }

        Log::info('Adding order');

        return response()->json([
            "message" => "Success Added",
            "status" => "success",
            "data" => [
                "attributes" => $order
            ]
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $findId = Order::find($id);
        $data = Order::where('id', $id)->with(array('orderitem'=>function($query){
            $query->select();
        }))->get();

        if(!$findId) {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error",
            ], 404);
        }

        Log::info('Showing order with post comment by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => "success",
            "data" => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:customers,id'
        ]);

        $order = Order::find($id);
        if ($order) {
            $order->user_id = $request->input('user_id');
            $order->status = "created";
            $order->save();

            $order_detail = $request->input('order_detail');

            for ($i=0; $i < count($order_detail); $i++) {
                $order_item = OrderItem::where('order_id', $id)->first();
                $order_item->product_id = $request->input('order_detail.'.$i.'.product_id');
                $order_item->quantity = $request->input('order_detail.'.$i.'.quantity');
                $order->orderitem()->save($order_item);
            }

            Log::info('Updating order by id');

            return response()->json([
                "message" => "Success Updated",
                "status" => "success",
                "data" => [
                    "attributes" => $order
                ]
            ], 200);
        }else {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error",
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        if($order) {
            $order->delete();

            $order_item = OrderItem::where('order_id', $id)->delete();

            Log::info('Deleting order by id');

            return response()->json([
                "message" => "Success Deleted",
                "status" => true,
                "data" => [
                    "attributes" => $order
                ]
            ], 200);
        }else {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error",
            ], 404);
        }
    }
}
