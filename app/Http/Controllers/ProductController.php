<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        // $products = Product::get();
        //search
        $query = Product::query()->latest('name');
        $keyword = $request->input('name');
        if($keyword){
            $query->where('name','like',"%{$keyword}%");
        }
        $products = $query->paginate(5);
        //end search
        //check product isEmpty or not
        if(!$products->isEmpty())
        {
            return ProductResource::collection($products);
        }else{
            return response()->json(['massage'=>'No record available'],200);
        }
    }

    public function store(Request $request)
    {
        //authorize token user must login before
        $token = $request->bearerToken();
        if(!$token){
            return response()->json(
                [
                    'massage' => 'Token not exists, please login first',
                ],
                401
            );
        }
        //validation
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
            //create
            $product = Product::create([
                'name' => $request->name,
                'desc' => $request->desc,
                'qty' => $request->qty,
                'price' => $request->price,
            ]);

            return response()->json(['massage'=>'Product was created','data' => new ProductResource($product)],200);
        }

    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(Request $request, Product $product)
    {
        //authorize token user must login before
        $token = $request->bearerToken();
        if(!$token){
            return response()->json(
                [
                    'massage' => 'Token not exists, please login first',
                ],
                401
            );
        }
        //validation
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
            //update
            $product->update([
                'name' => $request->name,
                'desc' => $request->desc,
                'qty' => $request->qty,
                'price' => $request->price,
            ]);

            return response()->json(['massage'=>'Product was updated','data' => new ProductResource($product)],200);
        }
    }

    public function destroy(Request $request,Product $product)
    {
        //authorize token user must login before
        $token = $request->bearerToken();
        if(!$token){
            return response()->json(
                [
                    'massage' => 'Token not exists, please login first',
                ],
                401
            );
        }
        //delete
        $product->delete();
        return response()->json(['massage'=>'Product was deleted'],200);
    }


}
