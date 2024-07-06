<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CartResource;
use App\Models\Cart;
use App\Models\Item;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function included\getDiscount;
use function included\getPrice;
use function included\sendResponse;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $total = 0;
        $carts = Cart::with(['item', 'offer'])->where('client_id', Auth::guard('api')->id())->get();
        if (count($carts) == 0)
            return sendResponse([], 'no data found', 0);
        foreach ($carts as $item) {
            if ($item->offer_id <> null) {
                $price = getPrice($item->offer, 'offer');
                $total += $price * $item->count;
            }
            if ($item->item_id <> null) {
                $price = getPrice($item->item);
                $discount = getDiscount($item->item);
                $total += ($discount > 0 ? $discount : $price) * $item->count;
            }
        }
        return response()->json([
            "items" => CartResource::collection($carts),//favorites
            "total_price" => $total
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => ['nullable', Rule::in(Item::all()->pluck('id'))],
            'offer_id' => ['nullable', Rule::in(Offer::all()->pluck('id'))],
            'count' => 'required|numeric|min:1',
        ]);
        if (count($validator->errors()) > 0)
            return sendResponse($validator->errors(), 'validation error', 0);


        $cart = Cart::where('client_id', '=', Auth::guard('api')->id())->get();

        if ($request->item_id & count($cart->where('item_id', $request->item_id)) > 0)
            return sendResponse([], 'you try to add same item to the cart', 0);
        if ($request->offer_id & count($cart->where('offer_id', $request->offer_id)) > 0)
            return sendResponse([], 'you try to add same offer to the cart', 0);

        $cart = Cart::create([
            'client_id' => Auth::guard('api')->id(),
            'item_id' => $request->item_id ?? null,
            'offer_id' => $request->offer_id ?? null,
            'is_offer' => $request->offer_id ? 1 : 0,
            'count' => $request->count,
        ]);
        return response()->json(CartResource::collection($cart = Cart::with(['item', 'offer'])->where('client_id', Auth::guard('api')->id())->get()));
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
        return request()->all();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($cart = Cart::find($id)) {
            $cart->delete();
            return sendResponse([], 'successful', 1);
        }
        return sendResponse([], 'not found', 0);
    }
}
