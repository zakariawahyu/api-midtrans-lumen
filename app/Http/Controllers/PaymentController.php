<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    public function __construct()
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = env("MIDTRANS_SERVER_KEY");
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = env("MIDTRANS_PRODUCTION");
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Payment::all();
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error",
            ], 404);
        }

        Log::info('Showing all payment');

        return response()->json([
            "message" => "Success get all data",
            "status" => "success",
            "data" => $data
        ], 200);
    }

    public function indexJoin()
    {
        $data = Payment::with(array('order'=>function($query){
            $query->select();
        }))->get();

        if(!$data) {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error",
            ], 404);
        }

        Log::info('Showing all payment');

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
            'payment_type' => 'required',
            'gross_amount' => 'required',
            'order_id' => 'required|exists:orders,id'
        ]);

        $data = new Payment();
        $data->payment_type = $request->input('payment_type');
        $data->gross_amount = $request->input('gross_amount');
        $data->order_id = $request->input('order_id');
        $data->transaction_id = 0;
        $data->transaction_time = "";
        $data->transaction_status = "created";
        $data->save();

        $orderitem = OrderItem::where('order_id', $data->order_id)->with(array('product'=>function($query){
            $query->select();
        }))->get();

        for ($i=0; $i < count($orderitem); $i++) {
            $array_item = [];
            $array_item['id'] = $orderitem[$i]['product']['id'];
            $array_item['price'] = $orderitem[$i]['product']['price'];
            $array_item['quantity'] = $orderitem[$i]['quantity'];
            $array_item['name'] = $orderitem[$i]['product']['name'];
            $item_details[] = $array_item;
        }

        $transaction_details = array(
            'gross_amount' => $data->gross_amount,
            'order_id' => "27082022"+$data->order_id,
        );

        $order = Order::find($data->order_id);
        $customer = Customer::find($order->user_id);

        $customer_details = array(
            'first_name' => $customer->full_name,
            'last_name' => $customer->username,
            'email' => $customer->email,
            'phone' => $customer->phone_number
        );

        $transaction = array(
            'payment_type' => $data->payment_type,
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
            'bank_transfer' => [
                'bank' => 'bca',
                'va_number' => '111111',
            ]
        );

        try {
            $midtrans = \Midtrans\CoreApi::charge($transaction);

            return response()->json([
                "message" => "Transaction added successfully",
                "status" => "success",
                "results" => $midtrans,
                "data" => [
                    "attributes" => $data
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "failed",
                "status" => false,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Payment::find($id);
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error",
            ], 404);
        }

        Log::info('Showing payment by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
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
        $data = $this->getById($id);
        $data = $data->data;
        $id_order = $data->order_id;

        $url = "https://api.sandbox.midtrans.com/v2/". $id_order. "/status";
        $curl = curl_init("$url");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . base64_encode(Config::$serverKey.':'),
            'Content-Type: application/json',
                'Accept: application/json',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $res = response()->json($response);

        return $this->saveUpdate($res, $id);
    }

    public function saveUpdate($data,$id)
    {
        $data = json_decode($data);
        $orders = Orders::find($id);
        $orders->transaction_time = $data->transaction_time;
        $orders->transaction_status = $data->transaction_status;
        $orders->transaction_id = $data->transaction_id;
        $orders->save();

        return response()->json([
            "message" => "Transaction updated successfully",
            "status" => "success"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Payment::find($id);
        if($data) {
            $data->delete();

            Log::info('Deleting payment by id');

            return response()->json([
                "message" => "Transaction deleted successfully",
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
