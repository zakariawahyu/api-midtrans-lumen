<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = OrderItem::all();
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error",
            ], 404);
        }

        Log::info('Showing all order item');

        return response()->json([
            "message" => "Success get all data",
            "status" => "success",
            "data" => $data
        ], 200);
    }

    public function indexJoin()
    {
        $data = OrderItem::with(array('order'=>function($query){
            $query->select();
        }))->with(array('product'=>function($query){
            $query->select();
        }))->get();

        if(!$data) {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error",
            ], 404);
        }

        Log::info('Showing all order item');

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
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required'
        ]);

        $data = new OrderItem();
        $data->order_id = $request->input('order_id');
        $data->product_id = $request->input('product_id');
        $data->quantity = $request->input('quantity');
        $data->save();

        Log::info('Adding order item');

        return response()->json([
            "message" => "Success Added",
            "status" => "success",
            "data" => [
                "attributes" => $data
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
        $data = OrderItem::find($id);
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error",
            ], 404);
        }

        Log::info('Showing order item by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => "success",
            "data" => $data
        ], 200);
    }

    public function showJoin($id)
    {
        $findId = OrderItem::find($id);
        $data = OrderItem::where('id', $id)->with(array('order'=>function($query){
            $query->select();
        }))->with(array('product'=>function($query){
            $query->select();
        }))->get();
        if(!$findId) {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error",
            ], 404);
        }

        Log::info('Showing order item with post comment by id');

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
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required'
        ]);

        $data = OrderItem::find($id);
        if ($data) {
            $data->order_id = $request->input('order_id');
            $data->product_id = $request->input('product_id');
            $data->quantity = $request->input('quantity');
            $data->save();

            Log::info('Updating order item by id');

            return response()->json([
                "message" => "Success Updated",
                "status" => "success",
                "data" => [
                    "attributes" => $data
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
        $data = OrderItem::find($id);
        if($data) {
            $data->delete();

            Log::info('Deleting order item by id');

            return response()->json([
                "message" => "Success Deleted",
                "status" => "success",
                "data" => [
                    "attributes" => $data
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
