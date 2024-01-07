<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $total=0;
        $cart = CartResource::collection(Cart::with('item')->where('client_id',Auth::guard('api')->id())->get());
        foreach ($cart as $item){
            $total += $item->item->price * $item->count;
        }
        return response()->json([
            "items"=> CartResource::collection($cart),
            "total_price"=>$total
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            $cart = Cart::create([
                'client_id'=>Auth::guard('api')->id(),
                'item_id'=>$request->item_id,
                'count'=>$request->count,
            ]);
        return response()->json(CartResource::collection($cart = Cart::with('item')->where('client_id',Auth::guard('api')->id())->get()));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
