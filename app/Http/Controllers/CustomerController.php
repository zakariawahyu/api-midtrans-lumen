<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Customer::all();
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error",
            ], 404);
        }

        Log::info('Showing all customer');

        return response()->json([
            "message" => "Success get all data",
            "status" => true,
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
            'full_name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
        ]);

        $data = new Customer();
        $data->full_name = $request->input('full_name');
        $data->username = $request->input('username');
        $data->email = $request->input('email');
        $data->phone_number = $request->input('phone_number');
        $data->save();

        Log::info('Adding customer');

        return response()->json([
            "message" => "Success Added",
            "status" => true,
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
        $data = Customer::find($id);
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found",
                "status" => "error"
            ], 404);
        }

        Log::info('Showing customer by id');

        return response()->json([
            "message" => "Success get data",
            "status" => "success",
            "data" => $data
        ]);
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
            'full_name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
        ]);

        $data = Customer::find($id);
        if ($data) {
            $data->full_name = $request->input('full_name');
            $data->username = $request->input('username');
            $data->email = $request->input('email');
            $data->phone_number = $request->input('phone_number');
            $data->save();

            Log::info('Updating customer by id');

            return response()->json([
                "message" => "Success Updated",
                "status" => "succcess",
                "data" => [
                    "attributes" => $data
                ]
            ], 200);
        } else {
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
        $data = Customer::find($id);
        if($data) {
            $data->delete();

            Log::info('Deleting customer by id');

            $results = array(
                "data" => array("attributes")
            );

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
