<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::get();
        if($products->count()>0)
        {
            return ProductResource::collection($products);
        }else{
            return response()->json(['massage'=>'No record available'],200);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'name'=> 'required|string|max:100',
            'desc'=> 'required|string|max:255',
            'qty'=> 'required|integer',
            'price'=> 'required'
        ]);
        if($validator->fails()){
            return response()->json(
                [
                    'massage' => 'All fields is require',
                    'error' => $validator->messages()
                ],
                422
            );
        }else{
            $product = Product::create([
                'name' => $request->name,
                'desc' => $request->desc,
                'qty' => $request->qty,
                'price' => $request->price,
            ]);

            return response()->json(['massage'=>'Product was created','data' => new ProductResource($product)],200);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(),
        [
            'name'=> 'required|string|max:100',
            'desc'=> 'required|string|max:255',
            'qty'=> 'required|integer',
            'price'=> 'required'
        ]);
        if($validator->fails()){
            return response()->json(
                [
                    'massage' => 'All fields is require',
                    'error' => $validator->messages()
                ],
                422
            );
        }else{
            $product->update([
                'name' => $request->name,
                'desc' => $request->desc,
                'qty' => $request->qty,
                'price' => $request->price,
            ]);

            return response()->json(['massage'=>'Product was updated','data' => new ProductResource($product)],200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['massage'=>'Product was deleted'],200);
    }
}
