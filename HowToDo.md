Work with laravel 11 rest api

# Laravel 11 API CRUD from Scratch

# -stap1: create a model and migration

EX1: products Migration
Schema::create('products', function (Blueprint $table) {
$table->id();
$table->string('name');
$table->string('desc');
$table->integer('qty');
$table->decimal('price');
$table->timestamps();
});

EX1: Product Model
protected $fillable = [
'name',
'desc',
'qty',
'price',
];
==============================
stap2: create a controller and resource
==============================
EX2: ProductController
==============================

<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

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

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

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

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['massage'=>'Product was deleted'],200);
    }
}
===========================

EX2: ProductResource
===========================
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'desc' => $this->desc,
            'qty' => $this->qty,
            'price' => $this->price,
        ];
    }
}


==============================
stap3: create a route api
==============================
Route::apiResource('product',ProductController::class);


==============================
stap3: Rendering Exceptions bootstrap/app.php
==============================

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
 
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (NotFoundHttpException $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'message' => 'Record not found.'
            ], 404);
        }
    });
})
