<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Product::all();
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found"
            ]);
        }

        Log::info('Showing all product');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
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
            'name' => 'required',
            'price' => 'required'
        ]);

        $data = new Product();
        $data->name = $request->input('name');
        $data->price = $request->input('price');
        $data->save();

        Log::info('Adding product');

        return response()->json([
            "message" => "Success Added",
            "status" => true,
            "data" => [
                "attributes" => $data
            ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Product::find($id);
        if(!$data) {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }

        Log::info('Showing product by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
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
            'name' => 'required',
            'price' => 'required'
        ]);

        $data = Product::find($id);
        if ($data) {
            $data->name = $request->input('name');
            $data->price = $request->input('price');
            $data->save();

            Log::info('Updating product by id');

            return response()->json([
                "message" => "Success Updated",
                "status" => true,
                "data" => [
                    "attributes" => $data
                ]
            ]);
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
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
        $data = Product::find($id);
        if($data) {
            $data->delete();

            Log::info('Deleting product by id');

            return response()->json([
                "message" => "Success Deleted",
                "status" => true,
                "data" => [
                    "attributes" => $data
                ]
            ]);
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }
    }
}
