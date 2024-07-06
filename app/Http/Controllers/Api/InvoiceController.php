<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CartResource;
use App\Http\Resources\Api\InvoiceResource;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Item;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function included\getDiscount;
use function included\getPrice;
use function included\sendResponse;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with(['items','offers'])->where('client_id', Auth::guard('api')->id())->orderBy('id','desc')->get();
        if (count($invoices) > 0)
            return sendResponse(InvoiceResource::collection($invoices), 'successful', 1);
        return sendResponse([], 'sorry no data found', 1);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if ($invoice = Invoice::where([
            ['id',$id],
            ['client_id',auth()->id()],
        ])->first())
            return sendResponse($invoice, 'successful', 1);
        return sendResponse([], 'sorry no data found', 1);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => [$request->offer_id ? 'nullable' : 'required', Rule::in(Item::all()->pluck('id'))],
            'offer_id' => [$request->item_id ? 'nullable' : 'required', Rule::in(Offer::all()->pluck('id'))],
            'address_id' => ['required', Rule::in(Address::all()->pluck('id'))],
            'count' => 'required|numeric',
            'payment_type' => 'required|in:cash,visa',
        ]);
        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }
        $item = Item::find($request->item_id); //get item details from database
        $offer = Offer::find($request->offer_id);//get offer details from database
        $price = ($item ? ($item->discount ?? $item->price) * $request->count : $offer->price * $request->count); //calculate the total price of item or offer

        if (!$client = Client::find(Auth::guard('api')->id()))
            return sendResponse([], 'try again', 0);

        DB::beginTransaction();
        $invoice = $client->invoices()->create([
            'price' => $price,
            'payment_type' => $request->payment_type,
            'address_id' => $request->address_id,
            'status' => 'pending',
//            'amount' => $request->amount,
        ]);

        if ($item) {
            $invoice->items()->attach($request->item_id, [
                'count' => $request->count,
                'price' => getDiscount($item) > 0 ?getDiscount($item) : getPrice($item) ,
            ]);
        } else {
            $invoice->offers()->attach($request->offer_id, [
                'count' => $request->count,
                'price' => getPrice($offer,'offer'),
            ]);
        }

        $invoice->invoiceStatuses()->create([
            'status' => 'pending' //'pending','underPrepare','onTheWay','delivery'
        ]);

        DB::commit();

        if (!$invoice)
            return sendResponse([], 'try again', 0);


        return sendResponse(InvoiceResource::make($invoice), 'successful', 1);
    }

    public function update(Request $request, $id)
    {

        if (!$invoice = Invoice::find($id))
            return sendResponse([], 'this invoice not found , try again', 0);

        $validator = Validator::make($request->all(), [
//            'phone' => 'required',
            'street_name' => 'required',
            'build_name' => 'required',
            'city' => 'required',
            'government' => 'required',
            'landmark' => 'required',

        ]);

        if (count($validator->errors()) > 0) {
            return sendResponse($validator->errors(), 'validation error', 0);
        }

        $update = $invoice->update([
            'phone' => $request->phone,
            'street_name' => $request->street_name,
            'build_name' => $request->build_name,
            'city' => $request->city,
            'government' => $request->government,
            'landmark' => $request->landmark,
        ]);
        if (!$update)
            return sendResponse([], 'try again', 0);
        return sendResponse(InvoiceResource::make($invoice), 'successful', 1);

    }

    public function destroy($id)
    {
        if (!$invoice = Invoice::where([
            ['id',$id],
            ['client_id',auth()->id()],
            ['status','pending'],
            ])->first())
            return sendResponse([], 'this invoice is incorrect', 0);
        if ($invoice->delete())
            return sendResponse([], 'delete successfully', 1);

    }

    /**
     * create invoice from cart
     *
     */
    public function fromCart(Request $request)
    {

        if (!$client = Client::with(['carts', 'addresses'])->find(Auth::guard('api')->id()))
            return sendResponse([], 'try again', 0);

        $total = 0;
        $cart = $client->carts;
//        return CartResource::collection($cart);
        if (!count($cart) > 0)
            return sendResponse([], 'no data found', 0);
        foreach ($cart as $item) {
            if ($item->offer_id) {
                $price = getPrice($item->offer,'offer');
                $total += ($price * $item->count);

            }else {
                $price = getPrice($item->item);
                $discount = getDiscount($item->item);
                $total += ($discount > 0 ? $discount : $price) * $item->count;
            }
        }

        DB::beginTransaction();

        try {
            $invoice = $client->invoices()->create([
                'price' => $total,
                'is_dollar' => request()->ipinfo->country == "EG" ?0:1,
                'payment_type' => $request->payment_type,
                'address_id' => $request->address_id,
            ]);

            foreach ($cart as $item) {
                if ($item->offer_id) {
                    $price = getPrice($item->offer,'offer');
                    $invoice->offers()->attach($item->offer_id, [
                        'count' => $item->count,
                        'price' => $price,
                    ]);
                } elseif ($item->item_id) {
                    $price = getPrice($item->item);
                    $discount = getDiscount($item->item);
                    $invoice->items()->attach($item->item_id, [
                        'count' => $item->count,
                        'price' => $discount > 0 ? $discount : $price,
                    ]);
                }
            }

            Cart::where('client_id', auth('api')->id())->delete();

            $invoice->invoiceStatuses()->create([
                'status' => 'pending' //'pending','underPrepare','onTheWay','delivery'
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }


//        DB::beginTransaction();
//        $invoice = $client->invoices()->create([
//            'price' => $total,
//            'payment_type' => $request->payment_type,
//            'address_id' => $request->address_id,
////            'amount' => $request->amount,
//        ]);
//
//        foreach ($cart as $item) {
//            if ($item->offer_id) {
//                $invoice->offers()->attach($item->offer_id, [
//                    'count' => $item->count,
//                    'price' => $item->offer->price,
//                ]);
//            } else
//                $invoice->items()->attach($item->item_id, [
//                    'count' => $item->count,
//                    'price' => $item->item->discount,
//                ]);
//        }
//
//        Cart::where('client_id',auth('api')->id())->delete();
//        $invoice->invoiceStatuses()->create(
//            [
//                'status'=>'padding' //'padding','underPrepare','onTheWay','delivery'
//            ]
//        );
//        DB::commit();
        return sendResponse('', 'successful', 1);

    }
}
